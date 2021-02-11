<?php
namespace frontend\controllers;

use common\models\BookAuthor;
use common\models\BookCategory;
use common\models\BookPicture;
use common\models\BookSearch;
use common\models\Book;
use common\models\Category;
use common\models\Status;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use common\models\Authors;
use common\models\AuthorsSearch;
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
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
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
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
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

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }


    public function actionParser () {
        $model = new Book();

        if ($model->load(Yii::$app->request->post()) AND isset($_POST['parser-button']) AND !empty($_POST['parser-button'])) {

            // get data from url
            $j = @file_get_contents($model->parserSourceAddress);

            // transform data to assoc array
            $data = json_decode($j, true);

            $all_books_count = count($data);
            $new_books_count = 0;
            $new_categories = 0;
            $new_authors = 0;

            foreach($data as $value){
                // fill table book
                $newBook = new Book();
                $newBook->title = $value['title'];
                $newBook->isbn = $value['isbn'];
                $newBook->published_date = date('Y-m-d H:i:s', strtotime($value['publishedDate']['$date']));
                $newBook->short_description = $value['shortDescription'];
                $newBook->long_description = $value['longDescription'];
                $newBook->page_count = $value['pageCount'];
                $newBook->thumbnail_url = $value['thumbnailUrl'];


                if (!empty($value['status'])){
                    // search exist status
                    $status = Status::find()->andWhere(['name'=>$value['status']])->one();
                    // if not exist, save new status and get his id
                    if(empty($status)){
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
                if(empty($newBook->status_id)){
                    $newBook->status_id = 1;
                }

                // check existing book in database
                $existBook = Book::find()->andWhere([
                    'isbn' => $newBook->isbn,
                    'title' => $newBook->title,
                    'published_date' => $newBook->published_date
                ])->all();

                // if book is not exist yet
                if(empty($existBook)){
                    set_time_limit(30);
                    $newBook->save(false);
                    $new_books_count++;

                    foreach($value['authors'] as $author_name){
                        $author = Authors::find()->andWhere(['name'=>$author_name])->one();
                        if(!empty($author)){
                            $book_author = new BookAuthor();
                            $book_author->book_id = $newBook->id;
                            $book_author->author_id = $author->id;
                            $book_author->save(false);
                        }else {
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

                    foreach($value['categories'] as $category_name){
                        $category = Category::find()->andWhere(['name'=>$category_name])->one();
                        if(!empty($category)){
                            $book_category = new BookCategory();
                            $book_category->book_id = $newBook->id;
                            $book_category->category_id = $category->id;
                            $book_category->save(false);
                        }else {
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
                    if(!empty($url) && $url != ''){
                        // set path
                        $path = '../book_pictures/isbn_'.$newBook->isbn;
                        // check directory exist, if not make new directory
                        if(!file_exists('../book_pictures')){
                            mkdir('../book_pictures', 0777, true);
                        }
                        if(!file_exists($path)){
                            mkdir($path, 0777, true);
                        }

                        // if good response from server
                        if($picture_file = file_get_contents($url)){

                            // save file to the directory
                            file_put_contents($path.'/'.$newBook->isbn.'.jpg', $picture_file);
                            $book_picture = new BookPicture();
                            $book_picture->book_id = $newBook->id;
                            $book_picture->picture_file_path = $path.'/'.$newBook->isbn.'.jpg';
                            $book_picture->save(false);
                        } else {
                            echo "Cannot read file ".$url." from server";
                            echo "<br><br>";
                        }

                    }

                }

            }

            Yii::$app->session->setFlash('success', 'Парсинг закончен, всего книг в файле '
                .$all_books_count.', новых записанных в базу '.$new_books_count.', новых категорий '.
                $new_categories.', новых авторов '.$new_authors
            );
            return $this->render ('parser',
                ['model'=>$model]);


        }

        return $this->render ('parser',
            ['model'=>$model]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user = $model->verifyEmail()) {
            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                return $this->goHome();
            }
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }

    /**
     * Displays a single Authors model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
            $model = Book::findOne($id);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Authors model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Authors the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Book::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
