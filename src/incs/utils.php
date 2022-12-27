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

	function sqlSelect($C, $query, $format = false, ...$vars) {
		$stmt = $C->prepare($query);
		if($format) {
			$stmt->bind_param($format, ...$vars);
		}
		if($stmt->execute()) {
			$res = $stmt->get_result();
			$stmt->close();
			return $res;
		}
		$stmt->close();
		return false;
	}

	function sqlInsert($C, $query, $format = false, ...$vars) {
		$stmt = $C->prepare($query);
		if($format) {
			$stmt->bind_param($format, ...$vars);
		}
		if($stmt->execute()) {
			$id = $stmt->insert_id;
			$stmt->close();
			return $id;
		}
		$stmt->close();
		return -1;
	}

	function sqlUpdate($C, $query, $format = false, ...$vars) {
		$stmt = $C->prepare($query);
		if($format) {
			$stmt->bind_param($format, ...$vars);
		}
		if($stmt->execute()) {
			$stmt->close();
			return true;
		}
		$stmt->close();
		return false;
	}

	function urlSafeEncode($m) {
		return rtrim(str_replace('+/', '-_', base64_encode($m)), '=');
	}
	function urlSafeDecode($m) {
		return base64_decode(str_replace('-_', '+/', $m));
	}
		

	function createToken() {
		$seed = urlSafeEncode(random_bytes(8));
		$t = time();
		$hash = urlSafeEncode(hash_hmac('sha256', session_id() . $seed . $t, CSRF_TOKEN_SECRET, true));
		return urlSafeEncode($hash . '||' . $seed . '||' . $t);
	}

	function validateToken($token) {
		$parts = explode('||', urlSafeDecode($token));
		if(count($parts) === 3) {
			$check_hash = hash_hmac('sha256', session_id() . $parts[1] . $parts[2], CSRF_TOKEN_SECRET, true);
			if(hash_equals($check_hash, urlSafeDecode($parts[0]))) {
				return true;
			}
		}
		return false;
	}

