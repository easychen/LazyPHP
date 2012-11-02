/*
send form data via ajax and return the data to callback function 
*/
function send_form( name , func )
{
	var url = $('#'+name).attr('action');
	
	var params = {};
	$.each( $('#'+name).serializeArray(), function(index,value) 
	{
		params[value.name] = value.value;
	});
	
	
	$.post( url , params , func );	
}

/*
send form data via ajax and show the return content to pop div 
*/

function send_form_pop( name )
{
	return send_form( name , function( data ){ show_pop_box( data ); } );
}

/*
send form data via ajax and show the return content in front of the form 
*/
function send_form_in( name )
{	
	return send_form( name , function( data ){ set_form_notice( name , data ) } );
}


function set_form_notice( name , data )
{
	data = '<span class="label label-important">' + data + '</span>';
	
	if( $('#form_'+name+'_notice').length != 0 )
	{
		$('#form_'+name+'_notice').html(data);
	}
	else
	{
		var odiv = $( "<div class='form_notice'></div>" );
		odiv.attr( 'id' , 'form_'+name+'_notice' );
		odiv.html(data);
		$('#'+name).prepend( odiv );
	} 
	
}


function show_pop_box( data , popid )
{
	if( popid == undefined ) popid = 'lp_pop_box'
	//console.log($('#' + popid) );
	if( $('#' + popid).length == 0 )
	{
		var did = $('<div><div id="' + 'lp_pop_container' + '"></div></div>');
		did.attr( 'id' , popid );
		did.css( 'display','none' );
		$('body').prepend(did);
	} 
	
	if( data != '' )
		$('#lp_pop_container').html(data);
	
	var left = ($(window).width() - $('#' + popid ).width())/2;
	
	$('#' + popid ).css('left',left);
	$('#' + popid ).css('display','block');
}

function hide_pop_box( popid )
{
	if( popid == undefined ) popid = 'lp_pop_box'
	$('#' + popid ).css('display','none');
}



/* post demo
$.post( 'url&get var'  , { 'post':'value'} , function( data )
{
	var data_obj = jQuery.parseJSON( data );
	console.log( data_obj  );
	
	if( data_obj.err_code == 0  )
	{
					
	}
	else
	{
		
	}	
} );

*/