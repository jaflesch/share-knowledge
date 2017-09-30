<?php
require_once('config.php');

try {
	if($config->debug) {
		ini_set("display_errors", "1");
		error_reporting(E_ALL);
	}

	// Include libs
	require_once('_lib/Controller.php');
	require_once('_lib/Helper/String.php');
	require_once('_lib/Helper/Recaptchalib.php');
	require_once("_lib/Helper/Traducao.php");
	require_once("_lib/Helper/Authentication.php");
	require_once("_lib/Helper/Handlers.php");
	require_once('_lib/Twig/Autoloader.php');

	//Initiate Error Handlers
	Handlers::start();

	//Initiate Template
	Twig_Autoloader::register();

	//Initiate the database
	$dbConn = @mysqli_connect($config->db->host, $config->db->user, $config->db->pass, $config->db->name);

	//Initiate session
	session_name($config->session_name);
	session_start();

	// filtra sessão
	if (!isset($_SESSION['user_agent'])) {
		$_SESSION['user_agent'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
	} 
	else {
		if (!isset($_SERVER['HTTP_USER_AGENT']) || $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
			session_destroy();
			$_SESSION = array();				
		}
	}

	// Filtra post
	$post = xssFilter($_POST);
	$get = xssFilter($_GET);

	$path_info = isset($_GET['url'])?trim($_GET['url'],'/'):'';
	$parametros =  explode('/',$path_info);	

	if (isset($_SERVER['HTTP_HOST'])) {
		$base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
		$base_url .= '://' . $_SERVER['HTTP_HOST'];
		$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
	} 
	else {
		$base_url = 'http://localhost/';
	}

	if (!isset($_SESSION['idioma'])) {
		$_SESSION['idioma'] = $config->default_idioma;
	}

	$idioma = $_SESSION['idioma'];

	$app = new stdclass();
	$app->post = $post;
	$app->get = $get;
	$app->path_info = $path_info;
	$app->parametros = $parametros;
	$app->base_url = $base_url;
	$app->config = $config;
	$app->dbConn = $dbConn;
	$app->metadata = $metadata;
	$app->idioma = $idioma;
	$app->request_uri = $_SERVER["REQUEST_URI"];	
	$app->base_path = $config->base_path;
	$app->debug = $config->debug;
	
	// Twig shortcut paths
	$path = new stdclass();
	$path->root = substr($base_url, 0, -1);
	$path->css = $path->root.'/assets/css';
	$path->js = $path->root.'/assets/js';
	$path->img = $path->root.'/assets/img';
	$path->font = $path->root.'/assets/font';
	$app->twig = $path;
	
	$controller = array_shift($parametros);

	if ($controller == '') {
		$controller = $config->default_controller;
	}

	$controller_path = "_controller/{$controller}.php";

	if (!file_exists($controller_path)) {
		$controller_path = "_controller/404.php";
		$app->controller = "Error404";
		$app->action = "index";

		require ($controller_path);
		$class = "Error404";
		$class::exec($app);
	}
	else {
		$action = array_shift($parametros);

		if ($action == '') $action = $config->default_action;
		
		$app->controller = $controller;
		$app->action = $action;

		require ($controller_path);
		$Class = Controller::setClassName($controller);
		$Class::exec($app);
	}

} 
catch (Exception $e) {
	if ($config->debug) {
		print_r($e->getMessage());
		print_r($e->getLine());
		print_r($e->getTrace());
	}
}