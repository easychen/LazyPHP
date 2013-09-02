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


require_once( CROOT . 'lib' . DS . 'Tonic' . DS . 'Autoloader.php' ) ;

$c = $GLOBALS['c'] = v('c');
if( strlen( $c ) > 0 )
{
	$resource_file = AROOT . 'resource' . DS . basename($c) . '.class.php';
	if( file_exists( $resource_file ) ) @include_once( $resource_file );

}

$model_function_file = AROOT . 'model' . DS . basename($c) . '.function.php';
if( file_exists( $model_function_file ) )  
	@include_once( $model_function_file );

$config = array(
    /*'load' => array(
        '*.resouce.php'// load example resources
    ),*/

    #'cache' => new Tonic\MetadataCacheMC() // use the metadata cache
    
    #'mount' => array('Tyrell' => '/nexus'), // mount in example resources at URL /nexus
    #'cache' => new Tonic\MetadataCacheFile('/tmp/tonic.cache') // use the metadata cache
    #'cache' => new Tonic\MetadataCacheAPC // use the metadata cache
);

$app = new Tonic\Application($config);

$request = new Tonic\Request();

try 
{

    $resource = $app->getResource($request);

    $response = $resource->exec();

} 
catch (Tonic\NotFoundException $e) 
{
    $response = new Tonic\Response(404, $e->getMessage());

} 
catch (Tonic\UnauthorizedException $e) 
{
    $response = new Tonic\Response(401, $e->getMessage());
    $response->wwwAuthenticate = 'Basic realm="My Realm"';

} 
catch (Tonic\Exception $e) 
{
    $response = new Tonic\Response($e->getCode(), $e->getMessage());
}

$response->output();



