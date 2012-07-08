<?php

// db functions
function db( $host = null , $port = null , $user = null , $password = null , $db_name = null )
{
	$db_key = MD5( $host .'-'. $port .'-'. $user .'-'. $password .'-'. $db_name  );
	
	if( !isset( $GLOBALS['LP_'.$db_key] ) )
	{
		include_once( AROOT .  'config/db.config.php' );
		//include_once( CROOT .  'lib/db.function.php' );
		
		$db_config = $GLOBALS['config']['db'];
		
		if( $host == null ) $host = $db_config['db_host'];
		if( $port == null ) $port = $db_config['db_port'];
		if( $user == null ) $user = $db_config['db_user'];
		if( $password == null ) $password = $db_config['db_password'];
		if( $db_name == null ) $db_name = $db_config['db_name'];
		
		if( !$GLOBALS['LP_'.$db_key] = mysql_connect( $host.':'.$port , $user , $password , true ) )
		{
			//
			echo 'can\'t connect to database';
			return false;
		}
		else
		{
			if( $db_name != '' )
			{
				if( !mysql_select_db( $db_name , $GLOBALS['LP_'.$db_key] ) )
				{
					echo 'can\'t select database ' . $db_name ;
					return false;
				}
			}
		}
		
		mysql_query( "SET NAMES 'UTF8'" , $GLOBALS['LP_'.$db_key] );
	}
	
	return $GLOBALS['LP_'.$db_key];
}

function s( $str , $db = NULL )
{
	if( $db == NULL ) $db = db();
	return   mysql_real_escape_string( $str , $db )  ;
	
}

// $sql = "SELECT * FROM `user` WHERE `name` = ?s AND `id` = ?i LIMIT 1 "
function prepare( $sql , $array )
{
	
	foreach( $array as $k=>$v )
		$array[$k] = s($v );
	
	$reg = '/\?([is])/i';
	$sql = preg_replace_callback( $reg , 'prepair_string' , $sql  );
	$count = count( $array );
	for( $i = 0 ; $i < $count; $i++ )
	{
		$str[] = '$array[' .$i . ']';	
	}
	
	$statement = '$sql = sprintf( $sql , ' . join( ',' , $str ) . ' );';
	eval( $statement );
	return $sql;
	
}

function prepair_string( $matches )
{
	if( $matches[1] == 's' ) return "'%s'";
	if( $matches[1] == 'i' ) return "'%d'";	
}


function get_data( $sql , $db = NULL )
{
	if( $db == NULL ) $db = db();
	
	$GLOBALS['LP_LAST_SQL'] = $sql;
	$data = Array();
	$i = 0;
	$result = mysql_query( $sql ,$db );
	
	if( mysql_errno() != 0 )
		echo mysql_error() .' ' . $sql;
	
	while( $Array = mysql_fetch_array($result, MYSQL_ASSOC ) )
	{
		$data[$i++] = $Array;
	}
	
	if( mysql_errno() != 0 )
		echo mysql_error() .' ' . $sql;
	
	mysql_free_result($result); 

	if( count( $data ) > 0 )
		return $data;
	else
		return false;
}

function get_line( $sql , $db = NULL )
{
	$data = get_data( $sql , $db  );
	return @reset($data);
}

function get_var( $sql , $db = NULL )
{
	$data = get_line( $sql , $db );
	return $data[ @reset(@array_keys( $data )) ];
}

function last_id( $db = NULL )
{
	if( $db == NULL ) $db = db();
	return get_var( "SELECT LAST_INSERT_ID() " , $db );
}

function run_sql( $sql , $db = NULL )
{
	if( $db == NULL ) $db = db();
	$GLOBALS['LP_LAST_SQL'] = $sql;
	return mysql_query( $sql , $db );
}

function db_errno( $db = NULL )
{
	if( $db == NULL ) $db = db();
	return mysql_errno( $db );
}


function db_error( $db = NULL )
{
	if( $db == NULL ) $db = db();
	return mysql_error( $db );
}

function last_error()
{
	if( isset( $GLOBALS['LP_DB_LAST_ERROR'] ) )
	return $GLOBALS['LP_DB_LAST_ERROR'];
}

function close_db( $db = NULL )
{
	if( $db == NULL )
		$db = $GLOBALS['LP_DB'];
		
	unset( $GLOBALS['LP_DB'] );
	mysql_close( $db );
}

// ==================================================================
//
// 插入数据
//
// ------------------------------------------------------------------


function db_insert($table,$data,$replace=false){
	if(is_string($data)){
		$data=db_create($data);
	}
	$keys=array_keys($data);
	$values=array_values($data);
	$values=array_map('s', $values);//安全过滤
	$type=$replace?'REPLACE':'INSERT';
	return run_sql("{$type} INTO `{$table}`(`".implode("`,`", $keys)."`) VALUES('".implode("','", $values)."')");
}

// ==================================================================
//
// 更新数据
//
// ------------------------------------------------------------------

function db_update($table,$data,$where){
	if(is_string($data)){
		$data=db_create($data);
	}
	$fields=array();
	foreach($data as $key=>$value){
		$fields[]="`{$key}`='".s($value)."'";//安全过滤
	}
	return run_sql("UPDATE `{$table}` SET ".implode(',', $fields)." WHERE {$where}");
}

// ==================================================================
//
// 创建数据
// $data的格式：
// field1,field2,field3
// or
// fiedl1,field2,field3:fun // 使用过滤函数过滤
// or
// field1,^field2,field3|fun,filed4:fun
// or
//！field1,field2,filed3:fun
//
// ------------------------------------------------------------------

function db_create($data){
	//判断是否为非模式，字符串以！开头
	if('!'==substr($data, 0,1)){
		$not=true;
		//如果为非模式，去掉开头的！
		$data=substr($data,1);
	}else{
		$not=false;
	}
	$field_filter=explode(':', $data);
	//提取字段
	$fields=explode(',', $field_filter[0]);
	//提取过滤函数
	$filter=isset($field_filter[1])?$field_filter[1]:false;
	$postData=$_POST;
	if($not){//非模式的处理
		foreach ($fields as $field ){
			unset($postData[$field]);
		}
		if($filter) $postData=array_map($filter, $postData);
		return $postData;
	}
	$result=array();
	//循环字段
	foreach ($fields as $field) {
		$not_filter=false;
		//字段如以^开头，不进行过滤函数过滤。
		if('^'==substr($field, 0,1)){
			$not_filter=true;
			$field=substr($field, 1);
		}
		//字段如果有|隔开一个函数名还需要用单独的过滤函数过滤
		$field_single=explode('|', $field);
		$field=$field_single[0];
		$single_filter=isset($field_single[1])?$field_single[1]:false;
		//数据过滤
		if($single_filter) $postData[$field]=$single_filter($postData[$field]);
		if($filter) $postData[$field]=$filter($postData[$field]);
		//加入result
		$result[$field]=$postData[$field];
	}
	return $result;
	
}

?>