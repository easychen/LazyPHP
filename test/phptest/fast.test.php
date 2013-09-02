<?
class TestOfFastfunction extends UnitTestCase
{
	
	
	public function test_v()
	{
		// hit
		$_REQUEST['name'] = 'oldtimes';
		$this->assertEqual( v('name') , 'oldtimes' );
		
		// not hit 
		unset($_REQUEST['name']) ;
		$this->assertEqual( v('name') , false );
		
	} 
	
	public function test_z()
	{
		$tmp = '<a href="http://news.com">news</a>';
		$this->assertEqual( z($tmp) , 'news' );
	}
	
	public function test_c()
	{
		$GLOBALS['config']['unittest'] = 'ing';
		$this->assertEqual( c('unittest') , 'ing' );
		
		unset( $GLOBALS['config']['unittest'] );
		$this->assertFalse( c('unittest') );
	}

	public function test_g()
	{
		unset( $GLOBALS['test'] );
		$this->assertFalse( g('test') );
		
		$GLOBALS['test'] = 'im';
		$this->assertEqual( g('test') , 'im' );
	}
	
	public function test_u()
	{
		$this->assertEqual( u('?c=user&a=login') , '%3Fc%3Duser%26a%3Dlogin' );
	}

	public function test_uid()
	{
		@session_start();
		$_SESSION['uid'] = 23;
		$this->assertEqual( uid() , 23 );
	}

	public function test_uname()
	{
		@session_start();
		$_SESSION['uname'] = 'easychen';
		$this->assertEqual( uname() , 'easychen' );
	}
	
	public function test_text()
	{
		$this->assertEqual( __('login') , '登入' );
		$this->assertEqual( __('hello%s' , 'Aoi') , '你好Aoi' );
		$this->assertEqual( __('Not exists' ) , 'Not exists' );
		$this->assertEqual( __('Not exists %s' , 'Money'  ) , 'Not exists Money' );
		
	}

	public function test_resource_helpers()
	{
		$this->assertEqual( image('go.png') , 'static/image/go.png' );
		$this->assertEqual( css('go.css') , 'static/css/go.css' );
		$this->assertEqual( js('go.js') , 'static/script/go.js' );
	}

	public function test_wintval()
	{
		$this->assertEqual( wintval('r23463564d35f4g4h35') , '23463564354435' );
	}

	public function  test_hooks()
	{
		add_action('LP_TEST_HOOK', function( $data )
		{
			return strtoupper($data);
		} );

		$this->assertTrue( has_hook('LP_TEST_HOOK') );

		$this->assertEqual( do_action( 'LP_TEST_HOOK' , 'abc' ) , 'ABC' );

		remove_hook('LP_TEST_HOOK');

		$this->assertFalse( has_hook('LP_TEST_HOOK') );  

	}
	
	// render
	// ajax_echo 
	// info_page
	
		
} 
