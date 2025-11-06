<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\ResetPasswordForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Reset password';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="auth-page signup">
    <div class="container">
        <div class="auth-page-main">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="auth-form-container">
                        <div class="auth-form-head">
                            <div class="title-2">Введите ваш новый пароль</div>
                        </div>

                        <?php $form = ActiveForm::begin([
                            'id' => 'reset-password-form',
                            'options' => ['class' => 'auth-form']
                        ]); ?>

                        <div class="form-group">
                            <?= $form->field($model, 'password')->passwordInput([
                                'placeholder' => 'Пароль',
                                'class' => 'field-text'
                            ])->label(false) ?>
                        </div>

                        <div class="form-group-submit">
                            <?= Html::submitButton('<i class="fas fa-user-plus"></i> Отправить', [
                                'class' => 'but but-three',
                                'name' => 'signup-button'
                            ]) ?>
                        </div>

                        <?php ActiveForm::end(); ?>

                        <div class="auth-divider">
                            <span>или</span>
                        </div>

                        <div class="auth-register">
                            <p>Вернуться?</p>
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
