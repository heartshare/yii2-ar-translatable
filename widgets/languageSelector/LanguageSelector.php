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
    /**
     * Указывает активный язык в виджете
     *
     * @var null|integer
     */
    public $currentId = null;

    public function run()
    {
        if (null == $this->currentId || null === ($current = \Yii::$app->languageManager->getById($this->currentId))) {
            $current = \Yii::$app->languageManager->getCurrent();
        }

        return $this->render('default', [
            'current' => $current,
            'langs' => Language::find()->all(),
        ]);
    }
} 