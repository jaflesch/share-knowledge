<?php

$config = new stdclass();

$config->db = new stdclass();
$config->db->host = "localhost";
$config->db->user = "root";
$config->db->pass = "";
$config->db->name = "";

$config->news = new stdclass();
$config->news->rows = 10;

$config->debug = true;
$config->session_name = "site";
$config->default_controller = "home";
$config->default_action = "index";

$config->base_path = dirname(__FILE__);

$config->main_host = "localhost/";

$config->email_contato = "contato@localhost.com.br";

$config->default_idioma = "PT";

$config->recaptcha = new stdclass();
$config->recaptcha->private_key = "6LdDPP0SAAAAADD5OdRaY9enySh3w_FMCjRQXff4";
$config->recaptcha->public_key = "6LdDPP0SAAAAAM8p6_pUNcoq1vHc9ywj0h91-7AQ";

// Metadata
$metadata = new stdclass();
$metadata->app = "";
$metadata->keywords = "";
$metadata->description = "";

// Timezone
date_default_timezone_set('America/Sao_Paulo');