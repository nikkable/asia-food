<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var repositories\Category\models\Category $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'parent_id')->textInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'id' => 'category-name']) ?>

    <?= $form->field($model, 'slug')->textInput([
        'maxlength' => true, 
        'id' => 'category-slug',
        'placeholder' => 'Автогенерируется из названия'
    ]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'imageFile')->fileInput(['accept' => 'image/*']) ?>
    
    <?php if ($model->image): ?>
        <div class="form-group">
            <label class="control-label">Текущее изображение:</label><br>
            <img src="<?= $model->getImageUrl() ?>" alt="<?= Html::encode($model->name) ?>" style="max-width: 200px; max-height: 200px;">
        </div>
    <?php endif; ?>

    <?= $form->field($model, 'status')->dropDownList([
        1 => 'Активная',
        0 => 'Неактивная'
    ], ['prompt' => 'Выберите статус']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs("
var slugManuallyEdited = false;

function transliterate(text) {
    var ru = 'а б в г д е ё ж з и й к л м н о п р с т у ф х ц ч ш щ ъ ы ь э ю я'.split(' ');
    var en = 'a b v g d e e zh z i y k l m n o p r s t u f h ts ch sh sch  y  e yu ya'.split(' ');
    
    text = text.toLowerCase();
    
    for (var i = 0; i < ru.length; i++) {
        text = text.split(ru[i]).join(en[i]);
    }
    
    // Убираем все символы кроме букв, цифр и дефисов
    text = text.replace(/[^a-z0-9\s-]/g, '');
    // Заменяем пробелы и множественные дефисы на одиночные дефисы
    text = text.replace(/[\s-]+/g, '-');
    // Убираем дефисы в начале и конце
    text = text.replace(/^-+|-+$/g, '');
    
    return text;
}

// Автогенерация slug при вводе названия
$('#category-name').on('input', function() {
    var name = $(this).val();
    var slugField = $('#category-slug');
    
    // Автозаполняем slug только если поле пустое или пользователь не редактировал его вручную
    if (!slugManuallyEdited || slugField.val() === '') {
        var slug = transliterate(name);
        slugField.val(slug);
    }
});

// Отслеживаем ручное редактирование slug
$('#category-slug').on('input', function() {
    slugManuallyEdited = true;
});

// Кнопка для сброса к автогенерации
$('#category-slug').after('<button type=\"button\" class=\"btn btn-sm btn-outline-secondary ml-2\" id=\"regenerate-slug\" title=\"Сгенерировать заново из названия\">↻</button>');

$('#regenerate-slug').on('click', function() {
    var name = $('#category-name').val();
    var slug = transliterate(name);
    $('#category-slug').val(slug);
    slugManuallyEdited = false;
});
");
?>
