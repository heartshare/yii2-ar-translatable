<?php

/**
 * User: Alexander Sazanovich <alexander@uniq.by>
 * Date: 27.11.2014
 * Time: 8:45
 */

namespace uniqby\yii2ArTranslatable\components;

use uniqby\yii2ArTranslatable\models\Language;
use yii\base\Component;
use Yii;

class LanguageManager extends Component
{
    /**
     * @var Language
     */
    private $current;

    public function getDefault()
    {
        return Language::getDefault();
    }

    public function getCurrent()
    {
        if ($this->current === null) {
            $this->current = $this->getDefault();
        }
        return $this->current;
    }

    //Установка текущего объекта языка и локаль пользователя
    public function setCurrent($url = null)
    {
        $language = Language::getByUrl($url);
        $this->current = ($language === null) ? $this->getDefault() : $language;

        Yii::$app->language = $this->current->locale;
    }
} 