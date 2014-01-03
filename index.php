<?php
/* lp app root */
// ↑____ for aoi . Do Not Delete it.
/****  load lp framework  ***/
define( 'DS' , DIRECTORY_SEPARATOR );
define( 'AROOT' , dirname( __FILE__ ) . DS  );
define('DEBUG', true);

//ini_set('include_path', dirname( __FILE__ ) . DS .'_lp' ); 
//include_once( '_lp'.DS .'lp.init.php' );
include AROOT . '_lp'.DS .'lp.init.php';
/**** lp framework init finished ***/
