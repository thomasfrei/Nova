<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

defined("BASE") or define("BASE", realpath(dirname(__FILE__).'/../') .'/');
defined("BASEPATH") or define("BASEPATH", BASE . 'Library/');
defined("APPPATH") or define("APPPATH", BASE . 'Application/');
defined("TESTPATH") or define("TESTPATH", BASE . 'Tests/testfiles/');
