<?php

namespace frontend\controllers;

use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\captcha\CaptchaAction;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\web\ErrorAction;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends BaseSeoController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
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
                'class' => VerbFilter::class,
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
                'class' => ErrorAction::class,
            ],
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex(): string
    {
        $this->setHomeSeoData();
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Спасибо, что обратились к нам. Мы ответим вам как можно скорее.');
            } else {
                Yii::$app->session->setFlash('error', 'При отправке вашего сообщения произошла ошибка.');
            }

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionAbout(): string
    {
        $this->setPageSeoData('О нас', 'about');
        return $this->render('about');
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            $user = $model->signup();
            if ($user) {
                Yii::$app->user->login($user);
                Yii::$app->session->setFlash('success', 'Добро пожаловать! Вы успешно зарегистрированы.');
                return $this->goHome();
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Проверьте свою электронную почту для получения дальнейших инструкций.');

                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Извините, мы не можем сбросить пароль для предоставленного адреса электронной почты.');
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * @throws BadRequestHttpException
     */
    public function actionResetPassword(string $token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Новый пароль сохранен.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * @throws BadRequestHttpException
     */
    public function actionVerifyEmail(string $token) :Response
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($model->verifyEmail()) {
            Yii::$app->session->setFlash('success', 'Ваш адрес электронной почты подтвержден!');
            return $this->goHome();
        }

        Yii::$app->session->setFlash('error', 'К сожалению, мы не можем проверить вашу учетную запись с помощью предоставленного токена.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Проверьте свою электронную почту для получения дальнейших инструкций.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'К сожалению, мы не можем повторно отправить письмо с подтверждением для указанного адреса электронной почты.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }

    /**
     * Displays delivery page.
     */
    public function actionDelivery(): string
    {
        $this->setPageSeoData('О доставке', 'delivery');
        return $this->render('delivery');
    }

    /**
     * Displays cooperation page.
     */
    public function actionCooperation(): string
    {
        $this->setPageSeoData('Сотрудничество', 'cooperation');
        return $this->render('cooperation');
    }

    /**
     * Displays contacts page.
     */
    public function actionContacts(): string
    {
        $this->setPageSeoData('Контакты', 'contacts');
        return $this->render('contacts');
    }

    /**
     * Displays privacy policy page.
     */
    public function actionPrivacyPolicy(): string
    {
        $this->setPageSeoData('Политика конфиденциальности', 'privacy-policy');
        return $this->render('privacy-policy');
    }

    /**
     * Displays personal data consent page.
     */
    public function actionPersonalDataConsent(): string
    {
        $this->setPageSeoData('Согласие на обработку персональных данных', 'personal-data-consent');
        return $this->render('personal-data-consent');
    }

    /**
     * Displays advertising consent page.
     */
    public function actionAdvertisingConsent(): string
    {
        $this->setPageSeoData('Согласие на рекламную рассылку', 'advertising-consent');
        return $this->render('advertising-consent');
    }
}
