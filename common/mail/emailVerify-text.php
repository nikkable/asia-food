<?php

/** @var yii\web\View $this */
/** @var common\models\User $user */

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verification_token]);
?>
Привет <?= $user->username ?>,

Перейдите по ссылке ниже, чтобы подтвердить свой адрес электронной почты:

<?= $verifyLink ?>
