<?php

namespace blakit\api\structures;

use yii\base\Model;

class LatLng extends Model
{
    public $lat;

    public $lng;

    public function rules()
    {
        return [
            [['lat', 'lng'], 'required'],
            [['lat'], 'validateLatitude'],
            [['lng'], 'validateLongitude']
        ];
    }

    function validateLatitude($attribute)
    {
        $value = str_replace(',', '.', $this->$attribute);
        if (!preg_match('/^(\+|-)?(?:90(?:(?:\.0{1,6})?)|(?:[0-9]|[1-8][0-9])(?:(?:\.[0-9]{1,20})?))$/', $value)) {
            $this->addError($attribute, 'Latitude invalid');
            return false;
        }

        return true;
    }

    function validateLongitude($attribute)
    {
        $value = str_replace(',', '.', $this->$attribute);
        if (!preg_match('/^(\+|-)?(?:180(?:(?:\.0{1,6})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,20})?))$/', $value)) {
            $this->addError($attribute, 'Longitude invalid');
            return false;
        }

        return true;
    }

    public function __toString()
    {
        return implode(';', [$this->lat, $this->lng]);
    }
}