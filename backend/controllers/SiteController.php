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
use backend\models\Parser;
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

        if (Yii::$app->user->isGuest) {
            return $this->redirect('site/login');
        }

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
        $model = new Parser();

        if ($model->load(Yii::$app->request->post()) && !empty($_POST['Parser']['parserSourceAddress'])) {

            $data = $model->getFileContent();

            $all_books_count = count($data);
            $new_books_count = 0;
            $new_categories = 0;
            $new_authors = 0;

            foreach ($data as $value) {

                if (Book::notExist($value)) {

                    // transform date for book model to view 0000-00-00 00:00:00
                    $value['publishedDate']['$date'] = date('Y-m-d H:i:s', strtotime($value['publishedDate']['$date']));

                    $newBook = new Book();
                    $newBook->fill($value);

                    if (!empty($value['status'])) {

                        $newBook->status_id = Status::getStatusId($value['status']);
                    }

                    $newBook->save();

                    $new_books_count++;

                    if(!empty($value['authors'])){

                        foreach ($value['authors'] as $author_name) {

                                $author_id = Authors::getAuthorId($author_name);

                                if(BookAuthor::notExist($newBook->id, $author_id)){

                                    $book_author = new BookAuthor();
                                    $book_author->fill($newBook->id, $author_id);
                                    $book_author->save();
                                }
                        }
                    }

                    if(!empty($value['categories'])){

                        foreach ($value['categories'] as $category_name) {

                            $category_id = Category::getCategoryId($category_name);

                            if(BookCategory::notExist($newBook->id, $category_id)){

                                $book_category = new BookCategory();
                                $book_category->fill($newBook->id, $category_id);
                                $book_category->save();
                            }
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

                        set_time_limit(60);

                        // Проверяем наличие ошибок
                        if (!curl_errno($ch)) {

                            $picture_file = curl_exec($ch);
                            //$picture_file = file_get_contents($url);
                            curl_close($ch);
                            file_put_contents($path . '/' . $newBook->isbn . '.jpg', $picture_file);

                            $book_picture = new BookPicture();
                            $book_picture->fill($newBook->id, $path.'/'.$newBook->isbn.'jpg');
                            $book_picture->save();

                        } else {

                            echo "Cannot read file " . $url . " from server";
                            echo "<br><br>";
                        }
                    }
                }

                Yii::$app->session->setFlash('success',
                    'Парсинг закончен, всего книг в файле ' . $all_books_count
                    . ', новых записанных в базу ' . $new_books_count
                    . ', новых категорий ' . $new_categories
                    . ', новых авторов ' . $new_authors
                );
            }

            return $this->refresh();
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
