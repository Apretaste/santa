<?php

class Santa extends Service
{
    /**
     * Function executed when the service is called
     *
     * @param Request
     * @return Response
     * */
    private $provinces_location = [];

    public function __construct()
    {
        require_once "{$this->pathToService}/clases/ProvinceLocation.php";

        $province1 = new \Santa\Clases\ProvinceLocation('Pinar del Rio', 'Cuba', 22.407626, -83.847302, 4, 'PR');
        $province2 = new \Santa\Clases\ProvinceLocation('Artemisa', 'Cuba', 22.777996, -82.891211, 4, 'A');
        $province3 = new \Santa\Clases\ProvinceLocation('Mayabeque', 'Cuba', 22.880357, -82.018741, 4, 'MY');
        $province4 = new  \Santa\Clases\ProvinceLocation('La habana', 'Cuba', 23.113593, -82.366596, 4, 'LH');
        $province5 = new \Santa\Clases\ProvinceLocation('Matanzas', 'Cuba', 21.025247, -79.994713, 4, 'M');
        $province6 = new \Santa\Clases\ProvinceLocation('Cienfuegos', 'Cuba', 21.025247, -79.994713, 4, 'CF');
        $province7 = new \Santa\Clases\ProvinceLocation('Villa Clara', 'Cuba', 37.370905, -121.967552, 4, 'VC');
        $province8 = new \Santa\Clases\ProvinceLocation('Ciego de Avila', 'Cuba', 20.885918, -121.967552, 4, 'CA');
        $province9 = new \Santa\Clases\ProvinceLocation('Sancti Spiritus', 'Cuba', 21.938149, -79.444370, 4, 'SS');
        $province10 = new \Santa\Clases\ProvinceLocation('Camagüey', 'Cuba', 21.386298, -77.897357, 4, 'C');
        $province11 = new \Santa\Clases\ProvinceLocation('Las Tunas', 'Cuba', 20.967876, -76.957324, 4, 'LT');
        $province12 = new \Santa\Clases\ProvinceLocation('Holguin', 'Cuba', 20.896610, -76.262497, 4, 'HO');
        $province13 = new \Santa\Clases\ProvinceLocation('Santiago de Cuba', 'Cuba', 20.024440, -75.826096, 4, 'SC');
        $province14 = new \Santa\Clases\ProvinceLocation('Guantanamo', 'Cuba', 20.147004, -77.897357, 4, 'G');
        $province15 = new \Santa\Clases\ProvinceLocation('Granma', 'Cuba', 20.147004, -77.897357, 4, 'GR');
        $province16 = new \Santa\Clases\ProvinceLocation('Isla de la Juventud', 'Cuba', 21.691097, -82.866640, 4, 'IJ');

        //Aqui inserto los estados foraneos de cuba Ej San Francisco, tiene q ser 22 uno para cada hora si insertar latitud y long
        $provincee1 = new \Santa\Clases\ProvinceLocation('Pinar del Rio', 'Cuba', 6, 0, 1, 'PR');
        $provincee2 = new  \Santa\Clases\ProvinceLocation('La habana', 'Cuba', 0, 0, 3, 'LH');
        $provincee3 = new \Santa\Clases\ProvinceLocation('Matanzas', 'Cuba', 0, 0, 3, 'M');
        $provincee4 = new \Santa\Clases\ProvinceLocation('Mayabeque', 'Cuba', 0, 0, 3, 'MY');
        $provincee5 = new \Santa\Clases\ProvinceLocation('Artemisa', 'Cuba', 0, 0, 3, 'A');
        $provincee6 = new \Santa\Clases\ProvinceLocation('Sancti Spiritus', 'Cuba', 0, 0, 3, 'SS');
        $provincee7 = new \Santa\Clases\ProvinceLocation('Villa Clara', 'Cuba', 0, 0, 3, 'VC');
        $provincee8 = new \Santa\Clases\ProvinceLocation('Camagüey', 'Cuba', 0, 0, 3, 'C');
        $provincee9 = new \Santa\Clases\ProvinceLocation('Guantanamo', 'Cuba', 0, 0, 3, 'G');
        $provincee10 = new \Santa\Clases\ProvinceLocation('Santiago de Cuba', 'Cuba', 0, 0, 3, 'SC');
        $provincee11 = new \Santa\Clases\ProvinceLocation('Las Tunas', 'Cuba', 0, 0, 3, 'LT');
        $provincee12 = new \Santa\Clases\ProvinceLocation('Pinar del Rio', 'Cuba', 6, 0, 1, 'PR');
        $provincee13 = new  \Santa\Clases\ProvinceLocation('La habana', 'Cuba', 0, 0, 3, 'LH');
        $provincee14 = new \Santa\Clases\ProvinceLocation('Matanzas', 'Cuba', 0, 0, 3, 'M');
        $provincee15 = new \Santa\Clases\ProvinceLocation('Mayabeque', 'Cuba', 0, 0, 3, 'MY');
        $provincee16 = new \Santa\Clases\ProvinceLocation('Artemisa', 'Cuba', 0, 0, 3, 'A');
        $provincee17 = new \Santa\Clases\ProvinceLocation('Sancti Spiritus', 'Cuba', 0, 0, 3, 'SS');
        $provincee18 = new \Santa\Clases\ProvinceLocation('Villa Clara', 'Cuba', 0, 0, 3, 'VC');
        $provincee19 = new \Santa\Clases\ProvinceLocation('Camagüey', 'Cuba', 0, 0, 3, 'C');
        $provincee20 = new \Santa\Clases\ProvinceLocation('Guantanamo', 'Cuba', 0, 0, 3, 'G');
        $provincee21 = new \Santa\Clases\ProvinceLocation('Santiago de Cuba', 'Cuba', 0, 0, 3, 'SC');
        $provincee22 = new \Santa\Clases\ProvinceLocation('Las Tunas', 'Cuba', 0, 0, 3, 'LT');


        array_push($this->provinces_location, $province1);
        array_push($this->provinces_location, $province2);
        array_push($this->provinces_location, $province3);
        array_push($this->provinces_location, $province4);
        array_push($this->provinces_location, $province5);
        array_push($this->provinces_location, $province6);
        array_push($this->provinces_location, $province7);
        array_push($this->provinces_location, $province8);
        array_push($this->provinces_location, $province9);
        array_push($this->provinces_location, $province10);
        array_push($this->provinces_location, $province11);
        array_push($this->provinces_location, $province12);
        array_push($this->provinces_location, $province13);
        array_push($this->provinces_location, $province14);
        array_push($this->provinces_location, $province15);
        array_push($this->provinces_location, $province16);

        //Inserto los estados de afuera
    }

