<?php

use \olivierdalang\mvc2000\Sys;

Sys::$b = '/'; 						//holds the base url as a string, usefull to create absolute urls...
Sys::$c['debug'] = 'false'; 		//an example custom configuration variable
Sys::$defaultUri = 'page/index';	//default controller/method when none is specified

//modules to load
Sys::loadPlugin('database',['pgsql:host=localhost;port=5432;dbname=mydb;user=postgres;password=postgres']);		