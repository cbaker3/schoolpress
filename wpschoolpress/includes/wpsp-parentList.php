<?php
	  $proversion	=	wpsp_check_pro_version();
	  $proclass		=	!$proversion['status'] && isset( $proversion['class'] )? $proversion['class'] : '';
	  $protitle		=	!$proversion['status'] && isset( $proversion['message'] )? $proversion['message']	: '';
	  $prodisable	=	!$proversion['status'] ? 'disabled="disabled"'	: '';
	  $parentFieldList =  array(	'p_fname'		=>	__('First Name', 'WPSchoolPress'),	
									'p_mname'		=>	__('Middle Name', 'WPSchoolPress'),	
									'p_lname'		=>	__('Last Name', 'WPSchoolPress'),
									's_fname'		=>	__('Student Name', 'WPSchoolPress'),
									'user_email'	=>	__('Parent Email ID', 'WPSchoolPress'),
									'p_edu'			=>	__('Education', 'WPSchoolPress'),									
									'p_gender'		=>	__('Gender', 'WPSchoolPress'),
									'p_profession'	=>	__('Profession', 'WPSchoolPress'),
									'p_bloodgrp'	=>	__('Blood Group', 'WPSchoolPress'),
							);
$sel_classid	=	isset( $_POST['ClassID'] ) ? $_POST['ClassID'] : '';										
$class_table	=	$wpdb->prefix."wpsp_class";
$classQuery		=	"select cid,c_name from $class_table Order By cid ASC";
if( $current_user_role=='teacher' ) {
	$cuserId	=	$current_user->ID;
	$classQuery	=	"select cid,c_name from $class_table where teacher_id=$cuserId";
	$msg		=	'Please Ask Principal To Assign Class';
}
$sel_class		=	$wpdb->get_results( $classQuery );

