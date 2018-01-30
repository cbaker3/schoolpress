<?php
// Add Student login with admin/teacher
function wpsp_AddStudent() {
	
    wpsp_Authenticate();	
	if (! isset( $_POST['sregister_nonce'] ) || ! wp_verify_nonce( $_POST['sregister_nonce'], 'StudentRegister' )) {
			echo "Unauthorized Submission";
			exit;
	}
	
	$username	=	esc_attr($_POST['Username']);
	
	if( wpsp_CheckUsername($username,true)===true ) {
		echo "Given Student User Name Already Exists!";
		exit;
	}
	
	if( email_exists( $_POST['email'] ) ) {
		echo "Student Email ID Already Exists!";
		exit;
	}
	
	if( strtolower( $_POST['Username'] ) ==  strtolower( $_POST['pUsername'] ) ) {
		echo "Both USer Name Should Not Be same";
		exit;
	}
	
	if( strtolower( $_POST['pEmail'] ) ==  strtolower( $_POST['Email'] ) ) {
		echo "Both Email Address Should Not Be same";
		exit;
	}
	
	global $wpdb;
	$wpsp_student_table	=	$wpdb->prefix."wpsp_student";
	$wpsp_class_table	=	$wpdb->prefix."wpsp_class";
	
	if( isset( $_POST['Class'] ) && !empty( $_POST['Class'] ) ) {
		$classID	=	$_POST['Class'];
		$capacity	=	$wpdb->get_var("SELECT c_capacity FROM $wpsp_class_table where cid=$classID"); 		
		if( !empty( $capacity ) ) {
			$totalstudent	=	$wpdb->get_var("SELECT count(*) FROM $wpsp_student_table where class_id=$classID");
			if( $totalstudent > $capacity  ) {
				echo 'This Class reached to it\'s capacity, Please select another class';
				exit;
			}
		}
	}
	global $wpdb;
	$parentMsg	=	'';
	$parentSendmail	=	false;
	$wpsp_student_table	=	$wpdb->prefix."wpsp_student";	
	$firstname			=	esc_attr($_POST['s_fname']);					
	$parent_id			=	isset( $_POST['Parent'] ) ? esc_attr($_POST['Parent']) : '0';
	$email				=	esc_attr($_POST['Email']);	
    $pfirstname			=	esc_attr($_POST['p_fname']);
	$pmiddlename		=	esc_attr($_POST['p_mname']);
	$plastname			=	esc_attr($_POST['p_lname']);
	$pgender			=	esc_attr($_POST['p_gender']);
	$pedu 				=	esc_attr($_POST['p_edu']);
	$pprofession		=	esc_attr($_POST['p_profession']);      
	$pbloodgroup	      =  esc_attr($_POST['p_bloodgrp']);  
	
	$email	=	empty( $email ) ? wpsp_EmailGen($username) : $email;
		
	$userInfo = array(	'user_login'	=>	$username,
						'user_pass'		=>	esc_attr($_POST['Password']),
						'user_nicename'	=>	esc_attr($_POST['Name']),
						'first_name'	=>	$firstname,
						'user_email'	=>	$email,
						'role'			=>	'student' );
	$user_id = wp_insert_user( $userInfo );
	
	if( !empty( $_POST['pEmail'] ) ) {
		$response		=	getparentInfo( $_POST['pEmail'] ); //check for parent email id	
		
		if( isset( $response['parentID'] ) && !empty( $response['parentID'] ) ) { //Use data of existing user
			$parent_id 		= 	$response['parentID'];
			$pfirstname		=	$response['data']->p_fname;
			$pmiddlename	=	$response['data']->p_mname;
			$plastname		=	$response['data']->p_lname;
			$pgender		=	$response['data']->p_gender;
			$pedu			=	$response['data']->p_edu;
			$pprofession	=	$response['data']->p_profession;
			$pbloodgroup	=	$response['data']->p_bloodgrp;		
		} else {		
			if( wpsp_CheckUsername( $_POST['pUsername'] ,true)===true ){
				$parentMsg	=	'Parent UserName Already Exists';
			} else {
				$parentInfo = array( 'user_login'	=>	$_POST['pUsername'],
								'user_pass'		=>	esc_attr($_POST['pPassword']),
								'user_nicename'	=>	esc_attr($_POST['pUsername']),
								'first_name'	=>	esc_attr($_POST['pfirstname']),
								'user_email'	=>	esc_attr($_POST['pEmail']),
								'role'			=>	'parent' );
				$parent_id = wp_insert_user( $parentInfo );	//Creating parent

				
				$msg = 'Hello '.$_POST['pfirstname'];
				$msg .= '<br>Your are registered as parent at <a href="'.site_url().'">School</a><br><br>';
				$msg .= 'Your Login details are below.<br>';
				$msg .= 'Your User Name is : ' .$_POST['pUsername'].'<br>';
				$msg .= 'Your Password is : '.$_POST['pPassword'].'<br><br>'; 
				$msg .= 'Please Login by clicking <a href="'.site_url().'/sch-dashboard">Here </a><br><br>';
				$msg .= 'Thanks,<br>'.get_bloginfo('name');
				wpsp_send_mail( $_POST['pEmail'], 'User Registered',$msg) ;
				
				if( !is_wp_error($parent_id) && !empty( $_FILES['pdisplaypicture']['name'] ) ) {
					$parentSendmail	=	true;
					$avatar	=	uploadImage('pdisplaypicture');					
					if( isset( $avatar[ 'url' ] ) ) { //Update parent's profile image
						update_user_meta( $parent_id, 'displaypicture', array ( 'full'=>$avatar[ 'url' ] ) ); 
						update_user_meta( $parent_id, 'simple_local_avatar', array ( 'full'=>$avatar[ 'url' ] ) ); 
					}				
				} else if( is_wp_error($parent_id) ) {
					$parentMsg		=	$parent_id->get_error_message();
					$parent_id 		= 	'';
					$pfirstname		=	$pmiddlename	= $plastname	= $pgender = $pedu = $pprofession =	$pbloodgroup = '';
				}
			}
		}	
	}	
	
	if(!is_wp_error($user_id)) {
		$studenttable	=	array(
						'wp_usr_id' 		=>	$user_id,
						'parent_wp_usr_id'	=>	$parent_id,						
						'class_id'			=>	isset( $_POST['Class'] ) ? esc_attr( $_POST['Class'] ) : '',						
						's_rollno' 			=>	isset( $_POST['s_rollno'] ) ? esc_attr($_POST['s_rollno']):'',
						's_fname' 			=>  $firstname,
						's_mname' 			=>  isset( $_POST['s_mname'] ) ? esc_attr( $_POST['s_mname'] ) : '',
						's_lname' 			=>  isset( $_POST['s_lname'] ) ? esc_attr( $_POST['s_lname'] ) : '',
						's_zipcode'			=> 	isset( $_POST['s_zipcode'] ) ?	esc_attr( $_POST['s_zipcode'] ) : '',
						's_country'			=> 	isset( $_POST['s_country'] ) ? esc_attr( $_POST['s_country'] ) : '',
						's_gender'			=> 	isset( $_POST['s_gender'] ) ? esc_attr($_POST['s_gender']) : '',
						's_address'			=>	isset( $_POST['s_address'] ) ? esc_attr( $_POST['s_address'] ) : '',						
						's_bloodgrp' 		=> 	isset( $_POST['s_bloodgrp'] ) ? esc_attr($_POST['s_bloodgrp']) : '',						
						's_dob'				=>	isset( $_POST['s_dob'] ) && !empty( $_POST['s_dob'] ) ? wpsp_StoreDate( $_POST['s_dob'] ) :'',
						's_doj'				=>	isset( $_POST['s_doj'] ) && !empty( $_POST['s_doj'] ) ? wpsp_StoreDate( $_POST['s_doj'] ) :'',
						's_phone'			=> 	isset( $_POST['s_phone'] ) ? esc_attr( $_POST['s_phone'] ) : '',
						'p_fname' 			=>  $pfirstname,
						'p_mname'			=>  $pmiddlename,
						'p_lname' 			=>  $plastname,
						'p_gender' 			=> 	$pgender,
						'p_edu' 			=>	$pedu,
						'p_profession' 		=>  $pprofession,
						's_paddress'		=>	isset( $_POST['s_paddress'] ) ? esc_attr($_POST['s_paddress']) : '',
						'p_bloodgrp' 		=> $pbloodgroup,
						's_city' 			=> isset( $_POST['s_city'] ) ? esc_attr( $_POST['s_city'] ) :'',
						's_pcountry'		=> isset( $_POST['s_pcountry'] ) ? esc_attr( $_POST['s_pcountry'] ) : '',
						's_pcity' 			=> isset( $_POST['s_pcity'] ) ? esc_attr( $_POST['s_pcity'] ) :'',						
						's_pzipcode'		=> isset( $_POST['s_pzipcode'] ) ? $_POST['s_pzipcode'] :''
						 );
		
		
		$msg = 'Hello '.$first_name;
		$msg .= '<br>Your are registered as student at <a href="'.site_url().'">School</a><br><br>';
		$msg .= 'Your Login details are below.<br>';
		$msg .= 'Your User Name is : ' .$username.'<br>';
		$msg .= 'Your Password is : '.$_POST['Password'].'<br><br>'; 
		$msg .= 'Please Login by clicking <a href="'.site_url().'/sch-dashboard">Here </a><br><br>';
		$msg .= 'Thanks,<br>'.get_bloginfo('name');
		
		wpsp_send_mail( $email, 'User Registered',$msg) ;

		$sp_stu_ins = $wpdb->insert( $wpsp_student_table , $studenttable );
				//send registration mail
			wpsp_send_user_register_mail( $userInfo, $user_id );
			if (!empty( $_FILES['displaypicture']['name'])) {
				$avatar	=	uploadImage('displaypicture');				
				if( isset( $avatar[ 'url' ] ) ) {
					update_user_meta( $user_id, 'displaypicture', array ( 'full'=>$avatar[ 'url' ] ) ); 
					update_user_meta( $user_id, 'simple_local_avatar', array ( 'full'=>$avatar[ 'url' ] ) ); 
				}	
			}
			$msg	=	$sp_stu_ins ? "success" : "Oops! Something went wrong try again.";
	} else if(is_wp_error($user_id)) {
        $msg	=	$user_id->get_error_message();
	}
	echo $msg;
	wp_die();
}

