<?

if( defined('SAE_APPNAME') )
{
	class TestOfSAEfunction extends UnitTestCase
	{
		
		// replication
		function test_sae_db()
		{
			$db = db();
			$data= reset(get_data( "show global variables like 'read_only';" , $db ));	
			$this->assertEqual( $data['Value'] , 'OFF' );
			
			$dbr = db_read();
			$data2= reset(get_data( "show global variables like 'read_only';" , $dbr ));	
			$this->assertEqual( $data2['Value'] , 'ON' );
			
			// auto
			$data3= reset(get_data( "show global variables like 'read_only';" ));
			$this->assertEqual( $data2['Value'] , 'ON' );
				
		}
		
		

			
	} 
}



