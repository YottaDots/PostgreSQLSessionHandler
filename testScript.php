<?php
include_once('../dbconfig.php');
include_once('classes/yottadotssessionhandler.php');
$handler = new yottadotssessionhandler();
//and now you can start your sessions. all sessiondata is save in, updated and collected from in a seperate database
session_set_save_handler($handler, true);
session_start();
$_SESSION['testdata'] = 'when you look in your database this sentence can be found in it';
print_r(session_id());

?>

