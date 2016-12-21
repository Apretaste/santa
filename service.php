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

        $province1 = new \Santa\Clases\ProvinceLocation('PINAR_DEL_RIO', 'Cuba', 22.407626, -83.847302, 4, 'PR');
        $province2 = new \Santa\Clases\ProvinceLocation('ARTEMISA', 'Cuba', 22.777996, -82.891211, 4, 'A');
        $province3 = new \Santa\Clases\ProvinceLocation('MAYABEQUE', 'Cuba', 22.880357, -82.018741, 4, 'MY');
        $province4 = new  \Santa\Clases\ProvinceLocation('LA_HABANA', 'Cuba', 23.113593, -82.366596, 4, 'LH');
        $province5 = new \Santa\Clases\ProvinceLocation('MATANZAS', 'Cuba', 21.025247, -79.994713, 4, 'M');
        $province6 = new \Santa\Clases\ProvinceLocation('CIENFUEGOS', 'Cuba', 21.025247, -79.994713, 4, 'CF');
        $province7 = new \Santa\Clases\ProvinceLocation('VILLA_CLARA', 'Cuba', 37.370905, -121.967552, 4, 'VC');
        $province8 = new \Santa\Clases\ProvinceLocation('CIEGO_DE_AVILA', 'Cuba', 20.885918, -121.967552, 4, 'CA');
        $province9 = new \Santa\Clases\ProvinceLocation('SANCTI_SPIRITUS', 'Cuba', 21.938149, -79.444370, 4, 'SS');
        $province10 = new \Santa\Clases\ProvinceLocation('CAMAGÜEY', 'Cuba', 21.386298, -77.897357, 4, 'C');
        $province11 = new \Santa\Clases\ProvinceLocation('LAS_TUNAS', 'Cuba', 20.967876, -76.957324, 4, 'LT');
        $province12 = new \Santa\Clases\ProvinceLocation('HOLGUIN', 'Cuba', 20.896610, -76.262497, 4, 'HO');
        $province13 = new \Santa\Clases\ProvinceLocation('SANTIAGO_DE_CUBA', 'Cuba', 20.024440, -75.826096, 4, 'SC');
        $province14 = new \Santa\Clases\ProvinceLocation('GUANTANAMO', 'Cuba', 20.147004, -77.897357, 4, 'G');
        $province15 = new \Santa\Clases\ProvinceLocation('GRANMA', 'Cuba', 20.147004, -77.897357, 4, 'GR');
        $province16 = new \Santa\Clases\ProvinceLocation('ISLA_DE_LA_JUVENTUD', 'Cuba', 21.691097, -82.866640, 4, 'IJ');


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
            if (strtoupper($prov->code) == strtoupper($name) || strtoupper($prov->name) == strtoupper($name)) {
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
        $argument = 'satellite';
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


    public function province_santa($prov)
    {
        $pos_province_sol = $this->find_province_pos($prov->name);
        $minutos = date('i');
        $message = '';
        $pos_santa = round($minutos / 4 - 1);
        $prov_santa = $this->provinces_location[$pos_santa];
        $message = "";
        if ($pos_santa > $pos_province_sol) {
            //Ya santa se fue
            $message = "Santa se encuentra en la provincia " . $prov_santa->name . " ya santa ha pasado por su provincia";
            $image='image/isla_cuba.jpg';
        }

        if ($pos_santa < $pos_province_sol) {
            //Santa no ha llegado
            $minutosfalt = ($pos_province_sol - $pos_santa) * 4;
            $message = "Santa se encuentra en la provincia " . $prov_santa->name . " no ha llegado, llega en " . $minutosfalt . " minutos";
            $image='image/isla_cuba.jpg';

        }
        if ($pos_santa == $pos_province_sol) {
            //Santa esta en tu provincia
            $message = "Enhorabuena!! Santa se encuentra en su provincia";
            $image='image/santa_here.jpg';

        }
        $response= new stdClass();
        $response->message=$message;
        $response->image= $image;
        return $response;
    }


    public function _main(Request $request)
    {

        $states = [];
        //Listado de paises para cdo Santa no este en cuba ubicar a santa en dependencia de la hora
        $province1 = new \Santa\Clases\ProvinceLocation('Japon', 'Japon', 33.4644908, 116.2318414, 0, '0');
        $province2 = new \Santa\Clases\ProvinceLocation('China', 'China', 34.525143, 86.013458, 0, '0');
        $province3 = new \Santa\Clases\ProvinceLocation('Rusia', 'Rusia', 22.880357, -82.018741, 0, '0');
        $province4 = new  \Santa\Clases\ProvinceLocation('Polonia', 'Polonia    ', 51.8402565, 14.64649, 0, '0');
        $province5 = new \Santa\Clases\ProvinceLocation('Alemania', 'Alemania', 50.7688441, 8.2304743, 0, '0');
        $province6 = new \Santa\Clases\ProvinceLocation('Suiza', 'Suiza', 48.2148625, 6.077154, 0, '0');
        $province7 = new \Santa\Clases\ProvinceLocation('Francia', 'Francia ', 45.8777618, -6.7773685, 0, '0');
        $province8 = new \Santa\Clases\ProvinceLocation('Italia', 'Italia', 40.9576498, 3.5747673, 0, '0');
        $province9 = new \Santa\Clases\ProvinceLocation('España', 'España', 39.8757545, -12.7145881, 0, '0');
        $province10 = new \Santa\Clases\ProvinceLocation('Reino Unido', 'Reino Unido', 39.8757545, -12.7145881, 0, '0');
        $province11 = new \Santa\Clases\ProvinceLocation('Groenlandia', 'Groenlandia', 61.4938353, -55.1075416, 0, '0');
        $province12 = new \Santa\Clases\ProvinceLocation('Canada', 'Canada', 50.8464613, -130.235056, 0, '0');
        $province13 = new \Santa\Clases\ProvinceLocation('Michigan', 'EUA', 44.8632388, -90.9065791, 0, '0');
        $province14 = new \Santa\Clases\ProvinceLocation('Illinois', 'EUA', 39.6611311, -93.9969731, 0, '0');
        $province15 = new \Santa\Clases\ProvinceLocation('Kentucky', 'EUA', 37.8053491, -88.0122994, 0, '0');
        $province16 = new \Santa\Clases\ProvinceLocation('Carolina del Norte', 'EUA', 35.1981563, -82.1354637, 0, '0');
        $province17 = new \Santa\Clases\ProvinceLocation('Carolina del Sur', 'EUA', 33.6092884, -83.1913434, 0, '0');
        $province18 = new \Santa\Clases\ProvinceLocation('Georgia', 'EUA', 32.6627763, -85.4676136, 4, '0');
        $province19 = new \Santa\Clases\ProvinceLocation('Orlando Florida', 'EUA', 28.4813989, -81.5088382, 0, '0');
        $province20 = new \Santa\Clases\ProvinceLocation('Tampa Florida', 'EUA', 33.6092884, -83.1913434, 0, '0');
        $province21 = new \Santa\Clases\ProvinceLocation('Miami Beach', 'EUA', 25.814092, -80.2143476, 12, 0, '0');
        $province22 = new \Santa\Clases\ProvinceLocation('Miami Florida', 'EUA', 25.7824618, -80.3010444, 0, '0');


        array_push($states, $province1);
        array_push($states, $province2);
        array_push($states, $province3);
        array_push($states, $province4);
        array_push($states, $province5);
        array_push($states, $province6);
        array_push($states, $province7);
        array_push($states, $province8);
        array_push($states, $province9);
        array_push($states, $province10);
        array_push($states, $province11);
        array_push($states, $province12);
        array_push($states, $province13);
        array_push($states, $province14);
        array_push($states, $province15);
        array_push($states, $province16);
        array_push($states, $province17);
        array_push($states, $province18);
        array_push($states, $province19);
        array_push($states, $province20);
        array_push($states, $province21);
        array_push($states, $province22);


        $responseContent = [];
        $prov = null;
        $template = 'basic.tpl';
        $response = new Response();
        $year = date('Y');
        //Si es antes del 24
        if (date('Y-m-d') < date('Y-m-d', strtotime($year . '-12-24'))) {

            $datetime1 = new DateTime(date('Y-m-d'));
            $datetime2 = new DateTime(date('Y-m-d', strtotime($year . '-12-24')));
            $interval = $datetime2->diff($datetime1);
             $dias=$interval->format('%a');
            $responseContent = array(
                "province" => $prov,
                "intime" => false,
                "message" => "Santa no ha partido de su casa en el Polo Norte. Esta durmiendo.Faltan $dias dias para navidad!!!",
                "image" => "image/sleeping.jpg",
            );
        }
        //Si es despues del 24
        if (date('Y-m-d') > date('Y-m-d', strtotime($year . '-12-24'))) {
            $responseContent = array(
                "province" => $prov,
                "intime" => false,
                "message" => "Santa regreso a su casa en el Polo Norte hasta las proximas navidades!!!.",
                "image" => "image/santa_go.jpg",
            );
        }
        //Si es el 24
        if (date('Y-m-d') == date('Y-m-d', strtotime($year . '-12-24'))){
            $hora = date('G');//<-- Calcular si la hora -5:00 EUA-Canada esta en las 10 de la noche
            //Si es las 10 de la noche
            if ($hora==22) {
                if (!empty($request->query)) {
                    $prov = $this->find_province($request->query);
                    $result = $this->province_santa($prov);
                    //Si entro la provincia mal
                    if ($prov == null) {
                        $response->setResponseSubject("Debe entrar la provincia para ubicar a Santa");
                        $response->createFromText("Usted no ha insertado ninguna provincia para ubicar a Santa. Inserte el texto en el asunto del email, justo despu&eacute;s de la palabra Santa.<br/><br/>Por ejemplo: Asunto: <b>SANTA LH</b>");
                        $responseContent = array(
                            "province" => $this->provinces_location,
                            "intime" => true,
                            "titulo" => 'Debe insertar una provincia',
                        );
                        $template = 'lista.tpl';
                    } else {
                        //Si esta la provincia , la funcion santa province te devuelve le mensaje
                        $response->setResponseSubject($result->message);
                        $responseContent = array(
                            "province" => $prov,
                            "message" => $result->message,
                            "intime" => true,
                            "image" => $result->image
                        );
                    }
                } else {
                    //Si no puso prov se coge primero la del user
					/*$email = $request->email;
                    $conection = new Connection();
                    $province = $conection->deepQuery("SELECT province FROM person WHERE email = '$email'");
                    $province = $province[0]->province;*/
					$province='';

                    if ($province == '') {
                        //Si no puso se le pone el listado de provinicia
                        $response->setResponseSubject("Debe entrar la provincia para ubicar a Santa");
                        $response->createFromText("Usted no ha insertado ninguna provincia para ubicar a Santa. Inserte el texto en el asunto del email, justo despu&eacute;s de la palabra Santa.<br/><br/>Por ejemplo: Asunto: <b>SANTA LH</b>");
                        $responseContent = array(
                            "province" => $this->provinces_location,
                            "intime" => true,
                            "titulo" => 'Debe insertar una provincia',
                        );
                        $template = 'lista.tpl';
                    } else {
                        $prov = $this->find_province($request->query);
                        $result = $this->province_santa($prov);
                        $response->setResponseSubject($result->message);
                        $responseContent = array(
                            "province" => $prov,
                            "message" => $result->message,
                            "intime" => true,
                            "image" => "$result->image"
                        );
                    }
                }
            } else {
                if ($hora > 10) {
                    $responseContent = array(
                        "province" => $prov,
                        'message' => 'Santa se a ido de su localidad para repartir regalos a los demas niños!!!',
                        "intime" => false,
                        "image" => "image/santa_go.jpg"
                    );
                }
                if ($hora < 10) {
                    $h = date("G");
                    $prov = $states[$h];
                    $texto = $prov->name;
                    if ($prov->name != $prov->country) {
                        $texto = $prov->name . ', ' . $prov->country;
                    }
                    $responseContent = array(
                        "province" => $prov,
                        'message' => 'Santa no ha llegado a su localidad, se encuentra en ' . $texto,
                        "intime" => false,
                        "image" => "image/santa_go.jpg"
                    );
                }
            }

        }
        if ($request->query=='info') {
            //Si pone info se le muestra el listado de las provincias
            $response->setResponseSubject("Santa info");
            $response->createFromText("Inserte el texto en el asunto del email, justo despu&eacute;s de la palabra Santa.<br/><br/>Por ejemplo: Asunto: <b>SANTA LH</b>");
            $responseContent = array(
                "province" => $this->provinces_location,
                "intime" => true,
                "titulo" => 'Santa info',
            );
            $template = 'lista.tpl';
        }
        $response = new Response();
        $response->setResponseSubject("Santa Tracker " . $request->query);
        $response->createFromTemplate($template, $responseContent, 'image');

        return $response;
    }


}

