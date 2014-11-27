<?php

/**
 * User: Alexander Sazanovich <alexander@uniq.by>
 * Date: 27.11.2014
 * Time: 8:44
 */

namespace uniqby\yii2ArTranslatable\components;

class UrlManager extends \yii\web\UrlManager
{
    public function createUrl($params)
    {
        if (isset($params['lang_id'])) {
            //Если указан идентификатор языка, то делаем попытку найти язык в БД,
            //иначе работаем с языком по умолчанию
            $lang = Language::findOne($params['lang_id']);
            if ($lang === null) {
                $lang = \Yii::$app->languageManager->getDefault();
            }
            unset($params['lang_id']);
        } else {
            //Если не указан параметр языка, то работаем с текущим языком
            $lang = \Yii::$app->languageManager->getCurrent();
        }

        //Получаем сформированный URL(без префикса идентификатора языка)
        $url = parent::createUrl($params);

        if ($lang->is_default) {
            return $url;
        } else {
            if ($url == '/') {
                return '/' . $lang->url;
            } else {
                return '/' . $lang->url . $url;
            }
        }
    }
} 