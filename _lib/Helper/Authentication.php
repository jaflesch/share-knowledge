<?php
class Auth {
	
	public static function getUser() {
		session_start();
		return $_SESSION['eco']['user'];
	}

	public static function setUser($user) {
		session_start();
		$_SESSION['eco']['user'] = $user;
	}
	
	public static function id() {
		session_start();
		return $_SESSION['eco']['user']->id;
	}

	public static function user() {
		session_start();
		return isset($_SESSION['eco']['user']);
	}

	public static function logout() {
		session_start();
		unset($_SESSION['eco']);
		session_destroy();
	}
}
?>