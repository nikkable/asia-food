<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Вход в личный кабинет';

$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
?>

<div class="auth-page">
    <div class="container">
        <div class="auth-page-main">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="auth-form-container">
                        <div class="auth-form-head">
                            <div class="title-2">Войти в личный кабинет</div>
                        </div>
                        
                        <?php $form = ActiveForm::begin([
                            'id' => 'login-form',
                            'options' => ['class' => 'auth-form']
                        ]); ?>
                        
                        <div class="form-group">
                            <?= $form->field($model, 'username')->textInput([
                                'autofocus' => true,
                                'placeholder' => 'Имя пользователя или email',
                                'class' => 'field-text'
                            ])->label(false) ?>
                        </div>
                        
                        <div class="form-group">
                            <?= $form->field($model, 'password')->passwordInput([
                                'placeholder' => 'Пароль',
                                'class' => 'field-text'
                            ])->label(false) ?>
                        </div>
                        
                        <div class="form-group">
                            <div class="field-checkbox">
                                <input type="checkbox" id="loginform-rememberme" class="form-check-input" name="LoginForm[rememberMe]" value="<?= $model->rememberMe ?>">
                                <label for="loginform-rememberme">Запомнить меня</label>
                            </div>
                        </div>

                        <div class="form-group-submit">
                            <?= Html::submitButton('<i class="fas fa-sign-in-alt"></i> Войти', [
                                'class' => 'but but-three',
                                'name' => 'login-button'
                            ]) ?>
                        </div>
                        
                        <?php ActiveForm::end(); ?>

                        <div>
                            <p>
                                <?= Html::a('Забыли пароль?', ['site/request-password-reset']) ?>
                            </p>
                        </div>


                        <div class="auth-divider">
                            <span>или</span>
                        </div>
                        
                        <div class="auth-register">
                            <p>Нет аккаунта?</p>
                            <?= Html::a('<i class="fas fa-user-plus"></i> Зарегистрироваться', ['site/signup'], [
                                'class' => 'but but-secondary'
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
