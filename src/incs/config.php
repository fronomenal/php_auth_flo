<?php

	// Global Variables
	define('MAX_LOGIN_ATTEMPTS_PER_HOUR', 5);
	define('MAX_EMAIL_VERIFICATION_REQUESTS_PER_DAY', 3);
	define('MAX_PASSWORD_RESET_REQUESTS_PER_DAY', 3);
	define('PASSWORD_RESET_REQUEST_EXPIRY_TIME', 60*60);
	define('CSRF_TOKEN_SECRET', 'somethingsomethingsomesecret');
	define('VALIDATE_EMAIL_ENDPOINT', 'http://localhost:8080/pages/validate-email.php');
	define('RESET_PASSWORD_ENDPOINT', 'http://localhost:8080/pages/reset-pass.php');

	// Database Credentials
	define('DB_DATABASE', realpath('../../db/auth.db')? realpath('../../db/auth.db') : realpath('../db/auth.db'));

	// Page Startup Scripts
	date_default_timezone_set('UTC'); 
	error_reporting(0);
	session_set_cookie_params(['samesite' => 'Strict']);
	session_start();
	
