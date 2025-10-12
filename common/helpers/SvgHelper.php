<?php

namespace common\helpers;

class SvgHelper
{
    /**
     * Вставляет SVG-иконку из папки web/img/.
     */
    public static function getIcon(string $name) :string
    {
        $path = \Yii::getAlias('@webroot/images/' . $name . '.svg');

        if (file_exists($path)) {
            return file_get_contents($path);
        }

        return '';
    }
}