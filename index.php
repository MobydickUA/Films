<?php
// ini_set('display_errors', 1);
require_once 'core/model.php';
require_once 'core/controller.php';
//require_once 'core/route.php';



class Route
{

	static function start()
	{
		// контроллер и действие по умолчанию
		$controller_name = 'films';
		$action_name = 'index';

		
		$tmp = explode('?', $_SERVER['REQUEST_URI']);
		$routes = explode('/', $tmp[0]);

		$n = 1;
		if($routes[0] == 'localhost')
			$n = 2;
		
		//$routes = explode('/', $_SERVER['REQUEST_URI']);	

		// получаем имя контроллера
		if ( !empty($routes[$n]) )
		{	
			$controller_name = $routes[$n];
		}
		// получаем имя экшена
		if ( !empty($routes[$n+1]) )
		{
			$action_name = $routes[$n+1];
		}

		if ( !empty($routes[$n+2]) )
		{
			$parameters = $routes[$n+2];
		}

		// добавляем префиксы
		$model_name = 'Model_'.$controller_name;
		$controller_name = 'Controller_'.$controller_name;
		$action_name = 'action_'.$action_name;

		// подцепляем файл с классом модели (файла модели может и не быть)

		$model_file = strtolower($model_name).'.php';
		$model_path = "models/".$model_file;
		if(file_exists($model_path))
		{
			require "models/".$model_file;
		}

		// подцепляем файл с классом контроллера
		$controller_file = strtolower($controller_name).'.php';
		$controller_path = "controllers/".$controller_file;
		if(file_exists($controller_path))
		{
			require $controller_path;
		}
		else
		{

			/*
			правильно было бы кинуть здесь исключение,
			но для упрощения сразу сделаем редирект на страницу 404
			*/
			Route::ErrorPage404();
		}
		
		// die($controller_name);
		// создаем контроллер
		$controller = new $controller_name();
		$action = $action_name;
		
		if(method_exists($controller, $action))
		{
			// вызываем действие контроллера
			$controller->$action();
		}
		else
		{
			$controller->getFilm($routes[2]);
		}
	
	}
	
	function ErrorPage404()
	{
		$host = 'http://'.$_SERVER['HTTP_HOST'].'/';
		header('HTTP/1.1 404 Not Found');
		header("Status: 404 Not Found");
		header('Location:'.$host.'404');
    }
}


Route::start();
?>