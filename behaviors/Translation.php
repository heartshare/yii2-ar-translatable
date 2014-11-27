<?php

/**
 * User: Alexander Sazanovich <alexander@uniq.by>
 * Date: 27.11.2014
 * Time: 7:29
 */

namespace uniqby\yii2ArTranslatable\behaviors;


use uniqby\yii2ArTranslatable\models\Language;
use yii\base\Behavior;

class Translation extends Behavior
{
    public $languageIdFieldName = 'language_id';

    public function getLanguage()
    {
        return $this->owner->hasOne(Language::className(), ['id' => $this->languageIdFieldName]);
    }
} 