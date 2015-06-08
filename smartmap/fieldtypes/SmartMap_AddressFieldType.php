<?php
namespace Craft;

class SmartMap_AddressFieldType extends BaseFieldType
{
    public function getName()
    {
        return Craft::t('Address (Smart Map)');
    }
    // ==================================================== //

    // Modify SQL query
    public function modifyElementsQuery(DbCommand $query, $params)
    {
        if ($params !== null) {
            craft()->smartMap->modifyQuery($query, $params);
        }
    }

    /**
     * Renders the input fields for this fieldtype
     *
     * @param string $name
     * @param SmartMap_AddressModel $model
     *
     * @return string
     */
    public function getInputHtml($name, $model) // $model has been prepared by prepValue()
    {

        if (!$model) {
            $model = new SmartMap_AddressModel;
        }

        $model->handle = $name;

        craft()->smartMap->measurementUnit = MeasurementUnit::Miles;

        $api  = '//maps.googleapis.com/maps/api/js';
        $api .= craft()->smartMap->googleBrowserKey('?');

        craft()->templates->includeJsFile($api);
        craft()->templates->includeJsResource('smartmap/js/address-fieldtype.js');
        craft()->templates->includeCssResource('smartmap/css/address-fieldtype.css');

        craft()->smartMap->loadGeoData();
        $here = craft()->smartMap->here;
        if ($here['latitude'] && $here['longitude']) {
            $hereJs = json_encode(array(
                'lat' => $here['latitude'],
                'lng' => $here['longitude'],
            ));
        } else {
            $hereJs = 'false';
        }
        craft()->templates->includeJs('here = '.$hereJs.';');

        return craft()->templates->render('smartmap/address/input', $model->getAttributes());
        
    }

    // Don't put field value into "craft_content" table
    public function defineContentAttribute()
    {
        return false;
    }

    // Field settings
    protected function defineSettings()
    {
        return array(
            //'fieldLayout' => array(AttributeType::String, 'column' => ColumnType::Text)
        );
    }

    // Field settings HTML
    public function getSettingsHtml()
    {
        craft()->templates->includeCssFile('//fonts.googleapis.com/css?family=Coming+Soon');
        craft()->templates->includeCssResource('smartmap/css/fieldtype-settings.css');
        craft()->templates->includeJsResource('smartmap/js/fieldtype-settings.js');
        return craft()->templates->render('smartmap/address/fieldtype-settings', array(
            'settings' => $this->getSettings()
        ));
    }

    // ==================================================== //

    /*
    // As the data enters the database
    public function prepValueFromPost($value)
    {
        // Called before onAfterSave() 
        return $value;
    }
    */

    // As the data leaves the database
    public function prepValue($value)
    {
        return craft()->smartMap->getAddress($this, $value);
    }

    // ==================================================== //
    // VALIDATION
    // ==================================================== //

    /**
     * Validates our fields submitted value beyond the checks 
     * that were assumed based on the content attribute.
     *
     * Returns 'true' or any custom validation errors.
     *
     * @param array $value
     * @return true|string|array
     */
    public function validate($value)
    {
        $errors = parent::validate($value);

        if (!is_array($errors))
        {
            $errors = array();
        }

        $validLat = array_key_exists('lat', $value) && (!$value['lat'] || is_numeric($value['lat']));
        $validLng = array_key_exists('lng', $value) && (!$value['lng'] || is_numeric($value['lng']));

        if (!$validLat || !$validLng)
        {
            $errors[] = Craft::t('If coordinates are specified, they must be numbers.');
        }

        if ($errors)
        {
            return $errors;
        }
        else
        {
            return true;
        }
    }

    // ==================================================== //
    // EVENTS
    // ==================================================== //

    // 
    //public function onBeforeSave() {}

    // 
    //public function onAfterSave() {}

    // After saving element, save field to plugin table
    public function onAfterElementSave()
    {
        // Returns true if entry was saved
        return craft()->smartMap->saveAddressField($this);
    }

}