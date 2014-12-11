<?php

use \olivierdalang\mvc2000\Sys;

Sys::$b = '/'; 						//holds the base url as a string, usefull to create absolute urls...
Sys::$defaultUri = 'page/index';	//default controller/method when none is specified
Sys::$autoload = ['convert.php'];	//files to autoload by every controller