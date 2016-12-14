<?php
namespace Santa\Clases;

class ProvinceLocation
{
    public $name;
    public $code;
    public $country;
    public $lat;
    public $long;
    public $time_zone;

    /**
     * ProvinceLocation constructor.
     * @param $name
     * @param $code
     * @param $country
     * @param $lat
     * @param $long
     * @param $time_zone
     */
    public function __construct($name,$country, $lat, $long, $time_zone,$code)
    {
        $this->name = $name;
        $this->code = $code;
        $this->country = $country;
        $this->lat = $lat;
        $this->long = $long;
        $this->time_zone = $time_zone;
    }


}