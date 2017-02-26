<?php
class Controller {
	
	public $model;
	public $view;
	
	function __construct($parameters = NULL)
	{
		$this->view = new View();
	}
	
	function action_index()
	{

	}

	private function set_header()
	{
		include_once("views/header.html");
	}

	private function set_footer()
	{
		include_once("views/footer.html");
	}

	protected function view($view_file,$params = [])
	{
		extract($params);
		$this->set_header();
		require_once('views/' . $view_file . '.html');
		$this->set_footer();
	}
}
?>