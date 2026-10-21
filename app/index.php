<?php
	require_once("Config/System.php");
	require_once("Core/WebSecurity.php");
    WebSecurity::bootstrap();
	require_once("Helpers/Helpers.php");

	$url = !empty($_GET['url']) ? $_GET['url'] : 'login/login';
	$arrUrl = explode("/", trim($url, "/"));

	$module     = isset($arrUrl[0]) ? $arrUrl[0] : '';
	$controller = isset($arrUrl[1]) ? $arrUrl[1] : $module;
	$method     = isset($arrUrl[2]) ? $arrUrl[2] : $controller;
	$params     = "";

	if (count($arrUrl) > 3) {
		$params = implode(',', array_slice($arrUrl, 3));
	}

	require_once("Core/Autoload.php");
	require_once("Core/Load.php");
?>