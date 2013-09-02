<?php

if( !defined('AROOT') ) die('NO AROOT!');
if( !defined('DS') ) define( 'DS' , DIRECTORY_SEPARATOR );

// define constant
define( 'IN' , true );

define( 'ROOT' , dirname( __FILE__ ) . DS );
define( 'CROOT' , ROOT . 'core' . DS  );

// define 
error_reporting(E_ALL^E_NOTICE);
ini_set( 'display_errors' , true );

include_once( CROOT . 'lib' . DS . 'core.function.php' );
@include_once( AROOT . 'lib' . DS . 'app.function.php' );

include_once( CROOT . 'config' .  DS . 'core.config.php' );
include_once( AROOT . 'config' . DS . 'app.config.php' );



$c = $GLOBALS['c'] = v('c') ? v('c') : c('default_controller');
$a = $GLOBALS['a'] = v('a') ? v('a') : c('default_action');

$c = basename(strtolower( z($c) ));
$a =  basename(strtolower( z($a) ));

$post_fix = '.class.php';

$cont_file = AROOT . 'controller'  . DS . $c . $post_fix;
$class_name = $c .'Controller' ; 
if( !file_exists( $cont_file ) )
{
	$cont_file = CROOT . 'controller' . DS . $c . $post_fix;
	if( !file_exists( $cont_file ) ) die('Can\'t find controller file - ' . $c . $post_fix );
} 


require_once( $cont_file );
if( !class_exists( $class_name ) ) die('Can\'t find class - '   .  $class_name );


$o = new $class_name;
if( !method_exists( $o , $a ) ) die('Can\'t find method - '   . $a . ' ');


// args binding
// strongly inspired by luofei ( http://weibo.com/luofei614 )
$method =   new ReflectionMethod($o, $a);
$params =  $method->getParameters();

$args=array();

foreach($params as $param)
{
   $name=$param->getName();
   
   if( $param->isDefaultValueAvailable() )
   {
   		// get default value
   		$dval = $param->getDefaultValue();
   		$reg = '/\:(.+?)\|(.*)$/is';

   		// is filter
   		if( preg_match( $reg , $dval , $out ) )
   		{
   			$fliter_func = t($out[1]);
   			$info = t($out[2]);
   			$ret = input_check( v($name) , $fliter_func , $info );
   			$args[$name] = $ret;
   		}
   		else
   		{
   			// not filter so set as default value
   			if( isset($_REQUEST[$name]) )
   			{
   				$args[$name] =  v($name);

		       if(is_string($args[$name]))
		           $args[$name]=t($args[$name]);
		   }
		   else
		   {
		   		$args[$name] = $dval;
		   }
   		}

   }

}

$method->invokeArgs($o,$args);


//if(strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE && @ini_get("zlib.output_compression")) ob_start("ob_gzhandler");
//call_user_func( array( $o , $a ) );




