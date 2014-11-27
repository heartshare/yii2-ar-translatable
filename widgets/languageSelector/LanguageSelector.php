<?php

/**
 * User: Alexander Sazanovich <alexander@uniq.by>
 * Date: 27.11.2014
 * Time: 11:41
 */

namespace uniqby\yii2ArTranslatable\widgets\languageSelector;


use uniqby\yii2ArTranslatable\models\Language;
use yii\base\Widget;

class LanguageSelector extends Widget
{
    public function run()
    {
        return $this->render('default', [
            'current' => \Yii::$app->languageManager->getCurrent(),
            'langs' => Language::find()->all(),
        ]);
    }
} 