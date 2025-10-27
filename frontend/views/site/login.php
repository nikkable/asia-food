<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Вход в личный кабинет';

// Подключаем FontAwesome для иконок
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

// Подключаем CSS для страницы авторизации
$this->registerCssFile('@web/css/auth-pages.css', ['depends' => [\yii\web\YiiAsset::class]]);
?>

<div class="auth-page">
    <div class="container">
        <div class="auth-page-head">
            <div class="auth-icon">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="title">Добро пожаловать!</div>
            <div class="subtitle">Войдите в свой личный кабинет</div>
        </div>
        
        <div class="auth-page-main">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="auth-form-container">
                        <div class="auth-form-header">
                            <h3>Вход в систему</h3>
                            <p>Введите свои данные для входа</p>
                        </div>
                        
                        <?php $form = ActiveForm::begin([
                            'id' => 'login-form',
                            'options' => ['class' => 'auth-form']
                        ]); ?>
                        
                        <div class="form-group-custom">
                            <div class="input-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <?= $form->field($model, 'username')->textInput([
                                'autofocus' => true,
                                'placeholder' => 'Имя пользователя или email',
                                'class' => 'form-control-custom'
                            ])->label(false) ?>
                        </div>
                        
                        <div class="form-group-custom">
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                            </div>
                            <?= $form->field($model, 'password')->passwordInput([
                                'placeholder' => 'Пароль',
                                'class' => 'form-control-custom'
                            ])->label(false) ?>
                        </div>
                        
                        <div class="form-group-checkbox">
                            <?= $form->field($model, 'rememberMe')->checkbox([
                                'class' => 'custom-checkbox'
                            ])->label('Запомнить меня') ?>
                        </div>
                        
                        <div class="form-group-submit">
                            <?= Html::submitButton('<i class="fas fa-sign-in-alt"></i> Войти', [
                                'class' => 'btn btn-primary btn-lg btn-block',
                                'name' => 'login-button'
                            ]) ?>
                        </div>
                        
                        <?php ActiveForm::end(); ?>
                        
                        <div class="auth-links">
                            <div class="auth-link-item">
                                <?= Html::a('<i class="fas fa-key"></i> Забыли пароль?', ['site/request-password-reset'], [
                                    'class' => 'auth-link'
                                ]) ?>
                            </div>
                            
                            <div class="auth-link-item">
                                <?= Html::a('<i class="fas fa-envelope"></i> Повторно отправить письмо', ['site/resend-verification-email'], [
                                    'class' => 'auth-link'
                                ]) ?>
                            </div>
                        </div>
                        
                        <div class="auth-divider">
                            <span>или</span>
                        </div>
                        
                        <div class="auth-register">
                            <p>Нет аккаунта?</p>
                            <?= Html::a('<i class="fas fa-user-plus"></i> Зарегистрироваться', ['site/signup'], [
                                'class' => 'btn btn-outline-primary btn-lg'
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
