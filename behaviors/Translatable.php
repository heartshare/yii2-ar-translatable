<?php

/**
 * User: Alexander Sazanovich <alexander@uniq.by>
 * Date: 27.11.2014
 * Time: 5:09
 */

namespace uniqby\yii2ArTranslatable\behaviors;

use yii\base\Model;
use yii\base\UnknownClassException;
use yii\db\ActiveRecord;

/**
 * Translatable behavior implements the basic methods
 * for translating dynamic content of models.
 * Behavior takes language from SLanguageManager::active or you
 * can specify language id through language() method.
 *
 * Example:
 * Find object with language id 2
 *     Object::model()->language(2)->find();
 * Detect language from array
 *     Object::model()->language($_GET)->find();
 * Language detected automatically
 *     Object::model()->find();
 *
 * Usage:
 * 1. Create new relation
 *  'translate'=>array(self::HAS_ONE, 'Translate Storage Model', 'object_id'),
 * 2. Attach behavior and enter translateable attributes
 *   'STranslateBehavior'=>array(
 *       'class'=>'ext.behaviors.STranslateBehavior',
 *       'translateAttributes'=>array(
 *           'title',
 *           'short_description',
 *           'full_description'
 *           etc...
 *       ),
 *   ),
 * 3. Set Model::$translateModelName - name of the model that handles translations.
 * 4. Create new db table to handle translated attribute values.
 *    Basic structure: id, object_id, language_id + attributes.
 * 5. Create 'Translate Storage Model' class and set $tableName.
 * 6. Connect events onCreate and onDelete
 * 7. Add language method to admin controller
 *
 * @todo: Dont load translations when system, has only one language
 *
 * @package common\components\yii2ArTranslatable\behaviors
 *
 * @property ActiveRecord $owner
 */
class Translatable extends \yii\base\Behavior
{
    public $translationRelation = 'translations';
    /**
     * @var string
     */
    public $languageIdFieldName = 'language_id';
    /**
     * @var string
     */
    public $ownerIdFieldName = 'owner_id';
    /**
     * @var string|null
     */
    public $translationModelClassName;
    public $languageId;
    private $translationModel;
    /**
     * @var string
     */
    private $suffix = 'Translation';

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_INSERT | OP_UPDATE,
        ];
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_VALIDATE => 'afterValidate',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
        ];
    }

    public function attach($owner)
    {
        parent::attach($owner);

        $this->resolveClassName();
        $this->resolveLanguageId();
    }

    /**
     * Resolves translation model class name
     *
     * @throws UnknownClassException
     */
    public function resolveClassName()
    {
        if (empty($this->translationModelClassName)) {
            $paths = explode('\\', $this->owner->className());

            $parentClassName = array_pop($paths);
            // Remove 'Search' from class name, eg.: NewsSearch -> News
            if (substr($parentClassName, -6, 6) == 'Search') {
                $parentClassName = str_replace('Search', '', $parentClassName);
            }

            $paths[] = mb_strtolower($this->suffix, \Yii::$app->charset);
            $paths[] = $parentClassName . $this->suffix;
            $this->translationModelClassName = implode('\\', $paths);
            $this->translationModel = new $this->translationModelClassName;

            if (!class_exists($this->translationModelClassName)) {
                throw new UnknownClassException();
            }

            \Yii::info(
                "Translate model for '{$parentClassName}' resolved: '{$this->translationModelClassName}'",
                __METHOD__
            );
        }
    }

    public function resolveLanguageId()
    {
        if (null === $this->languageId) {
            $this->languageId = \Yii::$app->languageManager->getCurrent()->id;
        }
    }

    /**
     * @return void
     */
    public function afterValidate()
    {
        if (!Model::validateMultiple($this->owner->{$this->translationRelation})) {
            $this->owner->addError($this->translationRelation);
        }
    }

    /**
     * @return void
     */
    public function afterSave()
    {
        /* @var ActiveRecord $translation */
//        foreach ($this->owner->translations as $translation) {
//            $this->owner->link('translations', $this->owner->translation);
//        }

        /* @var ActiveRecord $translation */
        foreach ($this->owner->{$this->translationRelation} as $translation) {
            $this->owner->link($this->translationRelation, $translation);
        }
    }

    public function __get($name)
    {
        $t = $this->getTranslation();
        try {
            $value = $t->__get($name);
        } catch (UnknownPropertyException $e) {
            if ($t->hasAttribute($name)) {
                $value = $t->getAtribute($name);
            } else {
                throw $e;
            }
        }

        return $value;
    }

    public function __set($name, $value)
    {
        $t = $this->getTranslation();
        try {
            $value = $t->__set($name, $value);
        } catch (UnknownPropertyException $e) {
            if ($t->hasAttribute($name)) {
                $t->setAttribute($name, $value);
            } else {
                throw $e;
            }
        }
    }

    /**
     * Returns the translation model for the specified language.
     *
     * @param string|null $language
     *
     * @return ActiveRecord
     */
    public function getTranslation($language = null)
    {
        if ($language === null) {
            $language = $this->languageId;
        }

        /* @var ActiveRecord[] $translations */
        $translations = $this->owner->{$this->translationRelation};
        foreach ($translations as $translation) {
            if ($translation->getAttribute($this->languageIdFieldName) === $language) {
                return $translation;
            }
        }
        /* @var ActiveRecord $class */
        $class = $this->owner->getRelation($this->translationRelation)->modelClass;
        /* @var ActiveRecord $translation */
        $translation = new $class();
        $translation->setAttribute($this->languageIdFieldName, $language);
        $translations[] = $translation;
        $this->owner->populateRelation($this->translationRelation, $translations);
        return $translation;
    }

    /**
     * @inheritdoc
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return in_array($name, $this->getTranslationModelAttributes()) || parent::canGetProperty($name, $checkVars);
    }

    /**
     * Returns all attributes from translation model
     *
     * @return array
     */
    private function getTranslationModelAttributes()
    {
        $key = __CLASS__ . '_translationAttributes';
        if (!($attributes = \Yii::$app->cache->get($key))) {
            $attributes = array_keys($this->translationModel->attributes);
            \Yii::$app->cache->set($key, $attributes, 60);
        }

        return $attributes;
    }

    /**
     * @inheritdoc
     */
    public function canSetProperty($name, $checkVars = true)
    {
        return in_array($name, $this->getTranslationModelAttributes()) || parent::canSetProperty($name, $checkVars);
    }
}