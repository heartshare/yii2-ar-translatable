# Translatable ActiveRecord behavior

[Yii2](http://www.yiiframework.com) Translatable ActiveRecord behavior makes the translating of your application so simple

## Installation

### Composer

The preferred way to install this extension is through [Composer](http://getcomposer.org/).

Either run

```
php composer.phar require uniqby/yii2-ar-translatable "dev-master"
```

or add

```
"uniqby/yii2-ar-translatable": "dev-master"
```

to the require section of your ```composer.json```

## Configuration

### Configure your application in common config:

```php
'sourceLanguage' => 'en-US',
'language' => 'ru-RU',
'components' => [
    'languageManager' => [
        'class' => 'uniqby\yii2ArTranslatable\components\LanguageManager',
    ],
    //
    'urlManager' => [
        'class' => 'uniqby\yii2ArTranslatable\components\UrlManager',
        ...
    ],
    //
    'request' => [
        'class' => 'uniqby\yii2ArTranslatable\components\Request'
    ]
]
```

#### Run

```
php yii migrate --migrationPath=@uniqby/yii2ArTranslatable/migrations
```
Command will create table languages with 2 default languages: Russian and English.

#### Create tables for your data and translation

```
CREATE TABLE `news` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `is_active` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `news_translation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) unsigned NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `short_description` text,
  `full_description` text,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `language_id` (`language_id`),
  KEY `fk_news_translate_news1_idx` (`object_id`),
  CONSTRAINT `fk_NewsTranslate_Language` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_news_translate_news1` FOREIGN KEY (`owner_id`) REFERENCES `news` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

#### Create models for tables

@common/models/News.php
```php
<?php
namespace common\models;

use uniqby\yii2ArTranslatable\models\prototypes\Translatable;
use Yii;

class News extends Translatable
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        ...
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('user/company', 'ID'),
        ];
    }
}
```

@common/models/translation/NewsTranslation.php
```php
<?php

namespace common\models\translation;

use uniqby\yii2ArTranslatable\models\prototypes\Translation;
use Yii;

class NewsTranslation extends Translation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news_translation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['language_id', 'owner_id', 'title'], 'required'],
            [['language_id', 'owner_id'], 'integer'],
            [['title', 'short_description'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        ...
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(News::className(), ['id' => 'owner_id']);
    }
}

```

## Usage

```php
$news = new News();
var_dump($news->title);

// output
```

## Widget

Add Language switcher to your page

```php
<?= \uniqby\yii2ArTranslatable\widgets\languageSelector\LanguageSelector::widget() ?>
```

## Author

[Alexander Sazanovich](https://uniq.by/), e-mail: [alexander@uniq.by](mailto:alexander@uniq.by)