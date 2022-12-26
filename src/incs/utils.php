<?php
	require_once 'config.php';

	function connect() {
		try {
		$C = new PDO("sqlite:" . DB_DATABASE);
		return $C;
		} catch (PDOException) {
			return false;
		}
	}