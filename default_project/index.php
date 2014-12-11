<?php

require 'vendor/autoload.php';
require 'app/config.php';

use \olivierdalang\mvc2000\Sys;

Sys::setup($sysConf);
Sys::loadUri($_GET['uri']);