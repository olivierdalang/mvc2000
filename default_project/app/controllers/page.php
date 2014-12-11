<?php

use \olivierdalang\mvc2000\Sys;

class Page{

	function index(){

		$data['clients'] =  Sys::m('clients')->getAll();

		Sys::v('main.php',$data);

	}

	function debug(){

		Sys::debug_showNeededHtaccess();

	}

}