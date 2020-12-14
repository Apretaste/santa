<?php

use Apretaste\Level;
use Apretaste\Money;
use Apretaste\Person;
use Apretaste\Request;
use Apretaste\Response;
use Framework\Utils;
use Framework\Crawler;
use Framework\Database;

/**
 * Santa Service
 *
 * @author  @kumahacker
 * @author  @salvipascual
 * @version 3.0
 */
class Service 
{
	// list of possible gifts
	private $gifts = [
		"ELF" => [
			"CREDIT_05" => "§0.05 de crédito",
			"CREDIT_010" => "§0.10 de crédito",
			"TICKET_1" => "1 ticket para la rifa",
			"TICKET_2" => "2 tickets para la rifa",
			"EXP_5" => "5 de experiencia",
			"EXP_10" => "10 de experiencia"
		],
		"SANTA" => [
			"CREDIT_1" => "§1 de crédito",
			"CREDIT_3" => "§3 de crédito",
			"TICKET_20" => "20 tickets para la rifa",
			"TICKET_30" => "30 tickets para la rifa",
			"EXP_50" => "50 de experiencia"
		]
	];

	/**
	 * Main action
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _main (Request $request, Response $response)
	{
		// make sure user has a province
		if (empty($request->person->provinceCode)) {
			return $response->setTemplate("province.ejs");
		}

		//
		// Before Christmas
		//

		// if current time less than christmas
		if(time() < strtotime(date('Y') . '-12-24 09:00:00')) {
			// get days until Christmas
			$daysTillChristmas = $this->getDaysTillChristmas();

			// message on Christmas day
			if($daysTillChristmas <= 0) {
				$message = "Hoy es navidad! Estoy preparándome para salir y empezar a repartir regalos por todo el mundo.";
			}

			// message on the ten days of Christmas
			elseif($daysTillChristmas <= 10) {
				$message = "¡Qué sueño! Casi estoy despertando; mientras puse a mis elfos a fabricar los regalos de Navidad.";
			}

			// message before Christmas
			else {
				$message = "Estoy durmiendo en mi casa en el Polo Norte. Aún faltan $daysTillChristmas días para salir a repartir regalos.";
			}

			// create content for the view
			$content = [
				"message" => $message, 
				"days" => $daysTillChristmas
			];

			// send data to the view
			$response->setTemplate('sleeping.ejs', $content);
			return $response;
		}

		//
		// After Christmas
		//

		// if current time passed christmas
		if(time() > strtotime(date('Y') . '-12-24 23:04:00')) {
			$response->setTemplate('return.ejs');
			return $response;
		}

		//
		// On Christmas Day!
		//

		// find the current location of Santa
		$santaCurrentLocation = $this->getSantaCurrentLocation();

		// create path to image map
		$googleMapsURL = "https://www.mapquestapi.com/staticmap/v5/map?key=Ut3gS9mkk5cmm8gcaynC3dykGc7eA2gu&center={$santaCurrentLocation->lat},{$santaCurrentLocation->long}&traffic=flow|cons|inc&size=300,300@2x&locations={$santaCurrentLocation->lat},{$santaCurrentLocation->long}";
		$mapImagePath = TEMP_PATH . 'santa/' . md5($googleMapsURL) . ".png";

		// download and save image as a png file
		if (!file_exists($mapImagePath)) {
			$content = Crawler::get($googleMapsURL);
			imagepng(imagecreatefromstring($content), $mapImagePath);
		}

		// create the list of messages
		$messages = [
			"Le estoy dando zanahorias a Rodolfo en {$santaCurrentLocation->name}, el pobre ha pasado hambre en el Polo Norte",
			"Estoy comiendo galletas en {$santaCurrentLocation->name}; me las regalaron los residentes locales",
			"Uff que frío hace aquí en {$santaCurrentLocation->name}. Ojalá que @{$request->person->username} me haya dejado chocolate caliente",
			"Mi esposa le hizo esta bufanda especialmente a @{$request->person->username}, y espero que le guste mucho. Estoy llegando a {$santaCurrentLocation->name}",
			"Me encanta venir a {$santaCurrentLocation->name} porque siempre me dejan muchas galletas y leche",
			"Cargar este saco lleno de regalos por todo {$santaCurrentLocation->name} me dejará tremendo dolor en la espalda",
			"¿Dónde habrá algo de leche fría en {$santaCurrentLocation->name}? Tengo muchísima sed",
			"Espero que @{$request->person->username} esté en casa porque estoy muy gordo para salir a buscarlo por todo {$santaCurrentLocation->name}"
		];

		// generate random santa's message
		$randomMessage = $messages[array_rand($messages)];

		// create content for the view
		$content = [
			"message" => "$randomMessage. Llegaré a tu provincia en {$santaCurrentLocation->arrivalTime}.",
			"image" => basename($mapImagePath),
		];

		// send information to the view
		$response->setTemplate('christmas.ejs', $content, [$mapImagePath]);
	}

	/**
	 * Show the Christmas tree
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _arbol (Request $request, Response $response)
	{
		// make sure user has a province
		if (empty($request->person->provinceCode)) {
			return $response->setTemplate("province.ejs");
		}

		// create an empty gift by default
		$gift = (Object) ["id" => "", "opened" => 1, "caption" => "", "sender" => "elf"];

		// get days until Christmas
		$daysTillChristmas = $this->getDaysTillChristmas();

		// after Christmas day
		if($daysTillChristmas < 0) {
			// offer the present till the end of the year
			if(strtotime(date(date('Y') . '-12-31')) > time()) {
				$gift = $this->getCurrentGift($request->person->id, 'SANTA');
			}

			// get the message
			$message = "¡Por fin acabó la Navidad! Ahora a relajar y disfrutar hasta el próximo año. Nos vemos pronto.";
		}

		// Christmas day!
		elseif($daysTillChristmas == 0) {
			// check if Santa passed by
			$didSantaVisit = $this->didSantaVisitMyProvince($request->person->provinceCode);

			if($didSantaVisit) {
				// get or create a gift
				$gift = $this->getCurrentGift($request->person->id, 'SANTA');

				// get the message
				$message = "¡Feliz Navidad! Pasé por tu provincia y te dejé un regalo. Nos vemos el próximo año.";
			} else {
				// get the message
				$message = "¡Hoy es Navidad! Santa está entregando los regalos, ¡Qué emoción! Mantente al tanto cuando llegue a tu provincia.";
			}
		}

		// the 10 days of Christmas
		elseif($daysTillChristmas <= 10) {
			// get or create a gift
			$gift = $this->getCurrentGift($request->person->id, 'ELF');

			// get the message
			if($gift->opened) {
				$message = "Parece que ya abristes el regalo que te traje hoy. Vira mañana y revisa bajo el arbolito.";
			} else {
				$message = "Mientras Santa duerme, te traje este regalo de la fábrica. Quedan $daysTillChristmas días para que salga Santa.";
			}
		}

		// ten days before Christmas 
		else {
			$message = "Ese gordo de Santa aún está roncando, mientras nosotros los elfos fabricamos todos los regalos. ¡Ya quiero que termine la Navidad!";
		}

		// create content for the view
		$content = [
			"gift" => $gift,
			"message" => $message
		];

		// send information to the view
		$response->setTemplate('tree.ejs', $content);
	}

	/**
	 * Display the Christmas help
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _reglas (Request $request, Response $response)
	{
		$response->setCache('year');
		$response->setTemplate('rules.ejs');
	}

	/**
	 * Take the gift under the tree
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function _regalo (Request $request, Response $response)
	{
		// get the id
		$id = $request->input->data->id;

		// make sure data is valid
		if (empty($id) || empty($request->person->provinceCode)) {
			return false;
		}

		// get the gift information
		$gift = Database::queryFirst("
			SELECT code 
			FROM _santa 
			WHERE id = $id 
			AND person_id = {$request->person->id}
			AND opened = 0");

		// do not continue for invalida data
		if(empty($gift)) return false;

		// apply code CREDIT_05
		if($gift->code == "CREDIT_05") {
			Money::send(Money::BANK, $request->person->id, 0.05, "Un regalo del Elfo por Navidad");
		}

		// apply code CREDIT_010
		if($gift->code == "CREDIT_010") {
			Money::send(Money::BANK, $request->person->id, 0.10, "Un regalo del Elfo por Navidad");
		}

		// apply code TICKET_1
		if($gift->code == "TICKET_1") {
			Utils::addRaffleTickets($request->person->id, 1, "como regalo de Navidad", "SANTA");
		}

		// apply code TICKET_2
		if($gift->code == "TICKET_2") {
			Utils::addRaffleTickets($request->person->id, 2, "como regalo de Navidad", "SANTA");
		}

		// apply code EXP_5
		if($gift->code == "EXP_5") {
			Level::setExperience('SANTA_EXP_5', $request->person->id);
		}

		// apply code EXP_10
		if($gift->code == "EXP_10") {
			Level::setExperience('SANTA_EXP_10', $request->person->id);
		}

		// apply code CREDIT_3
		if($gift->code == "CREDIT_1") {
			Money::send(Money::BANK, $request->person->id, 1, "Tu regalo de Navidad");
		}

		// apply code CREDIT_3
		if($gift->code == "CREDIT_3") {
			Money::send(Money::BANK, $request->person->id, 3, "Tu regalo de Navidad");
		}

		// apply code TICKET_20
		if($gift->code == "TICKET_20") {
			Utils::addRaffleTickets($request->person->id, 20, "como regalo de Navidad", "SANTA");
		}

		// apply code TICKET_30
		if($gift->code == "TICKET_30") {
			Utils::addRaffleTickets($request->person->id, 30, "como regalo de Navidad", "SANTA");
		}

		// apply code EXP_50
		if($gift->code == "EXP_50") {
			Level::setExperience('SANTA_EXP_50', $request->person->id);
		}

		// mark the gift as opened
		Database::query("UPDATE _santa SET opened = 1 WHERE id = $id AND person_id = {$request->person->id}");
	}

	/**
	 * Get Days Till Christmas
	 *
	 * @return Int: 0 for Xmas day, -1 if Xmas passed
	 */
	private function getDaysTillChristmas()
	{
		// get both dates
		$today = new DateTime(date('Y-m-d'));
		$christmas = new DateTime(date('Y') . '-12-24 09:00:00');

		// is date passed christmas?
		if($today->getTimestamp() > $christmas->getTimestamp()) {
			return -1;
		}

		// calculate interval
		$interval = $christmas->diff($today);
		return $interval->format('%a');
	}

