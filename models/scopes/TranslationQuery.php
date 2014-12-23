<?php

/**
 * User: Alexander Sazanovich <alexander@uniq.by>
 * Date: 27.11.2014
 * Time: 9:24
 */

namespace uniqby\yii2ArTranslatable\models\scopes;

use yii\db\ActiveQuery;

class TranslationQuery extends ActiveQuery
{
    public function forLanguageId($id = null)
    {
        $alias = call_user_func([$this->modelClass, 'tableName']);

        $this->andWhere([$alias . '.language_id' => $id]);
        return $this;
    }
} 