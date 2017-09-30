
<?php
class Home extends Controller {

	public static function index() {
		$bag = array();			
		self::render("home/index.html", $bag);
	}

	public static function submit_form() {
		global $dbConn;
		$post = preprocess((object)static::$app->post);
		$date = date('Y-m-d');

		$query = "
			INSERT INTO lead (name, email, phone,city, message, date)
			VALUES (
				'{$post->name}',
				'{$post->email}',
				'{$post->phone}',
				'{$post->city}',
				'{$post->message}',
				'{$date}'
			)
		";

		$result = mysqli_query($dbConn, $query);
		$json = new stdclass();
		$json->success = $result;
		$json->msg = ($result === true) ? 'Obrigado pelo seu feedback!' : 'Erro ao enviar sua mensagem. Por favor, tente novamente.';

		die(json_encode($json));
	}
}