	/**
	 * Find the current location of Santa
	 */
	private function getSantaCurrentLocation()
	{
		// load the santa locations
		$locations = json_decode(file_get_contents(__DIR__ . "/locations.json"));

		// find the current location
		$santaCurrentLocation = false;
		foreach ($locations as $item) {
			// calculate the range of minutes
			$minutes = explode('-', $item->minutes);
			$minFrom = intval($minutes[0]);
			$minTo = intval($minutes[1]);

			// find the location based on hour and minutes
			if($item->hour == date('G') && date('i') >= $minFrom && date('i') <= $minTo) {
				// calculate arrival time
				$time_left = ($item->hour * 60 + $minTo) - (date('G') * 60 + date('i'));
				$hours_left = intval($time_left / 60);
				$min_left = $time_left - $hours_left * 60;
				$item->arrivalTime = ($hours_left > 0 ? "$hours_left horas y " : "") . "$min_left minutos";

				// save the current location
				$santaCurrentLocation = $item;
				break;
			}
		}

		return $santaCurrentLocation;
	}

	/**
	 * Get a gift from the database, or create one
	 * 
	 * Integer $personId
	 * Enum $sender: [ELF | SANTA]
	 */
	private function getCurrentGift($personId, $sender)
	{
		// check if there is an open gift
		$gift = Database::queryFirst("
			SELECT id, code, opened
			FROM _santa
			WHERE inserted > CURRENT_DATE
			AND person_id = $personId
			AND sender = '$sender'");

		// if not existant, get a random gift
		if(empty($gift)) {
			// pick a random gift
			$code = array_rand($this->gifts[$sender]);

			// save gift in the database
			$id = Database::query("
				INSERT INTO _santa(code, person_id, sender) 
				VALUES ('$code', $personId, '$sender')");

			// create the gift object
			$gift = new stdClass();
			$gift->id = $id;
			$gift->code = $code;
			$gift->opened = 0;
		}

		// add the gift caption
		$gift->sender = strtolower($sender);
		$gift->caption = $this->gifts[$sender][$gift->code];
		return $gift;
	}

	/**
	 * Check if Santa passed by a Cuban province
	 * 
	 * Integer $province
	 */
	private function didSantaVisitMyProvince($province)
	{
		// load the santa locations
		$locations = json_decode(file_get_contents(__DIR__ . "/locations.json"));

		// locate your province
		foreach ($locations as $item) {
			if($item->province == $province) break;
		}

		// calculate the scheduling passing datetime
		$minutes = explode('-', $item->minutes);
		$minFrom = intval($minutes[0]);
		$scheduledVisitDate = date(date('Y') . '-12-24 ' . "{$item->hour}:{$minFrom}:00");

		// return if Santa already passed by your province
		return strtotime($scheduledVisitDate) < time();
	}
}