    public function find_province($name)
    {
        $found = null;

        $i = 0;
        While ($found == null && $i < count($this->provinces_location)) {
            $prov = $this->provinces_location[$i];
            if ($prov->code == $name || $prov->name == $name) {
                $found = $prov;
            }
            $i++;
        }
        return $found;
    }

    public function find_province_pos($name)
    {
        $found = null;

        $i = 0;
        While ($found == null && $i < count($this->provinces_location)) {
            $prov = $this->provinces_location[$i];
            if ($prov->code == $name || $prov->name == $name) {
                $found = $i;
            }
            $i++;
        }
        return $found;
    }

    /* //Para que muestre toda la info de las provincias con sus codigos...
      public function info($code){

          $i=0;

          foreach ($this->provinces_location[$i] as $prov){
             if($prov->code=="INFO")
             {
                 $prov = $this->provinces_location[$code];
             }
             $i++;
         }

          return $prov;
      }
  */
    public function obtain_coordinate()
    {
        // include google maps library
        require_once "{$this->pathToService}/lib/GoogleStaticMap.php";
        require_once "{$this->pathToService}/lib/GoogleStaticMapFeature.php";
        require_once "{$this->pathToService}/lib/GoogleStaticMapFeatureStyling.php";
        require_once "{$this->pathToService}/lib/GoogleStaticMapMarker.php";
        require_once "{$this->pathToService}/lib/GoogleStaticMapPath.php";
        require_once "{$this->pathToService}/lib/GoogleStaticMapPathPoint.php";

        // get and clean the argument
        $argument = $request->query;
        $argument = str_replace("\n", " ", $argument);
        $argument = str_replace("\r", "", $argument);
        $argument = trim(strtolower($argument));

        // detecting type
        $type = 'hibrido';
        $internalType = "hybrid";

        if (stripos($argument, 'fisico') !== false) {
            $type = 'fisico';
            $internalType = "satellite";
        } elseif (stripos($argument, 'politico') !== false) {
            $type = 'politico';
            $internalType = "roadmap";
        } elseif (stripos($argument, 'terreno') !== false) {
            $type = 'terreno';
            $internalType = "terrain";
        }

        // remove the type from the query to display on the template
        $argument = str_ireplace($type, '', $argument);

        // detecting zoom
        $zoom = null;
        for ($i = 22; $i >= 1; $i--) {
            if (stripos($argument, $i . 'x') !== false) {
                $zoom = $i;
                $argument = str_ireplace("{$i}x", '', $argument);
            }
        }

        // remove bad starting arguments
        if (substr($argument, 0, 3) == 'de ') $argument = substr($argument, 3);
        if (substr($argument, 0, 4) == 'del ') $argument = substr($argument, 4);

        // create the map
        $oStaticMap = new GoogleStaticMap();
        $oStaticMap->setScale(1);
        $oStaticMap->setHeight(400);
        $oStaticMap->setWidth(400);
        $oStaticMap->setLanguage("es");
        $oStaticMap->setHttps(true);
        $oStaticMap->setMapType($internalType);
        if (!is_null($zoom)) $oStaticMap->setZoom($zoom);
        $oStaticMap->setCenter($argument);

        // get path to the www folder
        $di = \Phalcon\DI\FactoryDefault::getDefault();
        $wwwroot = $di->get('path')['root'];

        // save the image as a temp file
        $mapImagePath = "$wwwroot/temp/" . $this->utils->generateRandomHash() . ".jpg";
        $content = file_get_contents($oStaticMap);
        file_put_contents($mapImagePath, $content);

        // optimize the image
        $this->utils->optimizeImage($mapImagePath);

        // create the response variables

// do not allow blank searches

    }

