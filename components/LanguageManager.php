<?php

/**
 * User: Alexander Sazanovich <alexander@uniq.by>
 * Date: 27.11.2014
 * Time: 8:45
 */

namespace uniqby\yii2ArTranslatable\components;

use common\models\UserProfile;
use uniqby\yii2ArTranslatable\models\Language;
use Yii;
use yii\base\Component;

class LanguageManager extends Component
{
    /**
     * @var Language
     */
    private $current;

    /**
     * @var Language
     */
    private $default;

    /**
     * Получение языка по-умолчанию
     *
     * @return Language
     */
    public function getDefault()
    {
        if (null === $this->default) {
            $this->default = Language::getDefault();
        }
        return $this->default;
    }

    /**
     * Получение активного для текщего запроса языка
     *
     * @return Language
     */
    public function getCurrent()
    {
        if (null === $this->current) {
            $this->current = $this->getDefault();
        }
        return $this->current;
    }

    /**
     * Установка текущего объекта языка и локаль пользователя
     *
     * @param null|string $url
     */
    public function setCurrent($url = null)
    {
        $profile = null;
        if (!Yii::$app->user->getIsGuest()) {
            $profile = Yii::$app->getUser()->getIdentity()->profile;
        }

        $language = Language::getByUrl($url);
        if ($language === null) {
            // Язык не определен по URL, пытаемся получить из профиля пользователя.
            if (!($profile instanceof UserProfile) || null === ($language = $profile->language)) {
                // Если гость или в профиле пользователя не указан id языка, то применяем язык по-умолчанию.
                $language = $this->getDefault();
            }
        } else {
            // Если не гость - обновляем его язык.
            if (($profile instanceof UserProfile) && $profile->language_id != $language->id) {
                $profile->language_id = $language->id;
                $profile->update(false, ['language_id']);
            }
        }

        $this->current = $language;
        Yii::$app->language = $language->locale;
    }

    public function getById($id)
    {
        if (empty($id)) {
            return null;
        }

        return Language::findOne($id);
    }
} 