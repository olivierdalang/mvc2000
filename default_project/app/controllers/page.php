<?php

use \olivierdalang\mvc2000\Sys;

class PageController{

	function index(){

		$data['clients'] =  Sys::m('clients')->getAll();

		Sys::v('main.php',$data);

	}

}