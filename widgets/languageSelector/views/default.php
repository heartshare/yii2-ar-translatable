<?php

/**
 * User: Alexander Sazanovich <alexander@uniq.by>
 * Date: 27.11.2014
 * Time: 11:47
 */

$items = [];
foreach ($langs as $lang) {
    if ($lang != $current) {
        $items[] = ['label' => $lang->name, 'url' => '/' . $lang->url . Yii::$app->getRequest()->getLangUrl()];
    }
}
?>
<div id="languageSelector">
    <?= \yii\bootstrap\ButtonDropdown::widget([
        'label' => $current->name,
        'options' => [
            'class' => 'btn-default'
        ],
        'dropdown' => [
            'items' => $items
        ],
    ]); ?>
</div>