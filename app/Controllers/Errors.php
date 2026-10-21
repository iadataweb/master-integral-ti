<?php 
	class Errors extends Controllers{
		
		public function __construct()
		{
			if (session_status() === PHP_SESSION_NONE) if (session_status() !== PHP_SESSION_ACTIVE) session_start();
			parent::__construct();
		}

		public function forbidden()
		{
			http_response_code(403);
			$this->views->getView($this, "", "403");
		}

		public function not_found()
		{
			http_response_code(404);
			$this->views->getView($this, "", "404");
		}

		public function system_error()
		{
			http_response_code(500);
			$this->views->getView($this, "", "500");
		}
	}
?>