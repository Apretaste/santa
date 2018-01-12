<?php

/**
 * Apretaste!
 *
 * Santa Service
 *
 * @author kuma <@kumahacker>
 * @version 1.0
 */
class Santa extends Service
{
	public function _main(Request $request)
	{
		date_default_timezone_set("America/New_York");

		// init variables
		$year = date('Y');
		$today = date('Y-m-d');
		$the_day = date('Y-m-d', strtotime($year . "-12-24"));
		$end_year = date('Y-m-d', strtotime($year . "-12-31"));
		$hora = date('G');

		// get user profile
		$person = $this->utils->getPerson($request->email);

		// load santa tracker
		$santa_stops = json_decode(file_get_contents($this->pathToService . "/santa_stops.json"));
		$santa_stops = $santa_stops->stops;

		// provinces dictionary
		$provs = array('PINAR_DEL_RIO', 'LA_HABANA', 'ARTEMISA', 'MAYABEQUE',
			'MATANZAS', 'VILLA_CLARA', 'CIENFUEGOS', 'SANCTI_SPIRITUS', 'CIEGO_DE_AVILA', 'CAMAGUEY',
			'LAS_TUNAS', 'HOLGUIN', 'GRANMA', 'SANTIAGO_DE_CUBA', 'GUANTANAMO', 'ISLA_DE_LA_JUVENTUD'
		);

		$synon = array();
		foreach ($provs as $v) {
			$v = str_replace('_', ' ', $v);
			$synon[$v] = $v;
		}

		// user has no province in their profile
		if (empty($person->province)) {
			$prov = str_replace(' ', '_', trim($request->query));
			while (strpos($prov, '__') !== false)
				$prov = str_replace('__', '_', $prov);

			if (empty($prov)) {
				$response = new Response();
				$response->setResponseSubject("Santa no sabe donde vives");
				$response->createFromTemplate("need_province.tpl", [
					"person" => $person,
					"list" => $synon
				]);
				return $response;
			}

			foreach ($provs as $item) {
				if (strtolower($item) == strtolower($prov)) {
					// update profile
					$connection = new Connection();
					$connection->deepQuery("UPDATE person SET province = '$item' WHERE email = '{$person->email}';");
					$person->province = $item;
					break;
				}
			}
		}

		// Es navidad
		if ($today == $the_day) {
			$user_pos = 9999;
			$user_stop = null;
			$user_hour = null;
			$user_minute = null;

			$i = 0;
			foreach ($santa_stops as $stop) {
				if ($stop->country == 'Cuba')
					if ($stop->name == $person->province) {
						$user_pos = $i;
						$user_stop = $stop;
						$user_hour = $user_stop->hour;
						$user_minute = explode('-', $user_stop->minutes);
						$user_minute = intval($user_minute[0]);
					}
				$i++;
			}

			$i = 0;
			foreach ($santa_stops as $stop) {
				if ($stop->hour == $hora) {
					$minutos = date('i');
					$arr = explode('-', $stop->minutes);
					$min_from = intval($arr[0]);
					$min_to = intval($arr[1]);

					if ($minutos >= $min_from && $minutos <= $min_to) {

						// include google maps library
						require_once "{$this->pathToService}/lib/GoogleStaticMap.php";
						require_once "{$this->pathToService}/lib/GoogleStaticMapFeature.php";
						require_once "{$this->pathToService}/lib/GoogleStaticMapFeatureStyling.php";
						require_once "{$this->pathToService}/lib/GoogleStaticMapMarker.php";
						require_once "{$this->pathToService}/lib/GoogleStaticMapPath.php";
						require_once "{$this->pathToService}/lib/GoogleStaticMapPathPoint.php";

						$oStaticMap = new GoogleStaticMap();
						$oStaticMap->setScale(1);
						$oStaticMap->setHeight(300);
						$oStaticMap->setWidth(300);
						$oStaticMap->setLanguage("es");
						$oStaticMap->setHttps(true);
						$oStaticMap->setMapType('hybrid');
						$oStaticMap->setZoom(14);

						if ($stop->country == 'Cuba') {
							$oStaticMap->setCenter("{$stop->lat},{$stop->long}");
							$marker = new GoogleStaticMapMarker([
								"color" => "FF0000",
								"label" => "Santa",
								"latitude" => $stop->lat,
								"longitude" => $stop->long
							]);

							$oStaticMap->setMarker($marker);
						}
						else $oStaticMap->setCenter(html_entity_decode($stop->name)." ".html_entity_decode($stop->country));

						// get path to the www folder
						$di = \Phalcon\DI\FactoryDefault::getDefault();
						$www_root = $di->get('path')['root'];

						// save image as a png file
						$content = file_get_contents($oStaticMap);
						$mapImagePath = "$www_root/temp/" . $this->utils->generateRandomHash() . ".png";
						imagepng(imagecreatefromstring($content), $mapImagePath);

						$city =  str_replace("_", " ", $stop->name);

						// generate random santa's message
						$messages = [
							"Le estoy dando zanahorias a Rodolfo en la ciudad de {$city} en {$stop->country}, el pobre ha pasado hambre desde el Polo Norte!!!",
							"Santa est&aacute; comiendo galletas de {$stop->country}, que le regalaron en {$city}",
							"Uff que fr&iacute;o hace aqu&iacute; arriba en {$city}. Ojal&aacute; que {$person->full_name} me haya dejado chocolate caliente de {$stop->country}!!!",
							"Mi esposa le hizo esta bufanda especialmente a {$person->full_name}, y espero que le guste mucho. Estoy llegando a {$city}  :)",
							"Me encanta venir a {$city} porque siempre me dejan mucha comida.",
							"Cargar este saco lleno de regalos por todo {$city} me dejar&aacute; tremendo dolor en la espalda!!!",
							"&iquest;Dónde habr&aacute; algo de leche fr&iacute;a en {$city}?",
							"Espero que {$person->full_name} est&eacute; durmiendo porque estoy muy gordo para salir corriendo por todo {$city}"
						];

						$msg_rand = $messages[mt_rand(0,count($messages)-1)];

						// santa no ha llegado
						if ($user_pos > $i) {
							$time_left = null;

							if (!is_null($user_stop)) {
								$now_hour = $stop->hour;
								$time_left = ($user_hour * 60 + $user_minute) - ($now_hour * 60 + $minutos);
								$hours_left = intval($time_left / 60);
								$min_left = $time_left - $hours_left * 60;
								$time_left_str = ($hours_left > 0?"$hours_left horas y ":"")."$min_left minutos";
							}

							if ($stop->country == 'Cuba') {
								$response = new Response();
								$response->setResponseSubject('Santa no ha llegado a tu provincia');
								$response->createFromTemplate('basic.tpl', array(
									'message' => "$msg_rand. Llegar&eacute; a tu provincia en <b>$time_left_str</b>.",
									"image" => $mapImagePath
								), [$mapImagePath]);
								return $response;
							} else {
								$response = new Response();
								$response->setResponseSubject('Santa no ha llegado a tu localidad');
								$response->createFromTemplate('basic.tpl', array(
									'message' => "$msg_rand. Llegar&eacute; a tu provincia en <b>$time_left_str</b>.",
									"image" => $mapImagePath
								), [$mapImagePath]);
								return $response;
							}
						}

						// santa esta aqui
						if ($user_pos == $i) {
							$response = new Response();
							$response->setResponseSubject('Santa ha llegado a tu provincia');
							$response->createFromTemplate('basic.tpl', array(
								'message' => "Estoy en tu provincia !!!. $msg_rand. ",
								"image" => $mapImagePath
							), [$mapImagePath]);
							return $response;
						}

						// santa se ha ido
						if ($user_pos < $i) {
							$response = new Response();
							$response->setResponseSubject('Santa se ha ido de tu localidad');
							$response->createFromTemplate('basic.tpl', array(
								'message' => "Me he ido de tu localidad para repartir regalos a los dem&aacute;s ni&ntilde;os !!!. $msg_rand. ",
								"image" => $mapImagePath
							), [$mapImagePath]);
							return $response;
						}
					}
				}
				$i++;
			}
		}

		// Si es despues de navidad o ya paso por todas las ciudades (no se dio return en el ciclo anterior)
		if (($today == $the_day && $hora >= $santa_stops[0]->hour) || ($today > $the_day && $today <= $end_year))
		{
			$response = new Response();
			$response->setResponseSubject("Santa ha regresado a su casa en el Polo Norte");
			$imgPath = $this->pathToService."/image/go.jpg";
			$response->createFromTemplate("basic.tpl", array(
				"message" => "Regres&eacute; a mi casa en el Polo Norte hasta las pr&oacute;ximas navidades !!!.",
				"image" => $imgPath,
			), [$imgPath]);
			return $response;
		}



		// Es antes de navidad
		$datetime1 = new DateTime($today);
		$datetime2 = new DateTime($the_day);
		$interval = $datetime2->diff($datetime1);
		$dias = $interval->format('%a');
		$response = new Response();
		$response->setResponseSubject("Santa no ha partido de su casa en el Polo Norte");
		
		$imgPath = $this->pathToService."/image/sleeping.jpg";

		$response->createFromTemplate('basic.tpl', array(
			"message" => "Estoy durmiendo en mi casa en el Polo Norte. ". (($dias > 1)?"Faltan $dias dias para navidad !!!":"Ma&ntilde;ana ser&aacute; navidad !!!"),
			"image" => $imgPath,
		), [$imgPath]);

		return $response;
	}
}
