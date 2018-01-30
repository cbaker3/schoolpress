	$(document).ready(function(){
		var table	=	$('#teacher_table').dataTable({
			"order": [],
			"columnDefs": [ {
			  "targets"  : 'nosort',
			  "orderable": false,
			}],
			responsive: true,
		});
		$('.dropdown-menu').click(function(e) {
			e.stopPropagation();
		});
		//$('.select_date').datepicker({autoclose: true,dateFormat: date_format, todayHighlight: true,  changeMonth: true,changeYear: true,yearRange: "-40:+0"});
		$( ".select_date" ).datepicker({
			autoclose: true,
			dateFormat: date_format,
			todayHighlight: true,
    		changeMonth: true,
            changeYear: true,
			yearRange: "-40:+0",
			beforeShow: function(input, inst) {
				$(document).off('focusin.bs.modal');
			},
			onClose:function(){
				$(document).on('focusin.bs.modal');
			},
  		});
		
		$('#ClassID').change(function () {
			$('#TeacherClass').submit();
		});
	
		$("#displaypicture").change(function(){	
			$('#test').html('');	
			var fsize = document.getElementById("displaypicture").files[0].size;
			var fileName = $(this).val();
			var maxsize = 3 * 1024 * 1024; //3145728			
			if( fsize > maxsize ) {
				$('#test').html( 'File Size should be less than 3 MB, Please select another file');
				$(this).val('');
			}	
			var fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
			if($.inArray(fileExtension, ['jpg','jpeg']) == -1) { 
				$('#test').html( 'Please select either jpg or jpeg file');
				$(this).val('');
			}
		});

		$("#TeacherEditForm").validate({
			rules: {
				Phone: {					
					number:true,	
					minlength: 10,
					maxlength: 10,								
				}
			}	
		});	
		$("#TeacherEntryForm").validate({
			rules: {
				firstname: "required",Address: "required",lastname: "required",
				Username: {
					required: true,
					minlength: 5
				},
				Password: {
					required: true,
					minlength: 4
				},
				ConfirmPassword: {
					required: true,
					minlength: 4,
					equalTo: "#Password"
				},
				Email: {
					required: true,
					email: true
				},
				Phone: {					
					number:true,	
					minlength: 10,
					maxlength: 10,								
				},
				zipcode:{number:true},
				whours:{number:true}
			},
			messages: {
				firstname: "Please Enter Teacher Name",Address: "Please Enter current Address",lastname: "Please Enter Last Name",
				Username: {
					required: "Please enter a username",
					minlength: "Username must consist of at least 5 characters"
				},
				Password: {
					required: "Please provide a password",
					minlength: "Password must be at least 5 characters long"
				},
				Confirm_password: {
					required: "Please provide a password",
					minlength: "Password must be at least 5 characters long",
					equalTo: "Please enter the same password as above"
				},
				Email: "Please enter a valid email address",
			},
			submitHandler: function(form){
				var data = new FormData();
				var fdata=$('#TeacherEntryForm').serializeArray();
				var file=$('#displaypicture')[0].files[0];
				data.append('action', 'AddTeacher');
				data.append('displaypicture',file);
				$.each(fdata,function(key,input){
						data.append(input.name,input.value);
				});
				data.append('data',fdata);
				$.ajax({
						type: "POST",
						url: ajax_url,
						data: data,
						cache: false,
						processData: false, 
						contentType: false,
						beforeSend:function () {
							$.fn.notify('loader',{'desc':'Saving data..'});
							$('#teacherform').attr("disabled", 'disabled');							
						},
						success: function(rdata) {
							$('#teacherform').removeAttr('disabled');
							if(rdata=='success')
							{
								$('#formresponse').html("<div class='alert alert-success'>Teacher added successfully !</div>");
								$('#TeacherEntryForm').trigger("reset");								
							}
							else
							{
								$('#formresponse').html("<div class='alert alert-danger'>"+rdata+"</div>");
							}
						},
						error:function () {
							$('#formresponse').html("<div class='alert alert-danger'>Something went wrong!</div>");
							$('#teacherform').removeAttr('disabled');
						},
						complete:function(){
							$('.pnloader').remove();
							$('#teacherform').removeAttr('disabled');
						}
				});
			}		
		});
		
		$(document).on('click','.ViewTeacher',function(e) {		
			e.preventDefault();
			var data=[];
			var tid=$(this).data('id');
			data.push({name: 'action', value: 'TeacherPublicProfile'},{name: 'id', value: tid});
			jQuery.post(ajax_url, data, function(pdata) {
				$('#ViewModalContent').html(pdata);
				$('#ViewModal').modal('show');
			});
		});
		
		$('#AddTeacher').on('click',function(e){
			e.preventDefault();
			$('#AddModal').modal('show');
		});
		
		$('#TeacherEntryForm').submit(function(e){
			e.preventDefault();
		});
		
		$('#TeacherImportForm').submit(function(e){
			e.preventDefault();
		});	
		
		
		$("#selectall").click(function(){
			if($(this).prop("checked")==true){
				$(".strowselect").prop('checked',true);
			}else{
				$(".strowselect").prop("checked",false);
			}
		});
		$('#bulkaction').change(function(){
			var op=$(this).val();
			if(op=='bulkUsersDelete'){
				var uids = $('input[name^="UID"]').map(function() {
						if($(this).prop('checked')==true)
						return this.value;
				}).get();
				if(uids.length==0){
					$.fn.notify('error',{'desc':'No user selected!'});
					return false;	
				} else {				
					new PNotify({
						title: 'Confirmation Needed',
						text: 'Are you sure want to delete?',
						icon: 'glyphicon glyphicon-question-sign',
						hide: false,
						confirm: {
							confirm: true
						},
						buttons: {
							closer: false,
							sticker: false
						},
						history: {
							history: false
						},
					}).get().on('pnotify.confirm', function(){					
						var data=new Array();
						data.push({name:'action',value:'bulkDelete'});
						data.push({name:'UID',value:uids});
						data.push({name:'type',value:'teacher'});
						$.ajax({
								type: "POST",
								url: ajax_url,	
								data:data,
								beforeSend: function(){
									$.fn.notify('loader',{'desc':'Deleting Students!'});
								},
								success: function(data) {
									$('.pnloader').remove();
									 if (data == 'success') {
										$.fn.notify('success', {'desc': 'Deleted successfully!'});
									} else {
										$.fn.notify('error', {'desc': 'Operation failed.Something went wrong!'});
									}
									$(location).attr('href', 'sch-teacher');
								   //$.fn.notify('success',{'desc':'Deleted successfully!'});								   
								},
								complete: function(){
									$('.pnloader').remove();
								}
						});
					});
				}
			}
		});
		
		$("#selectall").click(function(){
			if($(this).prop("checked")==true){
				$(".tcrowselect").prop('checked',true);
			}else{
				$(".tcrowselect").prop("checked",false);
			}
		});


					   $('#displaypicture').change(function(){
		               var fp = $("#displaypicture");
		               var lg = fp[0].files.length; // get length
		               var items = fp[0].files;
		               var fileSize = 0;
		           
		           if (lg > 0) {
		               for (var i = 0; i < lg; i++) {
		                   fileSize = fileSize+items[i].size; // get file size
		               }
		               if(fileSize > 3000000) {
		                   document.getElementById("test").innerHTML = "File size must not be more than 3 MB";
		                    //alert('File size must not be more than 2 MB');
		                    $('#displaypicture').val('');
		               }
		           }
		        });
	});