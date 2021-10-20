<?php

namespace Triangulum\Yii\Unit\Front\Items;

use yii\web\AssetBundle;
use yii\web\YiiAsset;
use yii\widgets\PjaxAsset;

class FrontPopupAsset extends AssetBundle
{
    public $depends = [
        YiiAsset::class,
        PjaxAsset::class,
    ];

    public $js = [
        'js/Popup.js?v=0.001',
    ];

    public function init(): void
    {
        $this->sourcePath = __DIR__ . '/assets';

        parent::init();
    }
}
