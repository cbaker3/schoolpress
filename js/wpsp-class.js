$(document).ready(function() {
    updateDate();
    function updateDate() {
        $('.wpsp-start-date').datepicker({
            autoclose: true,
            dateFormat: date_format,
            todayHighlight: true,
            changeMonth: true,
            changeYear: true,
            beforeShow: function(input, inst) {
                $(document).off('focusin.bs.modal');
            },
            onClose: function() {
                $(document).on('focusin.bs.modal');
            },
            onSelect: function(selectedDate) {
                $(".wpsp-end-date").datepicker("option", "minDate", selectedDate);
            }
        });

        $('.wpsp-end-date').datepicker({
            autoclose: true,
            dateFormat: date_format,
            todayHighlight: true,
            changeMonth: true,
            changeYear: true,
            beforeShow: function(input, inst) {
                $(document).off('focusin.bs.modal');
            },
            onClose: function() {
                $(document).on('focusin.bs.modal');
            },
            onSelect: function(selectedDate) {
                $(".wpsp-start-date").datepicker("option", "maxDate", selectedDate);
            }
        });
    }

    $('#class_table').dataTable({
        "order": [],
        "columnDefs": [{
            "targets": 'nosort',
            "orderable": false,
        }],
        responsive: true,
    });

    $("#ClassAddForm").validate({
        rules: {
            Name: {
                required: true,
                minlength: 2,
            },
            capacity: {
                required: true,
                max: 100
            },
            ClassTeacherID: {
                number: true
            },
            Sdate: {
                required: true,
            },
            Edate: {

                required: true,

            }

        },

        messages: {

            Name: {

                required: "Please enter classname",

                minlength: "Class must consist of at least 2 characters"

            },
            capacity: {
                max: "Class Out of capacity"
            }

        },

        submitHandler: function(form) {

            var data = $('#ClassAddForm').serializeArray();

            data.push({
                name: 'action',
                value: 'AddClass'
            });

            $.ajax({

                type: "POST",

                url: ajax_url,

                data: data,

                beforeSend: function() {
                    //$('#ClassAddForm .formresponse').html("Saving..");
					$.fn.notify('loader', {'desc': 'Saving data..'});
                },
                success: function(rdata) {
                    if (rdata == 'inserted') {
						$.fn.notify('success', {'desc': 'Class created successfully!', autoHide: true, clickToHide: true});
                      //  $('#ClassAddForm .formresponse').html("<div class='alert alert-success'>Class created successfully!</div>");
                        $('#ClassAddForm').trigger("reset");
                    } else {						
						$.fn.notify('error', {'desc': rdata, autoHide: true, clickToHide: true });
                        //$('#ClassAddForm .formresponse').html("<div class='alert alert-danger'>" + rdata + "</div>");
                    }
                },
				complete: function () {
                    $('.pnloader').remove();
                }
            });
        }
    });




    $("#ClassEditForm").validate({
        rules: {
            Name: {
                required: true,
                minlength: 2,
            },
            capacity: {
                required: true,
                max: 100
            },
            ClassTeacherID: {
                number: true
            },
            Sdate:{
                required: true,
            },
            Edate:{
                required: true,
            }
        },
        messages: {
            Name: {
                required: "Please enter classname",
                minlength: "Class must consist of at least 2 characters"
            },
            capacity: {
                max: "Class Out of capacity"
            }
        },

        submitHandler: function(form) {
            var data = $('#ClassEditForm').serializeArray();
            data.push({
                name: 'action',
                value: 'UpdateClass'
            });
            $.ajax({
                type: "POST",
                url: ajax_url,
                data: data,
                beforeSend: function() {
                    //$('#ClassAddForm .formresponse').html("Saving..");
					$.fn.notify('loader', {'desc': 'Saving data..'});
                },
                success: function(rdata) {
                    if (rdata == 'updated') {
						$.fn.notify('success', {'desc': 'Class updated successfully!', autoHide: true, clickToHide: true});                        
                    } else {
                        $.fn.notify('error', {'desc': rdata, autoHide: true, clickToHide: true  });
                    }
                },
				complete: function () {
                    $('.pnloader').remove();
                }
            });
        }
    });



   /* $('.ClassEditBt').on('click', function(e) {

        e.preventDefault();

        var data = [];

        cid = $(this).attr('cid');

        data.push({
            name: 'action',
            value: 'GetClass'
        }, {
            name: 'cid',
            value: cid
        });

        jQuery.post(ajax_url, data, function(pdata) {

            var pardata = JSON.parse(pdata);

            if (typeof pardata == 'object') {

                $('#ClassEditForm input[name=cid]').val(cid);

                $('#ClassEditForm input[name=Name]').val(pardata.c_name);

                $('#ClassEditForm input[name=Number]').val(pardata.c_numb);

                $('#ClassEditForm input[name=Location]').val(pardata.c_loc);

                $('#ClassEditForm input[name=Sdate]').val(pardata.c_sdate);

                $('#ClassEditForm input[name=Edate]').val(pardata.c_edate);

                $('#ClassEditForm input[name=capacity]').val('');

                if (pardata.c_capacity != null)

                    $('#ClassEditForm input[name=capacity]').val(pardata.c_capacity);

                $('#ClassEditForm select[name=ClassTeacherID]').val(pardata.teacher_id);

                $('#EditModal').modal('show');

                updateDate();

            } else {



                $('#InfoModalTitle').text("Error Information!");

                $('#InfoModalBody').html("<h3>Sorry! No data retrived!</h3><span class='text-muted'>You can refresh page and try again</span>");

                $('#InfoModal').modal('show');

            }



        });



    }); */

	$(document).on('click','.ClassDeleteBt',function(e) {
        var cid = $(this).data('id');
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
						 var data = [];

						data.push({
							name: 'action',
							value: 'DeleteClass'
						}, {
							name: 'cid',
							value: cid
						});

						cid = '0';

						jQuery.post(ajax_url, data, function(cddata) {
							
							 if (cddata == 'success') {
							  $.fn.notify('success', {'desc': 'Deleted successfully!'});
							  $(location).attr('href', 'sch--class'); 
							} else {
								$.fn.notify('error', {'desc': 'Operation failed.Something went wrong!'});
							}
					});	
					});	


    });



});