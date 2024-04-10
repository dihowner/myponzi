$(document).ready(function() {
	$('#search_participant').keyup(function(e) {
		e.preventDefault();
		var result = $('#result');
		var search_participant = $(this).val();
		var data = 'search_participant='+search_participant;
		if(search_participant == '')
		{
			result.hide();
		}
		else
		{
				result.show();
			$.ajax({
				url: '../action?GETUSER',
				data: data,
				type: 'post',
				beforeSend: function(){
						result.addClass('alert alert-info').html("<font size='38px'><i class='fa fa-spinner fa-lg fa-spin'></i> Please wait...</font>");
					 
				},
				success: function(msg){
					setTimeout(function() {
						result.removeClass('alert alert-info');
						result.html(msg);
						}, 1000);
					  return true;
				}
				
			});
		}
	});
});