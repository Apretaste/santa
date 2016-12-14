<?php

require_once "/var/www/Core/services/santa/clases/ProvinceLocation.php";


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
		$province = new  \Santa\Clases\ProvinceLocation('La habana', 'Cuba', 1111112, 444444443, 5, 'LH');
		$province1 = new \Santa\Clases\ProvinceLocation('Pinar del Rio', 'Cuba', 1111112, 444444443, 6, 'PR');
		$province2 = new \Santa\Clases\ProvinceLocation('Matanzas', 'Cuba', 1111111, 444444444, 2, 'M');
		$province3 = new \Santa\Clases\ProvinceLocation('Mayabeque', 'Cuba', 1111111, 444444444, 7, 'MY');
		$province4 = new \Santa\Clases\ProvinceLocation('Artemisa', 'Cuba', 1111111, 444444444, 13, 'A');
		$province5 = new \Santa\Clases\ProvinceLocation('Sancti Spiritus', 'Cuba', 1111111, 444444444, 15, 'SS');
		$province6 = new \Santa\Clases\ProvinceLocation('Villa Clara', 'Cuba', 1111111, 444444444, 21, 'VC');
		$province7 = new \Santa\Clases\ProvinceLocation('Camagüey', 'Cuba', 1111111, 444444444, 14, 'C');
		$province8 = new \Santa\Clases\ProvinceLocation('Guantanamo', 'Cuba', 1111111, 444444444, 18, 'G');
		$province9 = new \Santa\Clases\ProvinceLocation('Santiago de Cuba', 'Cuba', 1111111, 444444444, 10, 'SC');
		$province10 = new \Santa\Clases\ProvinceLocation('Las Tunas', 'Cuba', 1111111, 444444444, 11, 'LT');
		$province11 = new \Santa\Clases\ProvinceLocation('Holguin', 'Cuba', 1111111, 444444444, 17, 'H');
		$province12 = new \Santa\Clases\ProvinceLocation('Cienfuegos', 'Cuba', 1111111, 444444444, 23, 'CF');
		$province13 = new \Santa\Clases\ProvinceLocation('Ciego de Avila', 'Cuba', 1111111, 444444444,24, 'CA');
		$province14 = new \Santa\Clases\ProvinceLocation('Isla de la Juventud', 'Cuba', 1111111, 444444444, 12, 'IJ');
		array_push($this->provinces_location, $province);
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

	public function _main(Request $request)
	{
		$responseContent = [];
		$prov = null;
		$response = new Response();

		if (!empty($request->query)) {
			$prov = $this->find_province($request->query);
			if ($prov == null) {
				$response->setResponseSubject("Debe entrar la provincia para ubicar a Santa");
				$response->createFromText("Usted no ha insertado ninguna provincia para ubicar a Santa. Inserte el texto en el asunto del email, justo despu&eacute;s de la palabra Santa.<br/><br/>Por ejemplo: Asunto: <b>SANTA LH</b>");
				$responseContent = array(
					"province" => $prov

				);
			}

			else {
				$responseContent = array(
					"province" => $prov
				);
			}
		}

/*		elseif (!empty($request->query)) {
			$prov = $this->info($request->query);
			if ($prov) {
				$response->setResponseSubject("Informacion de las Provincias");
				$response->createFromText("Informacion de las provincias");
				$responseContent = array(
					"info" => $prov

				);
			}
		}*/
		else
		{
			$response->setResponseSubject("Debe entrar la provincia para ubicar a Santa");
			$response->createFromText("Usted no ha insertado ninguna provincia para ubicar a Santa. Inserte el texto en el asunto del email, justo despu&eacute;s de la palabra Santa.<br/><br/>Por ejemplo: Asunto: <b>SANTA LH</b>");
			$responseContent = array(
				"province" => $prov
			);
		}
		// create the response
		$response = new Response();
		$response->setResponseSubject("Santa Tracker " . $request->query);
		$response->createFromTemplate("basic.tpl", $responseContent,'image');

		return $response;
	}


}
