<?php

class Controller {
	public static $app;
	public static $config;
	public static $dbConn;
	public static $twig;
	public static $metadata;

	public static function redirect($url, $internal = false) {
		$base_url = static::$app->base_url;

		if($internal) {
			header("Location: {$base_url}{$url}");
			exit(0);			
		}
		else {
			header("Location: {$url}");
			exit(0);				
		}
	}

	public static function exec($app) {
		static::$app = $app;
		static::$config = $app->config;
		static::$dbConn = $app->dbConn;
		static::$twig = $app->twig;
		static::$metadata = $app->metadata;
		
		if ($app->action=='') $app->action = $app->default_action;		

		$action = str_replace("-", "_", $app->action);
		if (!method_exists(get_called_class(), $action)) $action = $app->default_action;
		static::$action();
	}

	public static function traduzObj($obj,$vars) {
		if (is_object($obj)) {
			foreach((array)$obj as $key=>$value) {
				$obj->{$key} = static::traduzObj($value, $vars);
			}
		} else if (is_array($obj)) {
			foreach($obj as $key=>$value) {
				$obj[$key] = static::traduzObj($value, $vars);
			}
		} else if (is_string($obj)) {
			$loader = new Twig_Loader_Array(array(
				'index.html' => $obj
			));
			$twig = new Twig_Environment($loader, array(
				'autoescape' => false,
				'auto_reload' => true
			));
			$obj = $twig->render('index.html',$vars);
		}

		return $obj;
	}

	public static function showJson($obj,$vars = array()) {
		header("Content-type: application/json");

		$app = static::$app;

		$vars['app'] = static::$app;
		$vars['config'] = static::$config;
		$vars['traducao'] = array();
		$vars['post'] = static::$app->post;
		$vars['get'] = static::$app->get;
		$vars['twig'] = static::$app->twig;
		$vars['meta'] = static::$metadata;
		$vars['session'] = $_SESSION;

		echo json_encode(static::traduzObj($obj,$vars));		
	}

	public static function render($tpl = "index.html", $vars = array(), $echo = true) {
		$loader = new Twig_Loader_Filesystem(static::$app->base_path.'/_view');
		$twig = new Twig_Environment($loader,array(
			'cache' => static::$app->debug?false:$app->base_path.'/_cache',
			'auto_reload' => true
		));

		$app = static::$app;
		$vars['app'] = static::$app;
		$vars['config'] = static::$config;
		$vars['path'] = static::$twig;
		$vars['meta'] = static::$metadata;
		
		try {
			$output = $twig->render($tpl,$vars);
			
			if(!$echo) return $output;
			echo $output;
		}
		catch(Exception $e) {
			print_r($e->getMessage());
			print_r($e->getLine());
		}
	}

	public static function setClassName($controller) {
		/**
			@param: (string) formatted as class-name-example
			@return: (string) class name formatted as ClassNameExample
		*/
		$controller = str_replace(array(" ", "-"), "_", $controller);
		$pieces = explode("_", $controller);
		unset($controller);
		foreach ($pieces as $piece) {
			$controller .= ucfirst($piece);
		}

		return $controller;
	}
}






