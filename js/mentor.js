$(document).ready(function(){
	var result = $("#result");
	var processing = $("#processing");

	//LOgin in
	$("#loginPO").click(function(PI) {
		PI.preventDefault();
		$("#loginPO").fadeOut();
		var gLogin = $("#gLogin").serialize();
		var ptc_username = $("#ptc_username").val();
		var ptc_password = $("#ptc_password").val();
		
		if(ptc_username == "" || ptc_password == "")
		{
			alert("Please fill all field...");
			result.addClass("alert alert-info").html("No entry made");
			$("#loginPO").fadeIn();
		}
		else
		{
			// alert(gLogin);
			
			$("#ptc_username").prop("disabled", true);
			$("#ptc_password").prop("disabled", true);
			$.ajax(
			{
				url: "action.php?loginCLIENT",
				type: "post",	
				data: gLogin,
				beforeSend:  function(){
					result.addClass("alert alert-info").html("<font size='4px'><b><img src='img/loader.gif' width='40' height='30'> Authenticating Account</b></font>");
				},
				success: function(msg)
				{
					if(msg == 'wrong mail')
					{
						$("#ptc_username").prop("disabled", false);
						$("#ptc_password").prop("disabled", false);
						result.html("Invalid Email Address Supplied");
						$("#loginPO").fadeIn(2000);
					}
					else if(msg == 'nouser')
					{
						$("#ptc_username").prop("disabled", false);
						$("#ptc_password").prop("disabled", false);
						$("#loginPO").fadeIn(2000);
						result.html("<font size='4px'><b>Bad combination of username or password</b></font>");
						alert("Bad combination of username or password");
					}
					else 
					{
						if(msg == 'logged')
						{
							result.removeClass("alert alert-info");
							result.addClass("alert alert-success").html("<i class='fa fa-smile-o'></i> Success! You will be redirected soon");
							
							setInterval(function () {
								window.location = "private/dashboard";
									
							}, 3000);
						
						}
					}
				}
			});
		}
	});
	
	
	//Saving Account Details
	$("#saveAccnt").click(function(PI) {
		PI.preventDefault();
		var getAllBankINFO = $("#getAllBankINFO").serialize();
		var accnt_number = $("#accnt_number").val();
		var bank_name = $("#bank_name").val();
		var accnt_name = $("#accnt_name").val();
		
		if(accnt_number == "" || bank_name == "" || accnt_name == "")
		{
			alert("Please fill all field");
		}
		
		else
		{
			$("#bank_name").prop("disabled", true);
			$("#accnt_name").prop("disabled", true);
			$("#accnt_number").prop("disabled", true);
			
			$.ajax({
				url: "../action.php?SaveACCOUNT",
				type: "post",
				data: getAllBankINFO,
				beforeSend: function(){
					result.addClass("alert alert-info").html("<b><img src='../img/loader.gif' width='40' height='30'> Processing</b>");
				},
				success: function(msg){
					// result.removeClass("alert alert-info");
					result.html(msg);					
					if(msg == "Error in saving account details")
					{	
						alert("Error in saving account details");
						$("#bank_name").prop("disabled", false);
						$("#accnt_name").prop("disabled", false);
						$("#accnt_number").prop("disabled", false);
					}
					else if(msg == "Account number already exists")
					{	
						alert("Account number already exists");
						$("#bank_name").prop("disabled", false);
						$("#accnt_name").prop("disabled", false);
						$("#accnt_number").prop("disabled", false);
						// window.location.href="bank_info";
					}
					else if(msg == "Invalid Account Number")
					{	
						alert("Invalid Account Number");
						$("#bank_name").prop("disabled", false);
						$("#accnt_name").prop("disabled", false);
						$("#accnt_number").prop("disabled", false);
						// window.location.href="bank_info";
					}
					else
					{
						if(msg == "Your banking details has been added successfully")
						{	
							alert("Your banking details has been added successfully");
							$("#saveAccnt").prop("disabled", true);
							
							window.location.href="dashboard";
							
						}
					}
					// else
					// {	
						// window.location.href="bank_info";
					// }
				}
				
			});
		}
		
	});
	
	
	
	//fetching ph details 
	load_data = {'fetch':1};
	window.setInterval(function(){
	 $.post('../action.php?FetchPH', load_data,  function(data) {
		$('#ph_order').html(data);
		var scrolltoh = $('#ph_order')[0].scrollHeight;
		$('#ph_order').scrollTop(scrolltoh);
	 });
	}, 1000);
	
	
	//fetching ph details 
	load_data = {'fetch':1};
	window.setInterval(function(){
	 $.post('../action.php?MergeGH', load_data,  function(data) {
		$('#mergegh_order').html(data);
		var scrolltoh = $('#mergegh_order')[0].scrollHeight;
		$('#mergegh_order').scrollTop(scrolltoh);
	 });
	}, 1000);
	
	
	//Submit Letter of testimony
	$("#submitGH").click(function(e){
		e.preventDefault();
		var ghletter_info = $("#ghletter_info").serialize();
		var gh_letter = $("#gh_letter").val();
		if(gh_letter == "")
		{
			alert("Empty Testimional not allowed");
		}
		else
		{
			
			$("#submitGH").fadeOut(2000);
			$("#gh_letter").prop("disabled", true);
			$.ajax({
				url: "../action.php?SaveGH_letter",
				type: "post",
				data: ghletter_info,
				beforeSend: function(){
					result.html("<div class='alert alert-info'><b><img src='../img/loader.gif' width='40' height='30' style='margin-top: -10px'/> Adding your testimony</b></div>");
				},
				success: function(msg)
				{
					alert("Testimonial saved");
					result.html("<div class='alert alert-success'><b>"+msg+"</b></div>");
				}
			});
		}
	});
	
	
	//OPening & Closing of ticket
	$("#OpenTicket").click(function(){
		$("#showTICKET").slideToggle(3000);
	});
	
	
	//Admin panel, Creating PH orders
	$("#auto_saveGH").click(function(e){
		e.preventDefault();
		$("#auto_saveGH").fadeOut(2000);
		var data = $('#all_PHINFO').serialize();
		$.ajax({
				url: "../action.php?auto_saveGH",
				type: "post",
				data: data,
				beforeSend: function(){
					result.html("<div class='alert alert-info'><b><img src='../img/loader.gif' width='40' height='30' style='margin-top: -3px'/> Creating Order</b></div>");
				},
				success: function(msg)
				{
					// alert("Testimonial saved");
					result.html("<div class='alert alert-success'><b>"+msg+"</b></div>");
				}
			});
	});
	
	
	//Sending Message In Contact
	// $("#Send_enquiry").click(function(e){
		// e.preventDefault();
		// alert(122);
		// var firstname = $("#firstname").val();
		// alert(firstname);
		// $.ajaax({
			// url: "../action.php?SAVENEWS",
			// type: "post",
			// data: data,
			// beforeSend: function(){
				// result.html("<div class='alert alert-info'><b><img src='img/loader.gif' width='40' height='30' style='margin-top: -3px'/> Saving Update</b></div>");
			// }
		// });
	// });
		
	//Editing Password
	$('#changePSWD_memeber').click(function(e){
		e.preventDefault();
		var newpass = $('#newpass').val();
		var re_newpass = $('#re_newpass').val();
		// alert(re_newpass)
		var ChangeINFO = $('#ChangeINFO').serialize();
		$("#newpass").prop("disabled", true);
		$("#re_newpass").prop("disabled", true);
		$("#changePSWD_memeber").prop("disabled", true);
		$.ajax(
		{
			url: "../action.php?ChangeMyPSWD",
			data: ChangeINFO,
			type: 'post',
			beforeSend: function(){
					result.addClass('alert alert-info').html("<font size='6'><i class='fa fa-spinner fa-lg fa-spin'></i> Please wait...</font>");
				 
			},
			success: function(msg){
				setTimeout(function() {
					if(msg == 'password different')
					{
						//since password are different then we need to open it back
						$("#newpass").prop("disabled", false);
						$("#re_newpass").prop("disabled", false);
						$("#changePSWD_memeber").prop("disabled", false);
						result.addClass('alert alert-info').html('<b>Password is not the same</b>');
					}
					else
					{
					// result.removeClass('alert alert-info');
						result.html(msg);
					}
					}, 1000);
				  return true;
			}
		});
	});

	
	//Cancelling Queue
	$("#cancelQueue").click(function(e){
		e.preventDefault();
		
		var data = $('#cancelQueueINFO').serialize();
		$.ajax({
				url: "../action.php?cancelQueue",
				type: "post",
				data: data,
				beforeSend: function(){
					result.html("<div class='alert alert-info'><b><img src='../img/loader.gif' width='40' height='30' style='margin-top: -3px'/> Cancelling Queue</b></div>");
				},
				success: function(msg){
					$("#cancelQueue").hide();
					result.html(msg);
				}
		});
	});
	
	
	//Adding to Queue
	$("#addQueue").click(function(e){
		e.preventDefault();
		var data = $('#addQueue_info').serialize();
		// $("#addQueue_pid").prop("disabled", true);
		// $("#amount_wallet").prop("disabled", true);
		// $("#addQueue").hide();
		$.ajax({
			url: "../action.php?addQueue",
			type: "post",
			data: data,
			beforeSend: function(){
				result.html("<div class='alert alert-info'><b><img src='../img/loader.gif' width='40' height='30' style='margin-top: -3px'/> Adding Queue</b></div>");
			},
			success: function(msg){
				result.html(msg);
			}
			
		});
	});
	
	
});


	//Load URL
	$(function(){
      // bind change event to select
      $('#dynamic_select').on('change', function () {
          var url = $(this).val(); // get selected value
          if (url) { // require a URL
              window.location = url; // redirect
          }
          return false;
      });
    });