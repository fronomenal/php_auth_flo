<?php
require_once 'config.php';

function connect() {
	try {
		$C = new PDO("sqlite:" . DB_DATABASE);
		$C->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		return $C;
	} catch (PDOException) {
		return false;
	}
}

function sqlSelect($C, $query, $vars = false) {
	$stmt = $C->prepare($query);
	$res = null;
	if($vars && $stmt->execute($vars)) $res = $stmt->fetch();
	else if($stmt->execute()) $res = $stmt->fetch(PDO::FETCH_ASSOC);
	return $res;
}

function sqlInsert($C, $query, $vars = false) {
	$stmt = $C->prepare($query);
	$id = -1;
	if($vars && $stmt->execute($vars)) $id = $C->lastInsertId();
	else if($stmt->execute()) $id = $C->lastInsertId();
	return $id;
}

function sqlUpdate($C, $query, $vars = false) {
	$stmt = $C->prepare($query);
	$succ = false;
	if($vars && $stmt->execute($vars)) $succ = true;
	else if($stmt->execute()) $succ = true;
	return $succ;
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


//Mocks mailing functionality by setting a session to reveal the verification message on the secret fake-mail route.
function sendEmail($to, $toName, $subj, $msg) {
	$session_mail=<<<MAILER
	<main>
		<p>$toName : $to</p>
		<hr>
		<h1>$subj</h1>
		<div>$msg</div>
	</main>
	MAILER;

	$_SESSION["VERIFY"] = $session_mail;

	return true;
}