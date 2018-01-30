	$(document).ready(function(){
		$('#wp-end-time').timepicker({
			 showInputs: false,
			 showMeridian:false
		});
		$('#timepicker1').timepicker({
			showInputs: false,
			showMeridian:false
		});
		 
			
		$("#SettingsInfoForm,#SettingsSocialForm,#SettingsMgmtForm,#SettingsGradeForm,#paytm_setting_form,#sms_settings_form, #payment_settings_form").submit(function(e) {			
			e.preventDefault();
			$('.pnloader').remove();
			/*var fdata=$(this).serializeArray();			
			var file=$('#displaypicture')[0].files[0];
			fdata.push( {name: 'displaypicture', value: file});
			fdata.push({name: 'action', value: 'genSetting'});
			*/
			var data 	=	new FormData();
			var fdata	=	$(this).serializeArray();
			var file	=	$('#displaypicture')[0].files[0];
			data.append('action', 'genSetting');
			data.append('displaypicture',file);
			$.each(fdata,function(key,input){
				data.append(input.name,input.value);
			});
			data.append('data',fdata);
			
			jQuery.ajax({
				type:"POST",
				url:ajax_url, 
				//data:fdata,
				data:data,
				cache: false,
				processData: false,
				contentType: false,	
				beforeSend:function() {
					$.fn.notify('loader',{'desc':'Saving settings..'});					
				},
				success:function(ires) {
					if(ires=='success'){
						var pntype='success';
						var pntext="Information Saved Successfully";
					} else {
						var pntype='error';
						var pntext= ires=='' ? "Something went wrong" : ires;
					}
					$.fn.notify(pntype,{'desc':pntext});
				},
				complete:function(){
					$('.pnloader').remove();
				}
			});
		});
		
		$('#AddGradeForm').validate({
			//e.preventDefault();
			rules: {
				grade_name: { required: true },
				grade_point:{ required: true },
				mark_from: { required: true },
				mark_upto: { required: true },
			},
			messages: {
				grade_name: "Please Enter Grade Name",
				grade_point: "Please Enter Grade Point",
				mark_from : "Please Enter Mark From",
				mark_upto: "Please Enter Mark Upto",
			},
			submitHandler: function(form) {				
				var fdata	=	$('#AddGradeForm').serializeArray();				
				fdata.push({name: 'action', value: 'manageGrade'});				
				jQuery.ajax({
					method:"POST",
					url:ajax_url, 
					data:fdata,
					beforeSend:function(){
						$.fn.notify('loader',{'desc':'Saving grade..'});
						$('#grade_save').attr("disabled", 'disabled');	
					},
					success:function(ires) {
						$('#grade_save').removeAttr('disabled');
						$('#AddGradeForm').trigger("reset");
						if(ires=='success'){
							var pntype='success';
							var pntext="Grade Saved Successfully";
						}
						else{
							var pntype='error';
							var pntext="Something went wrong";
						}
						$.fn.notify(pntype,{'desc':pntext});
					},
					complete:function(){
						$('#grade_save').removeAttr('disabled');
						$('.pnloader').remove();
						$('#AddGradeForm').trigger("reset");
					}
				});
			}
		});
		
		$('.sch-remove-logo').click( function(e){
			$('#sch_logo_control').val('');
			$('.sch-logo-container').remove();
			$('.logo-label').html( 'Upload Logo');
		});
		$('#wpsp_grade_list, #wpsp_sub_division_table,#wpsp_class_hours').dataTable({
			"order": [],
			"columnDefs": [ {
				  "targets"  : 'nosort',
				  "orderable": false,
			}],
			responsive: true,
		});
	
		$('.DeleteGrade').click(function(){
			var gid=$(this).attr('data-id');
			var gdata=new Array();
			gdata.push({name: 'action', value: 'manageGrade'},{name: 'grade_id', value: gid},{name: 'actype', value: 'delete'});
			jQuery.ajax({
				method:"POST",
				url:ajax_url, 
				data:gdata, 
				success:function(gres) {
					if(gres=='success'){
						$.fn.notify('success',{'desc':'Grade deleted succesfully!'});
						window.location.reload();
					}else
						$.fn.notify('error',{'desc':gres});				
				},
				error:function(){
					$.fn.notify('error',{'desc':'Something went wrong'});
				},
				beforeSend:function(){
					$.fn.notify('loader',{'desc':'Deleting grade..'});
				},
				
				complete:function(){
					$('.pnloader').remove();
				}
			});			
		});
		
		$('#SubFieldsClass').change(function(){
			$('#SubFieldSubject option:gt(0)').remove();
			var sfdata=new Array();
			var cid=$(this).val();
			sfdata.push({name: 'action', value: 'subjectList'},{name: 'ClassID', value: cid});
			jQuery.ajax({
				method:"POST",
				url:ajax_url, 
				data:sfdata, 
				success:function(sfres) {
					var newOptions=$.parseJSON(sfres);
					var $el = $("#SubFieldSubject");					$.each( newOptions.subject,function(field,value) {	
						$el.append($("<option></option>").attr("value", value.id).text(value.sub_name)); 
					});
				},
				error:function(){
					$.fn.notify('error',{'desc':'Something went wrong'});
				},
				beforeSend:function(){
					$.fn.notify('loader',{'desc':'Loading Subjects'});
				},
				complete:function(){
					PNotify.removeAll();
				}
			});
		});
		$('input[type=radio][name=sch_sms_provider]').change(function() {
			var value = this.value;
			$( '.sms_setting_div' ).hide();
			$( '#sms_main_'+value ).show();
		});
		$('#SubFieldsForm').submit(function(e){
			e.preventDefault();
			var sfdata=$(this).serializeArray();
			sfdata.push({name: 'action', value: 'addSubField'});
			jQuery.ajax({
				method:"POST",
				url:ajax_url, 
				data:sfdata, 
				success:function(sfres) {
					if(sfres=='success'){
						$('#SubFieldsForm')[0].reset();
						$.fn.notify('success',{'desc':'Fields added succesfully!'});
					}else
						$.fn.notify('error',{'desc':sfres});				
						
				},
				error:function(){
					$.fn.notify('error',{'desc':'Something went wrong'});
				},
				beforeSend:function(){
					$.fn.notify('loader',{'desc':'Saving Fields'});
				},
				complete:function(){
					$('.pnloader').remove();
				}
			});
		});
		
		//Subject Fields Update Function
		
		$('.SFUpdate').click(function(){
			var sfid=$(this).attr('data-id');
			var field=$("#"+sfid+"SF").val();
			var sfdata=new Array();
			sfdata.push({name: 'action', value: 'updateSubField'},{name: 'sfid', value: sfid},{name: 'field', value: field});
			jQuery.ajax({
				method:"POST",
				url:ajax_url, 
				data:sfdata, 
				success:function(sfres) {
					if(sfres=='success'){
						$.fn.notify('success',{'desc':'Field updated succesfully!'});
					}else
						$.fn.notify('error',{'desc':sfres});				
				},
				error:function(){
					$.fn.notify('error',{'desc':'Something went wrong'});
				},
				beforeSend:function(){
					$.fn.notify('loader',{'desc':'Saving Fields'});
				},
				complete:function(){
					$('.pnloader').remove();
				}
			});
		});
		//Sub Field delete function
		$('.SFDelete').click(function(){
			var sfid=$(this).attr('data-id');
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
					var sfdata=new Array();
					sfdata.push({name: 'action', value: 'deleteSubField'},{name: 'sfid', value: sfid});
					jQuery.ajax({
						method:"POST",
						url:ajax_url, 
						data:sfdata, 
						success:function(sfres) {
							if(sfres=='success'){
								$.fn.notify('success',{'desc':'Field deleted succesfully!'});
								window.location.reload();
							}else
								$.fn.notify('error',{'desc':sfres});				
						},
						error:function(){
							$.fn.notify('error',{'desc':'Something went wrong'});
						},
						beforeSend:function(){
							$.fn.notify('loader',{'desc':'Deleting Fields'});
						},
						complete:function(){
							$('.pnloader').remove();
						}
					});
                });
		});
	});
