<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\SignupForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Регистрация';

// Подключаем FontAwesome для иконок
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

// Подключаем CSS для страницы авторизации
$this->registerCssFile('@web/css/auth-pages.css', ['depends' => [\yii\web\YiiAsset::class]]);
?>

<div class="auth-page signup">
    <div class="container">
        <div class="auth-page-head">
            <div class="auth-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="title">Присоединяйтесь к нам!</div>
            <div class="subtitle">Создайте аккаунт и получите доступ к личному кабинету</div>
        </div>
        
        <div class="auth-page-main">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="auth-form-container">
                        <div class="auth-form-header">
                            <h3>Регистрация</h3>
                            <p>Заполните форму для создания аккаунта</p>
                        </div>
                        
                        <?php $form = ActiveForm::begin([
                            'id' => 'form-signup',
                            'options' => ['class' => 'auth-form']
                        ]); ?>
                        
                        <div class="form-group-custom">
                            <div class="input-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <?= $form->field($model, 'username')->textInput([
                                'autofocus' => true,
                                'placeholder' => 'Имя пользователя',
                                'class' => 'form-control-custom'
                            ])->label(false) ?>
                        </div>
                        
                        <div class="form-group-custom">
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <?= $form->field($model, 'email')->textInput([
                                'placeholder' => 'Email адрес',
                                'class' => 'form-control-custom',
                                'type' => 'email'
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
                        
                        <div class="form-group-submit">
                            <?= Html::submitButton('<i class="fas fa-user-plus"></i> Зарегистрироваться', [
                                'class' => 'btn btn-primary btn-lg btn-block',
                                'name' => 'signup-button'
                            ]) ?>
                        </div>
                        
                        <?php ActiveForm::end(); ?>
                        
                        <div class="auth-divider">
                            <span>или</span>
                        </div>
                        
                        <div class="auth-register">
                            <p>Уже есть аккаунт?</p>
                            <?= Html::a('<i class="fas fa-sign-in-alt"></i> Войти', ['site/login'], [
                                'class' => 'btn btn-outline-primary btn-lg'
                            ]) ?>
                        </div>
                        
                        <div class="auth-info">
                            <div class="info-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>Ваши данные защищены</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-gift"></i>
                                <span>Специальные предложения для зарегистрированных пользователей</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-history"></i>
                                <span>История заказов и быстрое повторное оформление</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
