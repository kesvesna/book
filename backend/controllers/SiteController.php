<?php

namespace backend\controllers;

use common\models\Authors;
use common\models\BookAuthor;
use common\models\BookCategory;
use common\models\BookPicture;
use common\models\BookSearch;
use common\models\Book;
use common\models\Category;
use common\models\Status;
use common\models\User;
use Yii;
use yii\debug\models\search\Log;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use yii\web\NotFoundHttpException;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'index', 'parser',
                            'view', 'delete', 'update', 'create'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],

                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {

        $searchModel = new BookSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            return $this->goBack();

        } else {

            $model->password = '';
            $this->layout = 'blank';
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect('login');
    }


    public function actionParser()
    {

        $model = new Book();

        if ($model->load(Yii::$app->request->post())
            AND isset($_POST['parser-button'])
            AND !empty($_POST['parser-button'])) {

            // get data from url
            $j = @file_get_contents($model->parserSourceAddress);

            // transform data to assoc array
            $data = json_decode($j, true);

            $all_books_count = count($data);
            $new_books_count = 0;
            $new_categories = 0;
            $new_authors = 0;

            foreach ($data as $value) {

                // check existing book in database
                $existBook = Book::find()->andWhere([
                    'isbn' => $value['isbn'],
                    'title' => $value['title'],
                    'published_date' => date('Y-m-d H:i:s', strtotime($value['publishedDate']['$date']))
                ])->all();

                // if book is not exist yet
                if (empty($existBook)) {

                    // fill table book
                    $newBook = new Book();
                    $newBook->title = $value['title'];
                    $newBook->isbn = $value['isbn'];
                    $newBook->published_date = date('Y-m-d H:i:s', strtotime($value['publishedDate']['$date']));
                    $newBook->short_description = $value['shortDescription'];
                    $newBook->long_description = $value['longDescription'];
                    $newBook->page_count = $value['pageCount'];
                    $newBook->thumbnail_url = $value['thumbnailUrl'];


                    if (!empty($value['status'])) {
                        // search exist status
                        $status = Status::find()->andWhere(['name' => $value['status']])->one();
                        // if not exist, save new status and get his id
                        if (empty($status)) {
                            $newStatus = new Status();
                            $newStatus->name = $value['status'];
                            $newStatus->save(false);
                            $newBook->status_id = $newStatus->id;
                            // else write exist status id in book id
                        } else {
                            $newBook->status_id = $status->id;
                        }
                    }

                    // if something wrong, book get default status = 1
                    if (empty($newBook->status_id)) {
                        $newBook->status_id = 1;
                    }

                    set_time_limit(30);
                    $newBook->save(false);
                    $new_books_count++;

                    foreach ($value['authors'] as $author_name) {
                        $author = Authors::find()->andWhere(['name' => $author_name])->one();
                        if (!empty($author)) {
                            $book_author = new BookAuthor();
                            $book_author->book_id = $newBook->id;
                            $book_author->author_id = $author->id;
                            $book_author->save(false);
                        } else {
                            $author = new Authors();
                            $author->name = $author_name;
                            $author->save(false);
                            $new_authors++;

                            $book_author = new BookAuthor();
                            $book_author->author_id = $author->id;
                            $book_author->book_id = $newBook->id;
                            $book_author->save(false);
                        }

                    }

                    foreach ($value['categories'] as $category_name) {
                        $category = Category::find()->andWhere(['name' => $category_name])->one();
                        if (!empty($category)) {
                            $book_category = new BookCategory();
                            $book_category->book_id = $newBook->id;
                            $book_category->category_id = $category->id;
                            $book_category->save(false);
                        } else {
                            $category = new Category();
                            $category->name = $category_name;
                            $category->save(false);
                            $new_categories++;

                            $book_category = new BookCategory();
                            $book_category->book_id = $newBook->id;
                            $book_category->category_id = $category->id;
                            $book_category->save(false);
                        }

                    }

                    // get picture file
                    $url = $newBook->thumbnail_url;
                    if (!empty($url) && $url != '') {
                        // set path
                        $path = '../../frontend/book_pictures/isbn_' . $newBook->isbn;
                        // check directory exist, if not make new directory
                        if (!file_exists('../../frontend/book_pictures')) {
                            mkdir('../../frontend/book_pictures', 0777, true);
                        }
                        if (!file_exists($path)) {
                            mkdir($path, 0777, true);
                        }


                        $ch = curl_init();

                        curl_setopt($ch, CURLOPT_HEADER, 0);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_URL, $url);

                        // Проверяем наличие ошибок
                        if (!curl_errno($ch)) {
                            $picture_file = curl_exec($ch);
                            //$picture_file = file_get_contents($url);
                            curl_close($ch);
                            file_put_contents($path . '/' . $newBook->isbn . '.jpg', $picture_file);
                            $book_picture = new BookPicture();
                            $book_picture->book_id = $newBook->id;
                            $book_picture->picture_file_path = $path . '/' . $newBook->isbn . '.jpg';
                            $book_picture->save(false);
                        } else {
                            echo "Cannot read file " . $url . " from server";
                            echo "<br><br>";
                        }


                    }

                }

            }

            Yii::$app->session->setFlash('success', 'Парсинг закончен, всего книг в файле '
                . $all_books_count . ', новых записанных в базу ' . $new_books_count . ', новых категорий ' .
                $new_categories . ', новых авторов ' . $new_authors
            );
            return $this->render('parser',
                ['model' => $model]);


        }

        return $this->render('parser',
            ['model' => $model]);
    }


    /**
     * Displays a single Authors model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $user = User::findOne(Yii::$app->user->id);
        $admin = $user->admin;
        $model = Book::findOne($id);
        return $this->render('view', [
            'model' => $model,
            'admin' => $admin,
        ]);
    }


    /**
     * Creates a new Book model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Book();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    /**
     * Deletes an existing Book model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = Book::findOne($id);
        $model->delete();

        return $this->redirect(['index']);
    }


    /**
     * Updates an existing Book model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = Book::findOne($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
}