add_action( 'wp_ajax_check_parent_info', 'wpsp_check_parent_info' );
function wpsp_check_parent_info(){
	$response = array();	
	$response['status']	=	0; //Fail status
	if( isset( $_POST['parentEmail'] ) && !empty( $_POST['parentEmail'] ) ) {
		$parentEmail	=	$_POST['parentEmail'];
		$response		=	getparentInfo( $parentEmail );		
	}
	echo json_encode( $response);
	exit();
}

function getparentInfo( $parentEmail ) {
	$parentInfo		=	get_user_by( 'email', $parentEmail);
	$response['status']	=	0;
	if( !empty( $parentInfo ) ) {
		global $wpdb;
		$student_table 	=	$wpdb->prefix . "wpsp_student";
		$roles			=	$parentInfo->roles;
		$parentID		=	$parentInfo->ID;
		$chck_parent	=	$wpdb->get_row("SELECT p_fname,p_mname,p_lname, p_gender, p_edu,  p_profession, p_bloodgrp from $student_table where parent_wp_usr_id=$parentID");
		$response['parentID']	=	$parentID;
		if( !empty( $chck_parent ) ) {
			$response['data']		=	$chck_parent;
			$response['status']		=	1;
			$response['username']	=	$parentInfo->data->user_login;
		}
	}
	return $response;
}

