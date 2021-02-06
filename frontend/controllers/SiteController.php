<?php
namespace frontend\controllers;

use frontend\models\BonusInfo;
use frontend\models\BonusSource;
use frontend\models\ItemSource;
use frontend\models\MoneySource;
use frontend\models\PresentCash;
use frontend\models\PresentItems;
use frontend\models\PresentStrategy;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\User;
use frontend\models\UserConfig;
use frontend\models\UserInfo;
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

    // *** SlotegratorTest *****************************************************************************************


    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect('/site/play?action=start');
        }
        return $this->render('index');
    }

    public function actionPlay($action = '', $int = 0)
    {
        if (Yii::$app->user->isGuest) return $this->redirect('/site/index');

            $minLevel = 1;
            $maxLevel = 100;
            $courseConvertation = 2;
            $result = false;

            if($action == 'refresh'){
                $this->start();
            }elseif($action == 'refuse') {
                if(ItemSource::refundLast(Yii::$app->user->id))
                    Yii::$app->session->setFlash('success', "Вы отказались от приза.");
            }elseif($action == 'convert' || $int) {
                if(MoneySource::convertToBonus($int, Yii::$app->user->id, $courseConvertation))
                    Yii::$app->session->setFlash('success', "Деньги конвертированны в бонусы.");
            }elseif($action == 'start'){
                Yii::$app->session->setFlash('success', "Нажмите с нажатия кнопки START.");
            }else{
                $dependency = [ BonusSource::class, ItemSource::class, MoneySource::class ];
                Yii::$container->set(BonusSource::class, [],['int' => rand($minLevel,$maxLevel), 'uid' => Yii::$app->user->id]);
                Yii::$container->set(ItemSource::class, [],['uid' => Yii::$app->user->id]);
                Yii::$container->set(MoneySource::class, [],['int' => rand($minLevel,$maxLevel), 'uid' => Yii::$app->user->id, 'courseConvertation' => $courseConvertation
                ]);
                do{
                    Yii::$container->set('random', $dependency[array_rand($dependency)]);
                    $random = Yii::$container->get('random');
                    $result = $random->getPresent();
                }while(!$result);
            }
            $bonusInfo = BonusInfo::run();
            $userInfo = UserInfo::run(Yii::$app->user->id);

            return $this->render('play',['result'=>$result, 'bonusInfo'=>$bonusInfo, 'userInfo'=>$userInfo, 'uid'=>Yii::$app->user->id]);
    }

    public function start()
    {
        UserConfig::deleteAll();
        $userConfig = new UserConfig();
        $userConfig->uid = Yii::$app->user->id;
        $userConfig->config = '{"cash":0, "bonus":0, "item":[]}';
        $userConfig->save();

        PresentCash::deleteAll();
        $presentCash = new PresentCash();
        $presentCash->name = 'us';
        $presentCash->count = 1000;
        $presentCash->save();

        PresentItems::deleteAll();
        $arrItems = [
            'Часы'=>5,
            'Телефон'=>5,
            'Планшет'=>5,
            'Ручка'=>5,
        ];

        foreach ($arrItems as $key=>$value){
            $presentCash = new PresentItems();
            $presentCash->name = $key;
            $presentCash->count = $value;
            $presentCash->save();
        }
        Yii::$app->session->setFlash('success', "Призовой фонд обновлен. Баланс пользователя очищен");
//        return $this->redirect('/site/play');
    }

    public function actionSendmoney($uid = 0)
    {
        if($uid){
           return 'Запрос на вывод средств на карту принят!';
        }
        // Ставим пометку в профиле о запросе вывода средств
        // По комманде cron находим баланс очищаем и отправляем у платежный пул
        // Ответ логируем и уведомляем клиента

    }

    // ****************************************************************************************************************
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
}
