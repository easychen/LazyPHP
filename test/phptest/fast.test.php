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
	
	// render
	// ajax_echo 
	// info_page
	
		
} 
