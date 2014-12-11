<?php

require 'vendor/autoload.php';
require 'app/config.php';

use \olivierdalang\mvc2000\Sys as Sys;

Sys::setup($sysConf);
Sys::load($_GET['uri']);