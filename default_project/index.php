<?php

require('system/system.php');
require('app/config.php');

//Uncomment this to show the required .htaccess
//Sys::debugShowNeededHtaccess();

Sys::setup($sysConf);
Sys::load($_GET['uri']);