    public function get_timezone_offset($remote_tz, $origin_tz = null)
    {
        if ($origin_tz === null) {
            if (!is_string($origin_tz = date_default_timezone_get())) {
                return false; // A UTC timestamp was returned -- bail out!
            }
        }
        $origin_dtz = new DateTimeZone($origin_tz);
        $remote_dtz = new DateTimeZone($remote_tz);
        $origin_dt = new DateTime("now", $origin_dtz);
        $remote_dt = new DateTime("now", $remote_dtz);
        $offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
        return $offset;
    }

    public function _main(Request $request)
    {
//        $offset = $this->get_timezone_offset('America/New_York');
//        echo $offset/3600;
//        die;
        $responseContent = [];
        $prov = null;
        $template='basic.tpl';
        $response = new Response();
        $year = date('Y');
        if (date('Y-m-d') < date('Y-m-d', strtotime($year . '-12-24'))) {

            $responseContent = array(
                "province" => $prov,
                "intime" => false,
                "message" => "Santa no ha partido de su casa en el Polo Norte. Esta durmiendo.",
                "image" => "image/sleeping.jpg",
            );
        }
        if (date('Y-m-d') > date('Y-m-d', strtotime($year . '-12-24'))) {
            $responseContent = array(
                "province" => $prov,
                "intime" => false,
                "message" => "Santa regreso a su casa en el Polo Norte hasta las proximas navidades!!!.",
                "image" => "image/santa_go.jpg",
            );
        }
        if (date('Y-m-d') == date('Y-m-d', strtotime($year . '-12-24'))){
            $hora =10; //date('h');//<-- Calcular si la hora -5:00 EUA-Canada esta en las 10 de la noche
            if ($hora == 10) {
                if (!empty($request->query)) {
                    $prov = $this->find_province($request->query);
                    $pos_province_sol = $this->find_province_pos($request->query);
                    $minutos = date('i');
                    $pos_santa = round($minutos / 4 - 1);
                    $prov_santa = $this->provinces_location[$pos_santa];
                    $message = "";
                    if ($pos_santa > $pos_province_sol) {
                        //Ya santa se fue
                        $message = "Santa se encuentra en la provincia " . $prov_santa->name . " ya santa ha pasado por su provincia";
                    }
                    if ($pos_santa < $pos_province_sol) {
                        //Santa no ha llegado
                        $minutosfalt = ($pos_province_sol - $pos_santa) * 4;
                        $message = "Santa se encuentra en la provincia " . $prov_santa->name . " no ha llegado, llega en " . $minutosfalt . " minutos";

                    }
                    if ($pos_santa == $pos_province_sol) {
                        //Santa esta en tu provincia
                        $message = "Enhorabuena!! Santa se encuentra en su provincia";

                    }
                    if ($prov == null) {
                        $response->setResponseSubject("Debe entrar la provincia para ubicar a Santa");
                        $response->createFromText("Usted no ha insertado ninguna provincia para ubicar a Santa. Inserte el texto en el asunto del email, justo despu&eacute;s de la palabra Santa.<br/><br/>Por ejemplo: Asunto: <b>SANTA LH</b>");
                        $responseContent = array(
                            "province" => $this->provinces_location,
                            "intime" => true,
                            "titulo" => 'Debe insertar una provincia',
                        );
                        $template='lista.tpl';
                    } else {
                        $response->setResponseSubject($message);
                        $responseContent = array(
                            "province" => $prov,
                            "message" => $message,
                            "intime" => true,
                            "image" => "image/sleeping.jpg"
                        );
                    }
                }
                else
                {
                    $response->setResponseSubject("Debe entrar la provincia para ubicar a Santa");
                    $response->createFromText("Usted no ha insertado ninguna provincia para ubicar a Santa. Inserte el texto en el asunto del email, justo despu&eacute;s de la palabra Santa.<br/><br/>Por ejemplo: Asunto: <b>SANTA LH</b>");
                    $responseContent = array(
                        "province" => $this->provinces_location,
                        "intime" => true,
                        "titulo" => 'Debe insertar una provincia',
                    );
                    $template='lista.tpl';
                }
            } else {
                if ($hora > 10) {
                    $responseContent = array(
                        "province" => $prov,
                        'message'=>'Santa se a ido de su localidad para repartir regalos a los demas niños!!!',
                        "intime" => false,
                        "image" => "image/santa_go.jpg"
                    );
                }
                if ($hora < 10) {
                    $responseContent = array(
                        "province" => $prov,
                        'message'=>'Santa no ha llegado a su localidad, se encuentra en .....',
                        "intime" => false,
                        "image" => "image/sleeping.jpg"
                    );
                }
            }

        }
        
        $response = new Response();
        $response->setResponseSubject("Santa Tracker " . $request->query);
        $response->createFromTemplate($template, $responseContent, 'image');

        return $response;
    }


}

