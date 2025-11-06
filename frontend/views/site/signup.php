<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\SignupForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Регистрация';

// Подключаем FontAwesome для иконок
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

?>

<div class="auth-page signup">
    <div class="container">
        <div class="auth-page-main">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="auth-form-container">
                        <div class="auth-form-head">
                            <div class="title-2">Регистрация</div>
                        </div>
                        
                        <?php $form = ActiveForm::begin([
                            'id' => 'form-signup',
                            'options' => ['class' => 'auth-form']
                        ]); ?>
                        
                        <div class="form-group">
                            <?= $form->field($model, 'username')->textInput([
                                'autofocus' => true,
                                'placeholder' => 'Имя пользователя',
                                'class' => 'field-text'
                            ])->label(false) ?>
                        </div>
                        
                        <div class="form-group">
                            <?= $form->field($model, 'email')->textInput([
                                'placeholder' => 'Email адрес',
                                'class' => 'field-text',
                                'type' => 'email'
                            ])->label(false) ?>
                        </div>
                        
                        <div class="form-group">
                            <?= $form->field($model, 'password')->passwordInput([
                                'placeholder' => 'Пароль',
                                'class' => 'field-text'
                            ])->label(false) ?>
                        </div>
                        
                        <div class="form-group-submit">
                            <?= Html::submitButton('<i class="fas fa-user-plus"></i> Зарегистрироваться', [
                                'class' => 'but but-three',
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
                                'class' => 'but but-secondary'
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
