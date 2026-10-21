<?php 
	
	class Views
	{
		function getView($controller, $module, $view, $data="")
		{
			$controller = get_class($controller);
			if (empty($module)) {
				$view = "Views/".$controller."/".$view.".php";
			} else {	
				$view = "Views/".$module."/".$controller."/".$view.".php";
			}
			require_once ($view);
		}
	}
	
?>