/*Upload Image*/
function uploadImage( $file ){
	
	if (!empty( $_FILES[$file]['name'])) {
		$mimes=array (
			'jpg|jpeg|jpe'=>'image/jpeg',
			'gif'=>'image/gif',
			'png'=>'image/png',
			'bmp'=>'image/bmp',
			'tif|tiff'=>'image/tiff'
		);
		
		if (!function_exists('wp_handle_upload'))
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		
		$avatar=wp_handle_upload( $_FILES[$file], array ('mimes'=>$mimes, 'test_form'=>false, 'unique_filename_callback'=>array ( $this, 'unique_filename_callback' ) ) );
		
		if ( empty( $avatar[ 'file' ] ) ) {
			switch ( $avatar[ 'error' ] ) {
				case 'File type does not meet security guidelines. Try another.' :
					add_action( 'user_profile_update_errors', create_function( '$a', '$a->add("avatar_error",__("Please upload a valid image file for the avatar.","kv_student_photo_edit"));' ) );
					break;
				default :
				add_action( 'user_profile_update_errors', create_function( '$a', '$a->add("avatar_error","<strong>".__("There was an error uploading the avatar:","kv_student_photo_edit")."</strong> ' . esc_attr( $avatar[ 'error' ] ) . '");' ) );
			}
			return;
		}
		return $avatar;
	}
}
/*Update Student*/
function wpsp_UpdateStudent(){
    wpsp_Authenticate();	
	$user_id=esc_attr($_POST['wp_usr_id']);
	global $wpdb;
	$wpsp_student_table	=	$wpdb->prefix."wpsp_student";
	$errors				=	validation(array($_POST['s_fname']=>'required',$_POST['s_lname']=>'required') );
    if( is_array($errors) ) {
        echo "<div class='col-md-12'><div class='alert alert-danger'>";
        foreach($errors as $error){
            echo "<li>".$error."</li>";
        }
        echo "</div></div>";
        return false;
    }	
	
	$wpsp_class_table	=	$wpdb->prefix."wpsp_class";
	if( isset( $_POST['Class'] ) && !empty( $_POST['Class'] ) && $_POST['Class'] != $_POST['prev_select_class'] ) {
		$classID	=	$_POST['Class'];
		$capacity	=	$wpdb->get_var("SELECT c_capacity FROM $wpsp_class_table where cid=$classID"); 		
		if( !empty( $capacity ) ) {
			$totalstudent	=	$wpdb->get_var("SELECT count(*) FROM $wpsp_student_table where class_id=$classID");			
			if( $totalstudent > $capacity  ) {
				echo '<div class="col-md-12"><div class="alert alert-danger">This Class reached to it\'s capacity, Please select another class</div></div>';
				return false;
			}
		}
	}
	$pfirstname			=	esc_attr($_POST['p_fname']);
	$pmiddlename		=	esc_attr($_POST['p_mname']);
	$plastname			=	esc_attr($_POST['p_lname']);
	$pgender			=	esc_attr($_POST['p_gender']);
	$pedu 				=	esc_attr($_POST['p_edu']);
	$pprofession		=	esc_attr($_POST['p_profession']);      
	$pbloodgroup	      =  esc_attr($_POST['p_bloodgrp']);
	
	$studenttable	=	array(
						'class_id'			=>	isset( $_POST['Class'] ) ? esc_attr( $_POST['Class'] ) : '',						
						's_rollno' 			=>	isset( $_POST['s_rollno'] ) ? esc_attr($_POST['s_rollno']):'',
						's_fname' 			=>  isset( $_POST['s_fname'] ) ? esc_attr( $_POST['s_fname'] ) : '',
						's_mname' 			=>  isset( $_POST['s_mname'] ) ? esc_attr( $_POST['s_mname'] ) : '',
						's_lname' 			=>  isset( $_POST['s_lname'] ) ? esc_attr( $_POST['s_lname'] ) : '',
						's_zipcode'			=> 	isset( $_POST['s_zipcode'] ) ?	esc_attr( $_POST['s_zipcode'] ) : '',
						's_country'			=> 	isset( $_POST['s_country'] ) ? esc_attr( $_POST['s_country'] ) : '',
						's_gender'			=> 	isset( $_POST['s_gender'] ) ? esc_attr($_POST['s_gender']) : '',
						's_address'			=>	isset( $_POST['s_address'] ) ? esc_attr( $_POST['s_address'] ) : '',						
						's_bloodgrp' 		=> 	isset( $_POST['s_bloodgrp'] ) ? esc_attr($_POST['s_bloodgrp']) : '',						
						's_dob'				=>	isset( $_POST['s_dob'] ) && !empty( $_POST['s_dob'] ) ? wpsp_StoreDate( $_POST['s_dob'] ) :'',
						's_doj'				=>	isset( $_POST['s_doj'] ) && !empty( $_POST['s_doj'] ) ? wpsp_StoreDate( $_POST['s_doj'] ) :'',
						's_phone'			=> 	isset( $_POST['s_phone'] ) ? esc_attr( $_POST['s_phone'] ) : '',
						'p_fname' 			=>  $pfirstname,
						'p_mname'			=>  $pmiddlename,
						'p_lname' 			=>  $plastname,
						'p_gender' 			=> 	$pgender,
						'p_edu' 			=>	$pedu,
						'p_profession' 		=>  $pprofession,
						's_paddress'		=>	isset( $_POST['s_paddress'] ) ? esc_attr($_POST['s_paddress']) : '',
						'p_bloodgrp' 		=> $pbloodgroup,
						's_city' 			=> isset( $_POST['s_city'] ) ? esc_attr( $_POST['s_city'] ) :'',
						's_pcountry'		=> isset( $_POST['s_pcountry'] ) ? esc_attr( $_POST['s_pcountry'] ) : '',
						's_pcity' 			=> isset( $_POST['s_pcity'] ) ? esc_attr( $_POST['s_pcity'] ) :'',						
						's_pzipcode'		=> isset( $_POST['s_pzipcode'] ) ? $_POST['s_pzipcode'] :''
						 );						 
	$stu_upd 		=	$wpdb->update( $wpsp_student_table , $studenttable, array('wp_usr_id'=>$user_id) );    	
	if (!empty( $_FILES['displaypicture']['name'])) {
		$avatar	=	uploadImage('displaypicture');		
		if( isset( $avatar[ 'url' ] ) ) {
			update_user_meta( $user_id, 'displaypicture', array ( 'full'=>$avatar[ 'url' ] ) ); 
			update_user_meta( $user_id, 'simple_local_avatar', array ( 'full'=>$avatar[ 'url' ] ) ); 
		}	
	}
    if( is_wp_error( $stu_upd ) ) {
        $msg= "<div class='col-md-12 col-lg-12'><div class='alert alert-warning'>".$stu_upd->get_error_message()."</div></div>";
    }else {
        $msg = "<div class='col-md-12 col-lg-12'><div class='alert alert-success'>Student profile updated successfully</div></div>" ;
    }
	echo $msg;
}

