<?php

/** @var yii\web\View $this */
/** @var common\models\User $user */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap5\ActiveForm;
use yii\widgets\MaskedInput;
use common\models\User;

$this->title = 'Редактирование профиля';

// Подключаем FontAwesome для иконок
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

?>

<div class="profile-page">
    <div class="container">
        <div class="profile-page-head">
            <div class="title text-center">Редактирование профиля</div>
        </div>
        
        <div class="profile-page-main">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="profile-edit-container">
                        <div class="profile-edit-head">
                            <div class="title-2">Личные данные</div>
                            <a href="<?= Url::to(['/profile']) ?>" class="but but-link">
                                <i class="fas fa-arrow-left"></i> Назад в профиль
                            </a>
                        </div>
                        
                        <?php $form = ActiveForm::begin([
                            'id' => 'profile-form',
                            'options' => ['class' => 'profile-form']
                        ]); ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= $form->field($user, 'username')->textInput([
                                        'class' => 'field-text',
                                        'placeholder' => 'Имя пользователя',
                                        'readonly' => true
                                    ])->label('Имя пользователя') ?>
                                    <small class="form-text text-muted">Имя пользователя нельзя изменить</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= $form->field($user, 'email')->textInput([
                                        'class' => 'field-text',
                                        'placeholder' => 'Email адрес',
                                        'readonly' => true
                                    ])->label('Email') ?>
                                    <small class="form-text text-muted">Email нельзя изменить</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= $form->field($user, 'full_name')->textInput([
                                        'class' => 'field-text',
                                        'placeholder' => 'Фамилия и имя'
                                    ])->label('Фамилия и имя <span class="text-danger">*</span>') ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= $form->field($user, 'phone')->widget(MaskedInput::class, [
                                        'mask' => '+7 (999) 999-99-99',
                                        'options' => [
                                            'class' => 'field-text',
                                            'placeholder' => '+7 (999) 123-45-67'
                                        ]
                                    ])->label('Телефон') ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= $form->field($user, 'birth_date')->textInput([
                                        'type' => 'date',
                                        'class' => 'field-text'
                                    ])->label('Дата рождения') ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= $form->field($user, 'gender')->dropDownList(
                                        User::getGenderList(),
                                        [
                                            'class' => 'field-text',
                                            'prompt' => 'Выберите пол'
                                        ]
                                    )->label('Пол') ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <?= $form->field($user, 'delivery_address')->textarea([
                                'class' => 'field-textarea',
                                'placeholder' => 'Адрес доставки по умолчанию',
                                'rows' => 3
                            ])->label('Адрес доставки') ?>
                        </div>
                        
                        <div class="profile-edit-actions">
                            <?= Html::submitButton('<i class="fas fa-save"></i> Сохранить изменения', [
                                'class' => 'but but-three'
                            ]) ?>
                            
                            <a href="<?= Url::to(['/profile']) ?>" class="but but-secondary">
                                <i class="fas fa-times"></i> Отмена
                            </a>
                        </div>
                        
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
