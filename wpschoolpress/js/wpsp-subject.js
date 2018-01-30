	$(document).ready(function(){
		
		$('#subject_table').dataTable({
			"order": [],
			"columnDefs": [ {
			  "targets"  : 'nosort',
			  "orderable": false,
			}],
			responsive: true,
		});
		
		$("#AddSubjectButton").click(function(){
			$("#SClassName").text($("#ClassID option:selected").text());
			$("#SCID").val($("#ClassID").val());
			$("#AddSubjectModal").modal('show');
		});
		$("#ClassID").change(function(){
			$("#SubjectList-Form").submit();
		});
		$("#ShowExtraFields").click(function(){
			$(".SubjectExtraDetails").toggle();
		});
		
		$("#SubjectEntryForm").validate({
			onkeyup:false,
            ignore:[],
			rules: {
				'SNames[]': {
					required: true,
					minlength: 2
				},
				'SCID':{
                    required: true,
                    number:true
                },
				STeacherID: {
					number:true
				}
			},
			messages: {
				SName: {
					required: "Please enter Subject Name",
					minlength: "Subject must consist of at least 2 characters"
				},
                SCID:{
				    required:"Class ID missing please refresh"
                }
			},
			submitHandler: function(form){
				var data=$('#SubjectEntryForm').serializeArray();
				data.push({name: 'action', value: 'AddSubject'});
				$.ajax({
						type: "POST",
						url: ajax_url,
						data: data,
						
						success: function(rdata) {
							if(rdata=='success')
							{
								$('.formresponse').html("<div class='alert alert-success'>Subject Created Successfully!</div>");
								$('#SubjectEntryForm').trigger("reset");
							}
							else
							{
								$('.formresponse').html("<div class='alert alert-danger'>"+rdata+"</div>");
							}
						}
				});

			}
		});
		
		/*Edit Subject Modal */
		$(".EditSubjectLink").click(function(){
			var sid=$(this).attr('sid');
			var SDetails= [];
			SDetails.push({name: 'action', value: 'SubjectInfo'});
			SDetails.push({name: 'sid', value:sid});
			$.ajax({
					type: "POST",
					url: ajax_url,
					data: SDetails,
					beforeSend:function(){
						$('#formresponse').html("Saving..");
					},
					success: function(subject_details) {
						var sdatapar = $.parseJSON(subject_details);
						if(typeof sdatapar =='object'){
							$('#SRowID').val(sdatapar.id);
							$('#ESClassID').val(sdatapar.class_id);
							$('#EditSCode').val(sdatapar.sub_code);
							$('#EditSName').val(sdatapar.sub_name);
							$('#EditBName').val(sdatapar.book_name);
							try{
								$("#EditSTeacherID option[value="+sdatapar.sub_teach_id+"]").attr('selected','selected');
							}
							catch(e){
								//
							}
							$('#EditSubjectModal').modal('show');
						}else{
							$('#InfoModalTitle').text("Error Information!");
							$('#InfoModalBody').html("<h3>Sorry! No data retrived!</h3><span class='text-muted'>You can refresh page and try again</span>");
							$('#InfoModal').modal('show');
						}
					},
					error:function(){
						$('#InfoModalTitle').text("Error Information!");
						$('#InfoModalBody').html("<h3>Sorry! File not reachable!</h3><span class='text-muted'>Check your internet connection!</span>");
						$('#InfoModal').modal('show');
					}
			});

		});
		/*Edit Save */
		$("#SEditForm").validate({
			onkeyup:false,
			rules: {
				EditSName: {
					required: true,
					minlength: 2
				},
				
				EditSTeacherID: {
					number:true
				}
			},
			messages: {
				SName: {
					required: "Please enter Subject Name",
					minlength: "Subject must consist of at least 2 characters"
				}
			},
			submitHandler: function(form){
				var data=$('#SEditForm').serializeArray();
				data.push({name: 'action', value: 'UpdateSubject'});
				$.ajax({
						type: "POST",
						url: ajax_url,
						data: data,
						success: function(rdata) {
							if(rdata=='updated')
							{
								$('#editformresponse').html("<div class='alert alert-success'>Subject information updated Successfully!</div>");
								window.location.reload();
							}
							else
							{
								$('#editformresponse').html("<div class='alert alert-danger'>"+rdata+"</div>");
							}
						}
				});

			}
		
		});
		/* Subject Delete */		$(document).on('click','.SubjectDeleteBt',function(e) {
			var sid=$(this).attr('sid');						new PNotify({				title: 'Confirmation Needed',				text: 'Are you sure want to delete?',				icon: 'glyphicon glyphicon-question-sign',				hide: false,				confirm: { confirm: true },				buttons: {					closer: false,					sticker: false				},				history: {					history: false				},			}).get().on('pnotify.confirm', function(){				var data=[];				data.push({name: 'action', value: 'DeleteSubject'},{name: 'sid', value: sid});				jQuery.post(ajax_url, data, function(cddata) {					if(cddata=='deleted'){						$('#InfoModalBody').html("<div class='col-md-8 alert alert-success'>Subject deleted successfully!</div>");						location.reload();					}					else{						$('#InfoModalBody').html("<div class='col-md-8 alert alert-danger'>"+cddata+"</div>");					}				});			});
		});		/*
		$(document).on('click','#SubjectDeleteConfirm',function(e){
			var data=[];
			data.push({name: 'action', value: 'DeleteSubject'},{name: 'sid', value: sid});
			sid='0';
			jQuery.post(ajax_url, data, function(cddata) {
				if(cddata=='deleted'){
					$('#InfoModalBody').html("<div class='col-md-8 alert alert-success'>Subject deleted successfully!</div>");
					location.reload();
				}
				else{
					$('#InfoModalBody').html("<div class='col-md-8 alert alert-danger'>"+cddata+"</div>");
				}
			});
		});
		$('.modal').on('hidden.bs.modal', function (e) {
			//location.reload();
		}); */
	});