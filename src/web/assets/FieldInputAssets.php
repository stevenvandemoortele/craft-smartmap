<?php
/**
 * Smart Map plugin for Craft CMS
 *
 * The most comprehensive proximity search and mapping tool for Craft.
 *
 * @author    Double Secret Agency
 * @link      https://www.doublesecretagency.com/
 * @copyright Copyright (c) 2014 Double Secret Agency
 */

namespace doublesecretagency\smartmap\web\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

use doublesecretagency\smartmap\web\assets\GoogleMapsAssets;

/**
 * Class FieldInputAssets
 * @since 3.0.0
 */
class FieldInputAssets extends AssetBundle
{

    /** @inheritdoc */
    public function init()
    {
        parent::init();

        $this->sourcePath = '@doublesecretagency/smartmap/resources';
        $this->depends = [CpAsset::class, GoogleMapsAssets::class];

        $this->css = [
            'css/address-fieldtype.css',
        ];

        $this->js = [
            'js/address-fieldtype.js',
        ];
    }

}