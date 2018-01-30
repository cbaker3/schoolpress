$(document).ready(function() {

	$("#changepassword").validate({		

		onkeyup:false,

		rules: {

			oldpw: {

				required: true,				

			},

			newpw: {

				required: true,

				minlength: 2

			},

			newrpw: {

				required: true,

				equalTo: "#newpw"

			}

		},

		messages: {

			oldpw: {

				required: "Please enter Current Password"				

			},

			newpw: {

				required: "Please enter New Password"

			},

			newrpw: {

				required: "Please enter Confirm New Password",

				equalTo : "Confirm New Password Should be same as New Password"

			}

		},

		submitHandler: function(form) {

			$('#Change').attr("disabled", 'disabled');

			$( '#message_response' ).html('');

			var data=$('#changepassword').serializeArray();

			data.push({name: 'action', value: 'changepassword'});

			$.ajax({

					type: "POST",

					url: ajax_url,

					data: data,
                    beforeSend:function () {
							$.fn.notify('loader',{'desc':'Loading..'});
							$('#Change').attr("disabled", 'disabled');
						},
					success: function(response) {

						var response_data = jQuery.parseJSON(response);

						$('#Change').removeAttr('disabled');

						if( response_data.success == 1 ) {

							$( '#message_response' ).html( "<div class='alert alert-success'>"+response_data.msg+"</div>" );

							$( '#changepassword' ).find("input[type=password]").val("");

						} else	{

							$( '#message_response' ).html( "<div class='alert alert-danger'>"+response_data.msg+"</div>" );

						}

						$('.form-control').val('');	


					},
					error:function () {
							$('#formresponse').html("<div class='alert alert-danger'>Something went wrong!</div>");
							$('#Change').removeAttr('disabled');
						},
						complete:function(){
							$('.pnloader').remove();
							$('#Change').removeAttr('disabled');
						}

				});

		}

	});

});