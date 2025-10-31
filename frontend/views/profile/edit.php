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

// Подключаем CSS для личного кабинета
$this->registerCssFile('@web/css/profile-pages.css', ['depends' => [\yii\web\YiiAsset::class]]);
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
                        <div class="section-header">
                            <h4>Личные данные</h4>
                            <a href="<?= Url::to(['/profile']) ?>" class="back-link">
                                <i class="fas fa-arrow-left"></i> Назад в профиль
                            </a>
                        </div>
                        
                        <?php $form = ActiveForm::begin([
                            'id' => 'profile-form',
                            'options' => ['class' => 'profile-form']
                        ]); ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <div class="input-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <?= $form->field($user, 'username')->textInput([
                                        'class' => 'form-control-custom',
                                        'placeholder' => 'Имя пользователя',
                                        'readonly' => true
                                    ])->label('Имя пользователя') ?>
                                    <small class="form-text text-muted">Имя пользователя нельзя изменить</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <div class="input-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <?= $form->field($user, 'email')->textInput([
                                        'class' => 'form-control-custom',
                                        'placeholder' => 'Email адрес',
                                        'readonly' => true
                                    ])->label('Email') ?>
                                    <small class="form-text text-muted">Email нельзя изменить</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <div class="input-icon">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                    <?= $form->field($user, 'full_name')->textInput([
                                        'class' => 'form-control-custom',
                                        'placeholder' => 'Полное имя'
                                    ])->label('Полное имя') ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <div class="input-icon">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <?= $form->field($user, 'phone')->widget(MaskedInput::class, [
                                        'mask' => '+7 (999) 999-99-99',
                                        'options' => [
                                            'class' => 'form-control-custom',
                                            'placeholder' => '+7 (999) 123-45-67'
                                        ]
                                    ])->label('Телефон') ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <div class="input-icon">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                    <?= $form->field($user, 'birth_date')->textInput([
                                        'type' => 'date',
                                        'class' => 'form-control-custom'
                                    ])->label('Дата рождения') ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <div class="input-icon">
                                        <i class="fas fa-venus-mars"></i>
                                    </div>
                                    <?= $form->field($user, 'gender')->dropDownList(
                                        User::getGenderList(),
                                        [
                                            'class' => 'form-control-custom',
                                            'prompt' => 'Выберите пол'
                                        ]
                                    )->label('Пол') ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group-custom">
                            <div class="input-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <?= $form->field($user, 'delivery_address')->textarea([
                                'class' => 'form-control-custom',
                                'placeholder' => 'Адрес доставки по умолчанию',
                                'rows' => 3
                            ])->label('Адрес доставки') ?>
                        </div>
                        
                        <div class="form-actions">
                            <?= Html::submitButton('<i class="fas fa-save"></i> Сохранить изменения', [
                                'class' => 'btn btn-foo'
                            ]) ?>
                            
                            <a href="<?= Url::to(['/profile']) ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Отмена
                            </a>
                        </div>
                        
                        <?php ActiveForm::end(); ?>
                        
                        <div class="profile-info">
                            <div class="info-section">
                                <h5><i class="fas fa-info-circle"></i> Информация</h5>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <i class="fas fa-shield-alt"></i>
                                        <span>Ваши данные надежно защищены</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-truck"></i>
                                        <span>Адрес доставки будет автоматически подставляться при оформлении заказов</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-user-check"></i>
                                        <span>Заполненный профиль ускоряет оформление заказов</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
