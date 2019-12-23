<?php

use Apretaste\Model\Person;

/**
 * Apretaste!
 *
 * Santa Service
 *
 * @author  kuma <@kumahacker>
 * @version 2.0
 */
class Service {

	/**
	 * Top users
	 *
	 * @return array|mixed
	 */
	public function getTop50() {
		// top 50 usuarios
		$sqlTop = " SELECT person.username, person.first_name, person.last_name, subq2.total
 				  	FROM (
	                    SELECT subq.id_person, count(*) as total 
	                    FROM (
							SELECT id_person, date(request_date) as fecha 
							FROM delivery 
							WHERE year(request_date) = ".date('Y')."
								AND month(request_date) = 12
							GROUP BY id_person, fecha) subq
						GROUP BY subq.id_person
						ORDER BY total desc
						LIMIT 50) subq2 
					INNER JOIN person 
					ON person.id = subq2.id_person";

		$top50 = Connection::query($sqlTop);
		if (is_array($top50)) return $top50;

		return [];
	}

	/**
	 * Main action
	 *
	 * @param \Request  $request
	 * @param \Response $response
	 *
	 * @return \Response
	 * @throws \Exception
	 */
	public function _main(Request $request, Response &$response) {
		date_default_timezone_set("America/New_York");

		$top50 = $this->getTop50();

		// init variables
		$year = date('Y');
		$today = date('Y-m-d');
		$the_day = date('Y-m-d', strtotime($year."-12-24"));
		$end_year = date('Y-m-d', strtotime($year."-12-31"));
		$hora = date('G');

		// get user profile
		$person = Person::find($request->person->email);

		// load santa tracker
		$santa_stops = json_decode(file_get_contents(__DIR__."/santa_stops.json"));
		$santa_stops = $santa_stops->stops;

		// provinces dictionary
		$provs = ['PINAR_DEL_RIO', 'LA_HABANA', 'ARTEMISA', 'MAYABEQUE',
			'MATANZAS', 'VILLA_CLARA', 'CIENFUEGOS', 'SANCTI_SPIRITUS', 'CIEGO_DE_AVILA', 'CAMAGUEY',
			'LAS_TUNAS', 'HOLGUIN', 'GRANMA', 'SANTIAGO_DE_CUBA', 'GUANTANAMO', 'ISLA_DE_LA_JUVENTUD'
		];

		$synon = [];
		foreach ($provs as $v) {
			$v = str_replace('_', ' ', $v);
			$synon[$v] = $v;
		}

		// user has no province in their profile
		if (empty($person->province)) {
			$prov = str_replace(' ', '_', trim($request->input->data->query));
			while (strpos($prov, '__')!==false)
				$prov = str_replace('__', '_', $prov);

			if (empty($prov)) {
				$response->setTemplate("need_province.ejs", [
					"person" => $person,
					"list"   => $synon
				]);

				return $response;
			}

			foreach ($provs as $item) {
				if (strtolower($item)==strtolower($prov)) {
					// update profile
					Connection::query("UPDATE person SET province = '$item' WHERE email = '{$person->email}';");
					$person->province = $item;
					break;
				}
			}
		}

		// Es navidad
		if ($today==$the_day) {
			$user_pos = 9999;
			$user_stop = null;
			$user_hour = null;
			$user_minute = null;

			$i = 0;
			foreach ($santa_stops as $stop) {
				if ($stop->country=='Cuba')
					if ($stop->name==$person->province) {
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
				if ($stop->hour==$hora) {
					$minutos = date('i');
					$arr = explode('-', $stop->minutes);
					$min_from = intval($arr[0]);
					$min_to = intval($arr[1]);

					if ($minutos >= $min_from && $minutos <= $min_to) {

						// include google maps library
						require_once __DIR__."/lib/GoogleStaticMap.php";
						require_once __DIR__."/lib/GoogleStaticMapFeature.php";
						require_once __DIR__."/lib/GoogleStaticMapFeatureStyling.php";
						require_once __DIR__."/lib/GoogleStaticMapMarker.php";
						require_once __DIR__."/lib/GoogleStaticMapPath.php";
						require_once __DIR__."/lib/GoogleStaticMapPathPoint.php";

						$oStaticMap = new GoogleStaticMap();
						$oStaticMap->setScale(1);
						$oStaticMap->setHeight(300);
						$oStaticMap->setWidth(300);
						$oStaticMap->setLanguage("es");
						$oStaticMap->setHttps(true);
						$oStaticMap->setMapType('hybrid');
						$oStaticMap->setZoom(14);
						//$oStaticMap->setAPIKey("");

;						if ($stop->country=='Cuba') {
							$oStaticMap->setCenter("{$stop->lat},{$stop->long}");
							$marker = new GoogleStaticMapMarker([
								"color"     => "FF0000",
								"label"     => "Santa",
								"latitude"  => $stop->lat,
								"longitude" => $stop->long
							]);

							$oStaticMap->setMarker($marker);
						} else $oStaticMap->setCenter(html_entity_decode($stop->name)." ".html_entity_decode($stop->country));

						// get path to the www folder
						$di = \Phalcon\DI\FactoryDefault::getDefault();
						$www_root = $di->get('path')['root'];

						// save image as a png file
						//$content = Utils::file_get_contents_curl("https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/{$stop->lat},{$stop->long},9.67,0.00,0.00/500x500@2x?access_token=pk.eyJ1Ijoia3VtYWhhY2tlciIsImEiOiJjazRpdzFodHMxcGJ4M25vNmhjdmJqbWF4In0.lhItxwxxV3021-D9rj3u0A");
						//$content = file_get_contents($oStaticMap);
						$content = Utils::file_get_contents_curl("https://www.mapquestapi.com/staticmap/v5/map?key=Ut3gS9mkk5cmm8gcaynC3dykGc7eA2gu&center={$stop->lat},{$stop->long}&traffic=flow|cons|inc&size=300,300@2x");
						$mapImagePath = "$www_root/shared/tmp/".Utils::generateRandomHash().".png";
						imagepng(imagecreatefromstring($content), $mapImagePath);

						$city = str_replace("_", " ", $stop->name);

						// generate random santa's message
						$messages = [
							"Le estoy dando zanahorias a Rodolfo en la ciudad de {$city} en {$stop->country}, el pobre ha pasado hambre desde el Polo Norte!!!",
							"Santa est&aacute; comiendo galletas de {$stop->country}, que le regalaron en {$city}",
							"Uff que fr&iacute;o hace aqu&iacute; arriba en {$city}. Ojal&aacute; que {$person->full_name} me haya dejado chocolate caliente de {$stop->country}!!!",
							"Mi esposa le hizo esta bufanda especialmente a @{$person->username}, y espero que le guste mucho. Estoy llegando a {$city}  :)",
							"Me encanta venir a {$city} porque siempre me dejan mucha comida.",
							"Cargar este saco lleno de regalos por todo {$city} me dejar&aacute; tremendo dolor en la espalda!!!",
							"&iquest;Dónde habr&aacute; algo de leche fr&iacute;a en {$city}?",
							"Espero que @{$person->username} est&eacute; durmiendo porque estoy muy gordo para salir corriendo por todo {$city}"
						];

						$msg_rand = $messages[mt_rand(0, count($messages) - 1)];

						// santa no ha llegado
						if ($user_pos > $i) {
							$time_left = null;

							if (!is_null($user_stop)) {
								$now_hour = $stop->hour;
								$time_left = ($user_hour * 60 + $user_minute) - ($now_hour * 60 + $minutos);
								$hours_left = intval($time_left / 60);
								$min_left = $time_left - $hours_left * 60;
								$time_left_str = ($hours_left > 0 ? "$hours_left horas y ":"")."$min_left minutos";
							}

							if ($stop->country=='Cuba') {
								$response->setTemplate('basic.ejs', [
									'title' => 'Santa no ha llegado a tu provincia',
									'message' => "$msg_rand. Llegar&eacute; a tu provincia en <b>$time_left_str</b>.",
									"image"   => $mapImagePath,
									'top50'   => $top50
								], [$mapImagePath]);

								return $response;
							} else {
								$response->setTemplate('basic.ejs', [
									'title' => 'Santa no ha llegado a tu localidad',
									'message' => "$msg_rand. Llegar&eacute; a tu provincia en <b>$time_left_str</b>.",
									"image"   => $mapImagePath,
									'top50'   => $top50
								], [$mapImagePath]);

								return $response;
							}
						}

						// santa esta aqui
						if ($user_pos==$i) {
							$response->setTemplate('basic.ejs', [
								'title' => 'Santa ha llegado a tu provincia',
								'message' => "Estoy en tu provincia !!!. $msg_rand. ",
								"image"   => $mapImagePath,
								'top50'   => $top50
							], [$mapImagePath]);

							return $response;
						}

						// santa se ha ido
						if ($user_pos < $i) {
							$response->setTemplate('basic.tpl', [
								'title' => 'Santa se ha ido de tu localidad',
								'message' => "Me he ido de tu localidad para repartir regalos a los dem&aacute;s ni&ntilde;os !!!. $msg_rand. ",
								"image"   => $mapImagePath,
								'top50'   => $top50
							], [$mapImagePath]);

							return $response;
						}
					}
				}
				$i++;
			}
		}

		// Si es despues de navidad o ya paso por todas las ciudades (no se dio return en el ciclo anterior)
		if (($today==$the_day && $hora >= $santa_stops[0]->hour) || ($today > $the_day && $today <= $end_year)) {
			$imgPath = __DIR__."/image/go.jpg";
			$response->setTemplate("basic.ejs", [
				"title" => "Santa ha regresado a su casa en el Polo Norte",
				"message" => "Regres&eacute; a mi casa en el Polo Norte hasta las pr&oacute;ximas navidades !!!.",
				"image"   => $imgPath,
				'top50'   => $top50
			], [$imgPath]);

			return $response;
		}

		// Es antes de navidad
		$datetime1 = new DateTime($today);
		$datetime2 = new DateTime($the_day);
		$interval = $datetime2->diff($datetime1);
		$dias = $interval->format('%a');
		$imgPath = __DIR__."/image/sleeping.jpg";

		$response->setTemplate('basic.ejs', [
			"title" => "Santa no ha partido de su casa en el Polo Norte",
			"message" => "Estoy durmiendo en mi casa en el Polo Norte. ".(($dias > 1) ? "Faltan $dias dias para navidad !!!":"Ma&ntilde;ana ser&aacute; navidad !!!"),
			"image"   => $imgPath,
			'top50'   => $top50
		], [$imgPath]);

		return $response;
	}
}
