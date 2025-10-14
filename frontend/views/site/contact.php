<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Контакты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">
    <h1 class="mb-4 text-center"><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-lg-7 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h3 class="mb-3">ООО «Азия Фуд»</h3>
                    <p><strong>Адрес:</strong> г. Оренбург, ул. Харьковская, д. 127</p>
                    <p><strong>ИНН:</strong> 5610243853</p>
                    <p><strong>ОГРН:</strong> 1215600031001</p>
                    <p><strong>КПП:</strong> 561001001</p>
                    <p><strong>р/сч:</strong> 40702810946000013167 в ОРЕНБУРГСКОЕ ОТДЕЛЕНИЕ N8623 ПАО СБЕРБАНК</p>
                    <p><strong>БИК:</strong> 045354601</p>
                    <p><strong>к/сч:</strong> 30101810600000000601</p>
                    <p><strong>Телефон:</strong> <a href="tel:+79228111503">+7 922 811 15 03</a></p>
                    <p><strong>Эл. почта:</strong> <a href="mailto:albekovn@bk.ru">albekovn@bk.ru</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
