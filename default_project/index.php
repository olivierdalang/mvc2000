<?php

require 'vendor/autoload.php';
require 'app/config.php';

class_alias('\olivierdalang\mvc2000\Sys','Sys');

Sys::setup($sysConf);
Sys::load($_GET['uri']);