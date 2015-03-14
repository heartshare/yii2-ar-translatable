<?php

/**
 * User: Alexander Sazanovich <alexander@uniq.by>
 * Date: 27.11.2014
 * Time: 7:40
 */

namespace uniqby\yii2ArTranslatable\models\prototypes;

use yii\db\ActiveRecord;

/**
 * Class Translatable
 * @package uniqby\yii2ArTranslatable\models\prototypes
 *
 * @property Translation $translation
 * @property Translations[] $translations
 */
class Translatable extends ActiveRecord
{
    public static function find()
    {
        return parent::find()->joinWith('translations');
    }

    public function behaviors()
    {
        return [
            'translatable' => 'uniqby\yii2ArTranslatable\behaviors\Translatable'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany($this->translationModelClassName, [$this->ownerIdFieldName => 'id']);
    }
}