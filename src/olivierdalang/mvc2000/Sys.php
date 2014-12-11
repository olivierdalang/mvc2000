<?php

/**
 *
 * # Tree #
 *
 * index.php 		Enter-script and loads the system.
 * .htaccess 		Rewriting rules. Call Sys::debugShowNeededHtaccess() to display the suggested .htaccess regarding to the app/public folder.				Contains the system files
 * system/			Contains the system.
 * app/				Contains the application 
 * app/controllers	Contains the controllers
 * app/hooks 		Contains files that are autoloaded.
 * app/inc 			Contains tfiles to be loaded by Sys::loadInc($incNmae) or $conf['autoload'] = $incName
 * app/models 		Contains the models to be loaded by Sys::loadModel($modelName)
 * app/public 		Contains all files that must be accessible without being served by SysMVC (images, scripts...)
 * app/views 		Contains the views to be loaded by Sys::loadVire($viewName)
 *
 *
 * # Usage #
 * 
 * require('system/system.php');
 * 
 * //Uncomment this to show the required .htaccess
 * //Sys::debugShowNeededHtaccess();
 * 
 * $sysConf['baseUrl'] = 'http://server.com/baseUrl/'; //or '/baseUrl/'
 * $sysConf['defaultUri'] = 'page/index';
 * 
 * Sys::setup($sysConf);
 * Sys::load($_GET['uri']);
 * 
 * 
 * 
 */

/**
 * Main and only class of Sys.
 */

namespace olivierdalang\mvc2000;

class Sys{

	public static $b = '';						//holds the base url as a string, usefull to create absolute urls...
	public static $db = null;					//holds the PDO db handle if one has been loaded
	public static $infos = [];					//holds informations such as the loaded controller, its methods, etc...
	
	private static $m;							//holds the models that have been loaded (object)
	private static $defaultUri = 'home/index';	//set this using setup($conf['defaultUri'])
	private static $autoload = [];				//set this using setup($conf['autoload'])

	/**
	 * Loads the parameters. Parameters are :
	 * String $conf['baseUrl']  : 	the base url path for absolute links (e.g. linking stylesheets...)
	 * String $conf['defaultUri'] : the default uri ( controller/method/params ) to load when none is give (e.g. homepage)
	 * Array $conf['autoload'] :	an array of scripts to autoload (scipts must be in app/inc). Loading happens just before the controller loading.
	 * String $conf['dsn'] :		A PDO connection string. If given, the loadDB method is run.
	 * @param array $conf
	 */
	static function setup($conf){
		if(isset($conf['baseUrl']))
			self::$b = $conf['baseUrl'];

		if(isset($conf['defaultUri']))
			self::$defaultUri = $conf['defaultUri'];

		if(isset($conf['autoload']))
			self::$autoload = $conf['autoload'];

		if(isset($conf['dsn']))
			self::loadDB($conf['dsn']);
	}

	/**
	 * This is an alias of loadUri()
	 * @param string uri 
	 */
	static function load($uri){
		self::loadUri($uri);
	}


	/**
	 * Decomposes the uri, and loads the controller and calls its method
	 * @param string uri
	 */
	private static function loadUri($uri){

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
	 * Loads a controller
	 * @param string $name The controller name (must correspond to the file name and the class name of the controller)
	 * @param string $method The method that is called
	 * @param array $parameters The parameters to pass to the method
	 * @return void
	 */
	private static function loadController($name, $method, $parameters=array()){

		if( !empty(self::$autoload)){
			//todo maybe : if self::$autoload == '*', load the whole directory ?
			foreach(self::$autoload as $file_to_autoload){
				self::loadInc($file_to_autoload);
			}
		}

		$file_to_load = 'app/controllers/'.$name.'.php';

		if( ! file_exists( $file_to_load ) ){
			header("HTTP/1.0 404 Not Found");
			throw new Exception('Controller file <b>'.$file_to_load.'</b> not found.' );
		}

		require_once($file_to_load);

		if( ! class_exists  ($name ) ){
			header("HTTP/1.0 500 Internal Server Error");
			throw new Exception('Controller <b>'.$name.'</b> class not found.' );
		}

		$controller = new $name();

		if( ! method_exists($controller, $method ) ){
			header("HTTP/1.0 404 Not Found");
			throw new Exception('Method <b>'.$method.'()</b> not found in controller <b>'.$name.'</b>' );
		}

		if( ! is_callable ([$controller, $method] ) ){
			header("HTTP/1.0 405 Method Not Allowed");
			throw new Exception('<b>'.$method.'()</b> in controller <b>'.$name.'</b> is <b>private</b> !' );
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
			throw new Exception('Model file <b>'.$file_to_load.'</b> not found.' );
		}

		require_once($file_to_load);

		$modelName = $model.'Model';
		if( ! class_exists  ($modelName) ){
			header("HTTP/1.0 500 Internal Server Error");
			throw new Exception('Model <b>'.$modelName.'</b> class not found.' );
		}

		if(self::$m === null)
			self::$m = new stdClass();
		
		self::$m->$model = new $modelName();
		
		return self::$m->$model;
	}
	static function loadInc($incName){
		$file_to_load = 'app/inc/'.$incName;
		if( ! file_exists( $file_to_load ) ){
			header("HTTP/1.0 500 Internal Server Error");
			throw new Exception('Include file <b>'.$file_to_load.'</b> not found.' );
		}
		require_once($file_to_load);

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
			throw new Exception('The view file <b>"'.$file_to_load.'"</b> could not be loaded.');
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

	static function loadDB($dsn){
		self::$db = new PDO($dsn);
	}


	/**
	 * Creates the default document tree.
	 * @return void
	 */
	static function postPackageInstall(){

		$directory = 'vendor/olivierdalang/mvc2000/default_project';

		$it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));

		$it->rewind();

		while($it->valid()) {

		    if (!$it->isDot()) {
		        echo 'SubPathName: ' . $it->getSubPathName() . "\n";
		        echo 'SubPath:     ' . $it->getSubPath() . "\n";
		        echo 'Key:         ' . $it->key() . "\n\n";
		    }

		    $it->next();
		}


	}

}
