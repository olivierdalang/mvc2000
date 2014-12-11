<?php

class Page{

	function index(){

		$data['clients'] =  \Sys::m('clients')->getAll();

		\Sys::v('main.php',$data);

	}

	function debug(){

		\Sys::debug_showNeededHtaccess();

	}

}