<?php

/**
 * User: Alexander Sazanovich <alexander@uniq.by>
 * Date: 27.11.2014
 * Time: 7:40
 */

namespace uniqby\yii2ArTranslatable\models\prototypes;

use yii\base\UnknownPropertyException;
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
    public function behaviors()
    {
        return [
            'translatable' => 'uniqby\yii2ArTranslatable\behaviors\Translatable'
        ];
    }

    public static function find()
    {
        return parent::find()->with('translation');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany($this->translationModelClassName, [$this->ownerIdFieldName => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslation()
    {
        return $this->hasOne(
            $this->translationModelClassName, [$this->ownerIdFieldName => 'id']
        )->forLanguageId($this->languageId);// ->inverseOf('owner');
    }

    public function __get($name)
    {
//        var_dump($this->getRelation('translation'));

        try {
            $value = parent::__get($name);
        } catch (UnknownPropertyException $e) {
            if ($this->translation->hasAttribute($name)) {
                $value = $this->translation->{$name};
            } else {
                throw $e;
            }
        }

        return $value;
    }
} 