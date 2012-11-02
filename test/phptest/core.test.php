<?
define( 'SITE_URL' , 'http://'.c('site_domain') . '/' );

class TestOfCorefunction extends WebTestCase
{
	
	function test_ajax_echo()
	{
		ob_start();
		ajax_echo('test');
		$out1 = ob_get_contents();
		ob_end_clean();
		
		$this->assertEqual( $out1 , 'test' );
	}
	
	
	function test_info_page()
	{
		ob_start();
		info_page('hello kitty');
		$out1 = ob_get_contents();
		ob_end_clean();
		
		$this->assertTrue( strpos( $out1 , '系统消息'  ) );
		$this->assertTrue( strpos( $out1 , 'hello kitty'  ) );
	}
	
	function test_safe_check()
	{
		if( file_exists( AROOT . 'controller' . DS . 'default.class.php' ) )
			$this->assertEqual( 'bad request' , $this->get( SITE_URL . 'controller/default.class.php' )  );
			
			
		if( file_exists( AROOT . 'controller' . DS . 'app.class.php' ) )
			$this->assertEqual( 'bad request' , $this->get( SITE_URL . 'controller/app.class.php' )  );	
			
	}
	
	
	
	// render
	
	
	
		
} 