?>
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-solid bg-blue-gradient">
				<div class="box-header ui-sortable-handle" style="cursor: move;">
                    <i class="fa fa-graph"></i>
                    <h3 class="box-title"><i class="fa fa-users" aria-hidden="true"></i>&nbsp; Parent List</h3>
                    <!-- tools box -->

                    <!-- /. tools -->
                </div>
                 <div class="box-footer text-black">				
						<?php if( empty( $sel_class ) && $current_user_role=='teacher' ) {
							echo '<div class="alert alert-danger col-lg-2">'.$msg.'</div>';
						} else { ?>
						<div class="col-md-12 col-lg-12 col-sm-12" style="padding:0;margin-bottom:10px">
							<div class="col-md-4 col-sm-12 col-lg-4 float-left" style="padding:0;">
								<form name="ClassForm" id="ClassForm" method="post" action="" class="class-filter">
									<label><?php _e( 'Select Class Name', 'WPSchoolPress' );?></label>
									<select name="ClassID" id="ClassID" class="form-control">
										<?php										
										foreach( $sel_class as $classes ) { ?>
											<option value="<?php echo $classes->cid;?>" <?php if($sel_classid==$classes->cid) echo "selected"; ?>><?php echo $classes->c_name;?></option>
										<?php } if($current_user_role=='administrator' ) { ?>											
											<option value="all" <?php if($sel_classid=='all') echo "selected"; ?>>All</option>
										<?php } ?>	
									</select>
								</form>
							</div>
							<div class="col-md-8 col-sm-12 col-lg-8 float-right">							
								<div class="button-group btn-pro"  title="<?php echo $protitle;?>" <?php echo $prodisable;?>>
									<div class="dropdown"> 
										<button type="button" class="btn btn-primary dropdown-toggle print" id="PrintParent" <?php echo $prodisable;?> title="<?php echo $protitle;?>"><i class="fa fa-print"></i> Print </button>
										<button type="button" class="btn btn-primary dropdown-toggle toggle-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" <?php echo $prodisable;?> title="<?php echo $protitle;?>">
											<span class="caret"></span>
											<span class="sr-only">Toggle Dropdown</span>
										</button>
										<ul class="dropdown-menu">
										<form id="ParentColumnForm" name="ParentColumnForm">
											<li class="dropdown-header"> Select Columns to Print </li>
												<?php foreach( $parentFieldList as $key=>$value ) { ?>
												<li class="checkbox checkbox-info" >
													<input type="checkbox" name="ParentColumn[]" value="<?php echo $key; ?>" id="<?php echo $key; ?>" checked="checked">
													<label for="<?php echo $key; ?>"><?php echo $value; ?></label>
												</li>
												<?php } ?>												
												<input type="hidden" name="ClassID" value="<?php if(isset($_POST['ClassID'])) echo $_POST['ClassID']; else echo $sel_class[0]->cid; ?>">
										</form>
										</ul>
									</div>
									
									<div class="btn-group dropdown">																			
										<button type="button" class="btn btn-primary print" id="ExportParents" <?php echo $prodisable;?> title="<?php echo $protitle;?>"><i class="fa fa-download"></i> Export </button>
										<button type="button" class="btn btn-primary dropdown-toggle export-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" <?php echo $prodisable;?> title="<?php echo $protitle;?>">
											<span class="caret"></span>
											<span class="sr-only">Toggle Dropdown</span>
										</button>
										<ul class="dropdown-menu">
											<form id="ExportColumnForm" name="ExportParentColumn" method="POST">
											<li class="dropdown-header"> Select Columns to Export </li>
												<?php foreach( $parentFieldList as $key=>$value ) { ?>
												<li class="checkbox checkbox-info" >
													<input type="checkbox" name="ParentColumn[]" value="<?php echo $key; ?>" id="<?php echo $key; ?>" checked="checked">
													<label for="<?php echo $key; ?>"><?php echo $value; ?></label>
												</li>
												<?php } ?>																						
												<input type="hidden" name="ClassID" value="<?php if(isset($_POST['ClassID'])) echo $_POST['ClassID']; else echo $sel_class[0]->cid; ?>">
												<input type="hidden" name="exportparent" value="exportparent">
											</form>
										</ul>
									</div>
								</div>

								<div class="add-parent-btn"><a class="btn btn-primary pull-right add-parent" href="sch-student/?tab=addstudent"><i class="fa fa-plus"></i> Add Parent</a></div>

							</div>
						</div>
						<div class="col-md-12 table-responsive">
						<table id="parent_table" class="table table-bordered table-striped table-responsive" style="margin-top:10px">
						<thead>
							<tr>								
								<th>Parent Name</th>
								<th>Student Name</th>							
								<th>Parent's Email ID</th>
								<th class="nosort">Select Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$student_table	=	$wpdb->prefix."wpsp_student";							
							$users_table	=	$wpdb->prefix."users";
                            if(isset($_POST['ClassID'])){
                                $class_id=$_POST['ClassID'];
                            } else if(!empty($sel_class)) {
                                $class_id = $sel_class[0]->cid;
                            } else {
                                $class_id='';
                            }
							$classquery	=	" AND class_id='$class_id' ";
							if($class_id=='all'){
								$classquery	=	"";
							}

							$parent_ids=$wpdb->get_results("select DISTINCT u.user_email, CONCAT_WS(' ', p_fname, p_mname, p_lname ) AS full_name, p.s_fname, p.wp_usr_id, p.parent_wp_usr_id from $student_table p, $users_table u where u.ID=p.parent_wp_usr_id $classquery");
													
							foreach($parent_ids as $pinfo)
							{								
							?>
								<tr>									
									<td><?php echo $pinfo->full_name;?></td>
									<td><?php echo $pinfo->s_fname; ?> </td>
									<td><?php echo $pinfo->user_email;?></td>									
									<td>
										<a href="?id=<?php echo $pinfo->parent_wp_usr_id;?>" title="View" data-id="<?php echo $pinfo->parent_wp_usr_id;?>" class="ViewParent"><i class="fa fa-eye btn btn-success"></i></a>
										<a href="sch-student?id=<?php echo $pinfo->wp_usr_id.'&edit=true';?>" title="Edit"><i class="fa fa-pencil btn btn-warning"></i></a>
									</td>
								</tr>
							<?php
							}
							?>
						</tbody>
						<tfoot>
						  <tr>														
							<th>Parent Name</th>
							<th>Student Name</th>							
							<th>Parent's Email ID </th>
							<th>Action</th>
						  </tr>
						</tfoot>
					  </table>
					</div>
					 <?php } ?> 
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>
		</div>
	</section>
</div>