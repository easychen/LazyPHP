<?php

if( !defined('IN') ) die('bad request');


class coreController 
{
	function __construct()
	{
		// load module functions
		$module_function_file = AROOT . 'module/' . g('c') . '.function.php';
		if( file_exists( $module_function_file ) ) require_once( $module_function_file );
	}
	
	public function index()
	{
		// 
	} 
}

