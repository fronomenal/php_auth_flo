<?php
	require_once '../incs/config.php';


if (isset($_SESSION["VERIFY"])){
  echo $_SESSION["VERIFY"];
  unset($_SESSION["VERIFY"]);
}else{
  ob_start();
  header("Location: /index.php");
  ob_end_flush();
  die();
}