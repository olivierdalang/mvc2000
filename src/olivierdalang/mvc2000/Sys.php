<?php

/**
 * Main and only class of Sys.
 */

namespace olivierdalang\mvc2000;

class Sys{

	// default configuration
	public static $b = '';						//holds the base url as a string, usefull to create absolute urls...	
	public static $defaultUri = 'home/index';	//set this using setup($conf['defaultUri'])

	public static $infos = [];					//holds informations such as the loaded controller, its methods, etc...

	private static $m;							//holds the models that have been loaded (object)
	private static $p;							//holds the plugins that have been loaded (object)


	/**
	 * Decomposes the uri, and loads the controller and calls its method
	 * @param string uri
	 */
	static function loadUri($uri){

		if($uri == false){
			$uri = self::$defaultUri;
		}

		$uri = explode('/',$uri);

		//Interpretation of the URI
		$controllerName = array_shift($uri);	//TODO IMPORTANT : sanitize (no directory browsing)
		$methodName = array_shift($uri)?:'index';	//TODO IMPORTANT : sanitize (no method starting by _)
		$parameters = $uri;

		self::loadController($controllerName, $methodName, $parameters);
	}
	
	/**
	 * Alias for loadController
	 */
	static function c($name, $method, $parameters=array()){
		return self::loadController($name, $method, $parameters=array());
	}

	/**
	 * Loads a controller
	 * @param string $name The controller name (must correspond to the file name and the class name of the controller)
	 * @param string $method The method that is called
	 * @param array $parameters The parameters to pass to the method
	 * @return void
	 */
	static function loadController($name, $method, $parameters=array()){

		$file_to_load = 'app/controllers/'.$name.'.php';

		if( ! file_exists( $file_to_load ) ){
			header("HTTP/1.0 404 Not Found");
			throw new \Exception('Controller file <b>'.$file_to_load.'</b> not found.' );
		}

		require_once($file_to_load);

		$controllerName = $name.'Controller';
		if( ! class_exists  ($controllerName) ){
			header("HTTP/1.0 500 Internal Server Error");
			throw new \Exception('Controller <b>'.$controllerName.'</b> class not found.' );
		}

		$controller = new $controllerName();

		if( ! method_exists($controller, $method ) ){
			header("HTTP/1.0 404 Not Found");
			throw new \Exception('Method <b>'.$method.'()</b> not found in controller <b>'.$name.'</b>' );
		}

		if( ! is_callable ([$controller, $method] ) ){
			header("HTTP/1.0 405 Method Not Allowed");
			throw new \Exception('<b>'.$method.'()</b> in controller <b>'.$name.'</b> is <b>private</b> !' );
		}

		self::$infos['controller'] = $name;
		self::$infos['method'] = $method;
		self::$infos['parameters'] = $parameters;
		call_user_func_array(array($controller,$method), $parameters);
	}

	/**
	 * Alias for loadModel
	 */
	static function m($model){
		return self::loadModel($model);
	}

	/**
	 * Loads a model (if not already loaded) and returns its instance.
	 * @param string $model The name of the model to load. Must correspond to the model file name AND to the model class name (with -Model suffix)
	 * @return object The instantiated model
	 */
	static function loadModel($model){

		if( isset(self::$m->$model) ){
			return self::$m->$model;
		}

		$file_to_load = 'app/models/'.$model.'.php';

		if( ! file_exists ($file_to_load ) ){
			header("HTTP/1.0 500 Internal Server Error");
			throw new \Exception('Model file <b>'.$file_to_load.'</b> not found.' );
		}

		require_once($file_to_load);

		$modelName = $model.'Model';
		if( ! class_exists  ($modelName) ){
			header("HTTP/1.0 500 Internal Server Error");
			throw new \Exception('Model <b>'.$modelName.'</b> class not found.' );
		}

		if(self::$m === null)
			self::$m = new \stdClass();
		
		self::$m->$model = new $modelName();
		
		return self::$m->$model;
	}

	/**
	 * Alias for loadModel
	 */
	static function p($plugin,$arguments=[]){
		return self::loadPlugin($model,$arguments);
	}

	static function loadPlugin($plugin,$arguments=[]){

		if( isset(self::$p->$plugin) ){
			return self::$p->$plugin;
		}

		$file_to_load = 'app/plugins/'.$plugin.'.php';

		if( ! file_exists ($file_to_load ) ){
			header("HTTP/1.0 500 Internal Server Error");
			throw new \Exception('Plugin file <b>'.$file_to_load.'</b> not found.' );
		}

		require_once($file_to_load);

		$pluginName = $plugin.'Plugin';
		if( ! class_exists  ($pluginName) ){
			header("HTTP/1.0 500 Internal Server Error");
			throw new \Exception('Plugin <b>'.$pluginName.'</b> class not found.' );
		}

		if(self::$p === null)
			self::$p = new \stdClass();
		
		$class = new \ReflectionClass($pluginName);		
		self::$p->$plugin = $class->newInstanceArgs($arguments);
		
		return self::$p->$plugin;
	}

	/**
	 *Alias for loadView
	 */
	static function v($view, $data=array(), $return = false){
		return self::loadView($view, $data, $return);
	}

	/**
	 * Loads a view and either outputs it or returns it as a string
	 * @param string $view The name oe the view (including .php)
	 * @param array $data The variables that should be available in the view
	 * @param boolean $return If true, the view is not sent to the standart output, but returned as a string
	 * @return mixed
	 */
	static function loadView($view, $data=array(), $return = false){

		$file_to_load = 'app/views/'.$view;
		if(!file_exists($file_to_load)){
			throw new \Exception('The view file <b>"'.$file_to_load.'"</b> could not be loaded.');
		}

		if($return){
			ob_start();
		}

		//This translates the $data array to $key variables, so it's shorter to write in the pseudo template
		if($data && is_array($data)){
			extract($data);
		}
		
		include($file_to_load);
			
		if($return){
			return ob_get_clean();
		}
	}



}
