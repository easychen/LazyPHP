<?php

if( !defined('AROOT') ) die('NO AROOT!');
if( !defined('DS') ) define( 'DS' , DIRECTORY_SEPARATOR );


// define constant
define( 'IN' , true );

define( 'ROOT' , dirname( __FILE__ ) . DS );
define( 'CROOT' , ROOT . 'core' . DS  );
define( 'TROOT' , ROOT . 'simpletest' . DS  );


// define 
error_reporting(E_ALL^E_NOTICE);
ini_set( 'display_errors' , true );

include_once( CROOT . 'lib' . DS . 'core.function.php' );
@include_once( AROOT . 'lib' . DS . 'app.function.php' );

include_once( CROOT . 'config' .  DS . 'core.config.php' );
include_once( AROOT . 'config' . DS . 'app.config.php' );

require_once( TROOT . 'autorun.php');
require_once( TROOT . 'web_tester.php' );


$test = new TestSuite('LazyPHP Test Center');

foreach( glob( AROOT . 'test'. DS .'phptest' . DS . '*.test.php' ) as $f )
	$test->addFile( $f );


//$test->run(new HtmlReporter('UTF-8'));
unset( $test );



