<?php
/* lp app root */
// ↑____ for aoi . Do Not Delete it.
/****  load lp framework  ***/
define( 'DS' , DIRECTORY_SEPARATOR );
define( 'AROOT' , dirname( __FILE__ ) . DS  );

//ini_set('include_path', dirname( __FILE__ ) . DS .'_lp' ); 
include_once( '_lp'.DS .'tn.init.php' );
/**** lp framework init finished ***/

// use Tonic as rest lib
// visit https://github.com/peej/tonic for more