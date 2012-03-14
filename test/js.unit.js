 test("hello", function() 
 {
 	ok(true, "world");
 	
 	show_pop_box('abc','testdiv');
 	ok($('#lp_pop_box').css( 'display')!='none');
 	
 	$('#lp_pop_box').css( 'display' , 'none' );
 	$('#lp_pop_container').css( 'display' , 'none' );
 	equal($('#lp_pop_container').html() , 'abc');
 	
 });