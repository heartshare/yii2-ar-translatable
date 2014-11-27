<?php

namespace uniqby\yii2ArTranslatable\models;

use Yii;

/**
 * This is the model class for table "language".
 *
 * @property integer $id
 * @property string $url
 * @property string $locale
 * @property string $name
 * @property boolean $is_default
 *
 * @property CompanyFormTranslation[] $companyFormTranslations
 */
class Language extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'language';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url', 'locale', 'name'], 'required'],
            [['is_default'], 'boolean'],
            [['url', 'locale'], 'string', 'max' => 5],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('uniqby/yii2ArTranslatable', 'ID'),
            'url' => Yii::t('uniqby/yii2ArTranslatable', 'Url'),
            'locale' => Yii::t('uniqby/yii2ArTranslatable', 'Locale'),
            'name' => Yii::t('uniqby/yii2ArTranslatable', 'Name'),
            'is_default' => Yii::t('uniqby/yii2ArTranslatable', 'Is Default'),
        ];
    }

    //Получения объекта языка по умолчанию
    static function getDefault()
    {
        return self::findOne([
            'is_default' => true
        ]);
    }

    //Получения объекта языка по буквенному идентификатору
    static function getByUrl($url = null)
    {
        if ($url === null) {
            return null;
        } else {
            $language = self::find()->where('url = :url', [':url' => $url])->one();
            if ($language === null) {
                return null;
            } else {
                return $language;
            }
        }
    }
}
