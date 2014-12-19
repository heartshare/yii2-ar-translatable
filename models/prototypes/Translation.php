<?php

/**
 * User: Alexander Sazanovich <alexander@uniq.by>
 * Date: 27.11.2014
 * Time: 7:46
 */

namespace uniqby\yii2ArTranslatable\models\prototypes;

use uniqby\yii2ArTranslatable\models\scopes\TranslationQuery;
use yii\db\ActiveRecord;

class Translation extends ActiveRecord
{
    public $languageIdFieldName = 'language_id';

    /**
     * @inheritdoc
     * @return TranslationQuery
     */
    public static function find()
    {
        return new TranslationQuery(get_called_class());
    }

    public function getLanguage()
    {
        return $this->owner->hasOne(Language::className(), ['id' => $this->languageIdFieldName]);
    }
} 