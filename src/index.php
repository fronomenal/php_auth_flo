<?php 
	require_once 'incs/utils.php'; 

	if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
		ob_start();
		header("Location: /index.php");
		ob_end_flush();
		exit;
	}

	$user = [];	
	$C = connect();
	if($C) {
		$res = sqlSelect($C, 'SELECT * FROM users WHERE id=?', [$_SESSION['userID']]);
		if($res) {
			$user = $res;
		} else {
			exit;
		}
	} else {
		exit;
	}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="csrf_token" content="<?php echo createToken(); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>PHP Auth Flo</title>
	<link rel="stylesheet" href="/www/style.css" />
	<script src='/www/script.js' defer></script>
</head>
<body>
		<h1>Secure Site</h1>
		<div id="errs" class="errorcontainer"></div>
		<br><br>
		<h2>Hello <?php echo htmlspecialchars($user['name'], ENT_QUOTES); ?></h2>
		<br><br>
		<div class="btn" onclick="logout();">Log Out</div>
		<br><br>
		<div class="btn" onclick="deleteAccount();">Delete Account</div>
	
	<svg class="wave" xmlns="http://www.w3.org/2000/svg" viewBox="0 32 1440 320"><defs><linearGradient id="a" x1="50%" x2="50%" y1="-10.959%" y2="100%"><stop stop-color="#ffffff" stop-opacity=".10" offset="0%"/><stop stop-color="#FFFFFF" stop-opacity=".05" offset="100%"/></linearGradient></defs><path fill="url(#a)" fill-opacity="1" d="M 0 320 L 48 288 C 96 256 192 192 288 160 C 384 128 480 128 576 112 C 672 96 768 64 864 48 C 960 32 1056 32 1152 32 C 1248 32 1344 32 1392 32 L 1440 32 L 1440 2000 L 1392 2000 C 1344 2000 1248 2000 1152 2000 C 1056 2000 960 2000 864 2000 C 768 2000 672 2000 576 2000 C 480 2000 384 2000 288 2000 C 192 2000 96 2000 48 2000 L 0 2000 Z"></path></svg>
	<svg class="wave" xmlns="http://www.w3.org/2000/svg" viewBox="0 32 1440 320"><defs><linearGradient id="a" x1="50%" x2="50%" y1="-10.959%" y2="100%"><stop stop-color="#ffffff" stop-opacity=".10" offset="0%"/><stop stop-color="#FFFFFF" stop-opacity=".05" offset="100%"/></linearGradient></defs><path fill="url(#a)" fill-opacity="1" d="M 0 320 L 48 288 C 96 256 192 192 288 160 C 384 128 480 128 576 112 C 672 96 768 64 864 48 C 960 32 1056 32 1152 32 C 1248 32 1344 32 1392 32 L 1440 32 L 1440 2000 L 1392 2000 C 1344 2000 1248 2000 1152 2000 C 1056 2000 960 2000 864 2000 C 768 2000 672 2000 576 2000 C 480 2000 384 2000 288 2000 C 192 2000 96 2000 48 2000 L 0 2000 Z"></path></svg>

</body>
</html>
