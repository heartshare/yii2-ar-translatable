<?php

/**
 * User: Alexander Sazanovich <alexander@uniq.by>
 * Date: 27.11.2014
 * Time: 5:09
 */

namespace uniqby\yii2ArTranslatable\behaviors;

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
 */
class Translatable extends \yii\base\Behavior
{
    /**
     * @var string
     */
    private $suffix = 'Translation';

    /**
     * @var string
     */
    private $_ownerIdFieldName = 'owner_id';

    /**
     * @var string
     */
    private $_languageIdFieldName = 'language_id';

    /**
     * @var string|null
     */
    private $_translationModelClassName;

    private $_languageId;

    public function getOwnerIdFieldName()
    {
        return $this->_ownerIdFieldName;
    }

    public function setOwnerIdFieldName($value)
    {
        $this->_ownerIdFieldName = $value;
    }

    public function getLanguageIdFieldName()
    {
        return $this->_languageIdFieldName;
    }

    public function setLanguageIdFieldName($value)
    {
        $this->_languageIdFieldName = $value;
    }

    public function getTranslationModelClassName()
    {
        return $this->_translationModelClassName;
    }

    public function setTranslationModelClassName($value)
    {
        $this->_translationModelClassName = $value;
    }

    public function getLanguageId()
    {
        return $this->_languageId;
    }

    public function setLanguageId($value)
    {
        $this->_languageId = $value;
    }

    public function events()
    {
        return array_merge(parent::events(), [
            ActiveRecord::EVENT_INIT => 'initialize'
        ]);
    }

    public function initialize()
    {
        $this->resolveLanguageId();
        $this->resolveClassName();
    }

    /**
     * Resolves translation model class name
     *
     * @throws UnknownClassException
     */
    public function resolveClassName()
    {
        $paths = explode('\\', $this->owner->className());
        $parentClassName = array_pop($paths);

        $paths[] = mb_strtolower($this->suffix, \Yii::$app->charset);
        $paths[] = $parentClassName . $this->suffix;
        $this->_translationModelClassName = implode('\\', $paths);

        if (!class_exists($this->_translationModelClassName)) {
            throw new UnknownClassException();
        }

        \Yii::info("Translate model for '{$parentClassName}' resolved: '{$this->_translationModelClassName}'",
            __METHOD__);
    }

    public function resolveLanguageId()
    {
        if (null === $this->_languageId) {
            $this->_languageId = \Yii::$app->languageManager->getCurrent()->id;
        }
    }
}