/*View Student*/
/* Student Functions */
function wpsp_StudentPublicProfile(){
	global $wpdb;
	$student_table	=	$wpdb->prefix."wpsp_student";
	$class_table	=	$wpdb->prefix."wpsp_class";
	$users_table	=	$wpdb->prefix."users";
	$sid			=	$_POST['id'];
	$stinfo			=	$wpdb->get_row("select a.*,b.c_name,d.user_email from $student_table a LEFT JOIN $class_table b ON a.class_id=b.cid LEFT JOIN $users_table d ON d.ID=a.wp_usr_id where a.wp_usr_id='$sid'");
	if(!empty($stinfo) ) {
		$loc_avatar		=	get_user_meta($stinfo->wp_usr_id,'simple_local_avatar',true);
		$img_url		=	isset( $loc_avatar['full'] ) && !empty( $loc_avatar['full'] ) ? $loc_avatar['full'] : WPSP_PLUGIN_URL.'img/avatar.png';
		$stinfo->imgurl	=	$img_url;
		$parentID		=	$stinfo->parent_wp_usr_id;
		$parentEmail	=	'';
		if( !empty( $parentID )	) {
			$parentInfo	=	get_userdata( $parentID );
			$parentEmail	=	isset( $parentInfo->data->user_email ) ? $parentInfo->data->user_email :'';			
		}
		$profile = "<section class='content'>
				<div class='row'>
					<div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>
					  <div class='panel panel-info'>
						<div class='panel-heading'>
						  <h3 class='panel-title'>$stinfo->s_fname $stinfo->s_mname $stinfo->s_lname </h3>
						</div>
						<div class='panel-body'>
						<div class='row'>
							<div class='col-md-3 col-lg-3'>
								<img src='$img_url' height='150px' width='150px' class='img img-circle'/>							
							</div>
							<div class=' col-md-9 col-lg-9 '> 
								<table class='table table-user-information'>
									<tbody>
										<tr>
											<td class='bold'>Roll No.</td>
											<td>$stinfo->s_rollno</td>
										</tr>
										<tr>
											<td class='bold'>Class </td>
											<td>
												 $stinfo->c_name
											</td>
										</tr>
										<tr>
											<td class='bold'>Gender</td>
											<td>
												 $stinfo->s_gender
											</td>
										</tr>
										<tr>
											<td class='bold'>Date of Birth</td>
											<td>".
											wpsp_ViewDate($stinfo->s_dob)
											."</td>
										</tr>
										<tr>
											<td class='bold'>Date of Join</td>
											<td>".
												 wpsp_ViewDate($stinfo->s_doj)
											."</td>
										</tr>
										<tr>
											<td class='bold'>Address</td>
											<td>$stinfo->s_address</td>
										</tr>
										<tr>
											<td class='bold'>City</td>
											<td>$stinfo->s_pcity</td>
										</tr>
										<tr>
											<td class='bold'>Country</td>
											<td>$stinfo->s_country</td>
										</tr>
										<tr>
											<td class='bold'>ZipCode</td>
											<td>$stinfo->s_zipcode</td>
										</tr>
										<tr>
											<td class='bold'>Email</td>
											<td>$stinfo->user_email</td>
										</tr>
										<tr>
											<td class='bold'>Blood Group</td>
											<td>$stinfo->s_bloodgrp</td>
										</tr>
										<tr>
											<td class='bold'>Phone Number</td>
											<td>
												$stinfo->s_phone
											</td>
										</tr>
										<tr>
											<td class='bold'>Parent Name</td>
											<td>
												$stinfo->p_fname  $stinfo->p_mname  $stinfo->p_lname
											</td>
										</tr>
										<tr>
											<td class='bold'>Parent Gender</td>
											<td>$stinfo->p_gender</td>
										</tr>
										<tr>
											<td class='bold'>Parent Email</td>
											<td>$parentEmail</td>
										</tr>
										<tr>
											<td class='bold'>Parent Profession</td>
											<td>$stinfo->p_profession</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						</div>
						<div class='panel-footer text-right'>							
							<a data-original-title='Remove this user' type='button' data-dismiss='modal' class='btn btn-sm btn-default'>Close</a>
						</div>
					  </div>
					</div>
				</div>
			</section>";
	}else{
		$profile ="No date retrived";
	}
	echo $profile;
	wp_die();
}
?>