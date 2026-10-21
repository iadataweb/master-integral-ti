<?php 

	// Normalizar nombres
	$moduleClass     = str_replace(' ', '', ucwords(str_replace('_', ' ', $module)));
	$controllerClass = str_replace(' ', '', ucwords(str_replace('_', ' ', $controller)));

	// Ruta dentro de módulo
	$controllerFile = "Controllers/{$moduleClass}/{$controllerClass}.php";

	// Verificar si existe dentro del módulo
	if (file_exists($controllerFile)) {
		require_once($controllerFile);
		$controllerObj = new $controllerClass();
		if (WebSecurity::callableAction($controllerObj, $method)) {
			$controllerObj->{$method}($params);
		} else {
			require_once("Controllers/Errors.php");
			$not_found = new Errors();
			$not_found->not_found();
		}
	} else {
		// Si no existe, buscar directamente en la raíz
		$controllerFileRoot = "Controllers/{$moduleClass}.php";
		if (file_exists($controllerFileRoot)) {
			require_once($controllerFileRoot);
			$controllerObj = new $moduleClass();
			if (WebSecurity::callableAction($controllerObj, $controller)) {
				$controllerObj->{$controller}($params);
			} else {
				require_once("Controllers/Errors.php");
				$not_found = new Errors();
				$not_found->not_found();
			}
		} else {
			require_once("Controllers/Errors.php");
			$not_found = new Errors();
			$not_found->not_found();
		}
	}
?>