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
    /**
     * @inheritdoc
     * @return TranslationQuery
     */
    public static function find()
    {
        return new TranslationQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            \uniqby\yii2ArTranslatable\behaviors\Translation::className()
        ];
    }
} 