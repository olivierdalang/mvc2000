<?php

require 'vendor/autoload.php';
require 'app/config.php';

use \olivierdalang\mvc2000\Sys;

Sys::loadUri($_GET['uri']);