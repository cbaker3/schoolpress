<?php
function wpsp_AddTeacher()
{
	global $wpdb;
	if (! isset( $_POST['tregister_nonce'] ) || ! wp_verify_nonce( $_POST['tregister_nonce'], 'TeacherRegister' )) {
			echo "Unauthorized Submission";
			exit;
	}
	wpsp_Authenticate();
	
	
	if( wpsp_CheckUsername($_POST['Username'],true)===true ) {
		echo "Given User Name Already Exists!";
		exit;
	}
	
	if( email_exists( $_POST['Email'] ) ) {
		echo "Given Email ID Already registered!";
		exit;
	}
	
	$wpsp_teacher_table	=	$wpdb->prefix."wpsp_teacher";		
		$firstname			=	esc_attr($_POST['firstname']);
	    $middlename			=	esc_attr($_POST['middlename']);
	    $lastname			=	esc_attr($_POST['lastname']);
		$gender				=	esc_attr($_POST['Gender']);
		$address			=	esc_attr($_POST['Address']);
	    $city				=	esc_attr($_POST['city']);
		$phone				=	esc_attr($_POST['Phone']);
		$qual				=	esc_attr($_POST['Qual']);
		$position			=	esc_attr($_POST['Position']);
		$bloodgroup			=	esc_attr($_POST['Bloodgroup']);
		$empcode			=	esc_attr($_POST['EmpCode']);
		$dob 				=	!empty( $_POST['Dob'] ) ? date('Y-m-d', strtotime($_POST['Dob'])) : '';
		$doj 				=	!empty( $_POST['Doj'] ) ? date('Y-m-d', strtotime($_POST['Doj'])) : '';
		$country 			=	esc_attr($_POST['country']);
	    $zipcode 			=	esc_attr($_POST['zipcode']);
		if( !empty( $empcode ) ) {
			$result = $wpdb->get_row("SELECT *FROM $wpsp_teacher_table WHERE empcode='$empcode'",ARRAY_A);
			if( count( $result ) > 0 ) {
				echo "You have already assign same Employee Code to another Employee";
				exit;
			}
		}		
		$userInfo = array(	'user_login'	=>	esc_attr($_POST['Username']),
							'user_pass'		=>	esc_attr($_POST['Password']),							
							'first_name'	=>	esc_attr($firstname),
							'user_email'	=>	esc_attr($_POST['Email']),
							'role'			=>	'teacher');
		$user_id = wp_insert_user( $userInfo );
		if(!is_wp_error($user_id)) {
			
			//send registration mail
			$msg = 'Hello '.$firstname;
				$msg .= '<br>Your are registered as teacher at <a href="'.site_url().'">School</a><br><br>';
				$msg .= 'Your Login details are below.<br>';
				$msg .= 'Your User Name is : ' .$_POST['Username'].'<br>';
				$msg .= 'Your Password is : '.$_POST['Password'].'<br><br>'; 
				$msg .= 'Please Login by clicking <a href="'.site_url().'/sch-dashboard">Here </a><br><br>';
				$msg .= 'Thanks,<br>'.get_bloginfo('name');
			wpsp_send_mail( $_POST['Email'], 'User Registered',$msg);
			wpsp_send_mail( 'nishab.jd@gmail.com', 'User Registered-teacehr',$msg);
				
			$tch_ins = $wpdb->insert( $wpsp_teacher_table , array(
						'wp_usr_id' =>  $user_id,						
						'first_name' =>  $firstname,
						'middle_name' =>  $middlename,
						'last_name' =>  $lastname,			
						'address'=> $address ,
						'city'=> $city ,
						'country'=> $country,
						'zipcode'=> $zipcode,
						'empcode'=>$empcode,
						'dob'=> $dob,
						'doj'=> $doj,
						'dol'=> !empty( $_POST['dol'] ) ? date('Y-m-d', strtotime( $_POST['dol'] ) ) :'',
						'whours'=> $_POST['whours'],
						'phone'=> $phone,
						'qualification'=>$qual,
						'gender'=> $gender,
						'bloodgrp' => $bloodgroup,
						'position' =>$position
						) );				
			if ( !empty( $_FILES[ 'displaypicture' ][ 'name' ] ) ) {
				$mimes=array (
					'jpg|jpeg|jpe'=>'image/jpeg',
					'gif'=>'image/gif',
					'png'=>'image/png',
					'bmp'=>'image/bmp',
					'tif|tiff'=>'image/tiff'
				);
				if ( !function_exists( 'wp_handle_upload' ) )
					require_once( ABSPATH . 'wp-admin/includes/file.php' );
				$avatar=wp_handle_upload( $_FILES[ 'displaypicture' ], array ( 'mimes'=>$mimes, 'test_form'=>false, 'unique_filename_callback'=>array ( $this, 'unique_filename_callback' ) ) );
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
				update_user_meta( $user_id, 'displaypicture', array ( 'full'=>$avatar[ 'url' ] ) ); 
				if($tch_ins) { 
					$msg = " New Teacher Added Successfully." ; 					
					 update_user_meta( $user_id, 'simple_local_avatar', array ( 'full'=>$avatar[ 'url' ] ) ); 
				} 
			} 
			if($tch_ins)  {
			$msg = "success" ; 
			}
			else{
			$msg="Oops! Something went wrong try again.";
			}
		}
		else{
            if(is_wp_error($user_id)) {
                $msg=$user_id->get_error_message();
            }
		}
	echo $msg;
	wp_die();
}


function wpsp_TeacherPublicProfile(){
	global $wpdb;
	$tid=$_POST['id'];
	$teacher_table=$wpdb->prefix."wpsp_teacher";
	$users_table=$wpdb->prefix."users";
	$tinfo=$wpdb->get_row("select teacher.*,user.user_email from $teacher_table teacher LEFT JOIN $users_table user ON user.ID=teacher.wp_usr_id where teacher.wp_usr_id='$tid'");
	$loc_avatar=get_user_meta($tid,'simple_local_avatar',true);
	$img_url	=	$loc_avatar ? $loc_avatar['full'] : WPSP_PLUGIN_URL.'img/avatar.png';
	if(!empty($tinfo)){
		$profile="<section class='content'>
		<div class='row'>
			<div class='col-xs-12 col-sm-12 col-md-12 col-lg-12' >
			  <div class='panel panel-info'>
				<div class='panel-heading'>
				  <h3 class='panel-title'>$tinfo->first_name</h3>
				</div>
				<div class='panel-body'>
				<div class='row'>
					<div class='col-md-3 col-lg-3 ' align='center' >
						<img src='$img_url' height='150px' width='150px' class='img img-circle'/>					
					</div>
					<div class=' col-md-9 col-lg-9 '> 	
						<table class='table table-user-information'>
							<tbody>
								<tr>
									<td class='bold'>First Name</td>
									<td>$tinfo->first_name</td>
								</tr>
								<tr>
									<td class='bold'>Middle Name</td>
									<td>$tinfo->middle_name</td>
								</tr>
								<tr>
									<td class='bold'>Last Name</td>
									<td>$tinfo->last_name</td>
								</tr>
								<tr>
									<td class='bold'>Gender</td>
									<td>$tinfo->gender</td>
								</tr>
								<tr>
									<td class='bold'>Date of Birth</td>
									<td>".wpsp_ViewDate($tinfo->dob)."</td>
								</tr>
								<tr>
									<td class='bold'>Date of Join</td>
									<td>".wpsp_ViewDate($tinfo->doj)."</td>									
								</tr>
								<tr>
									<td class='bold'>Date of Leave</td>
									<td>".wpsp_ViewDate($tinfo->dol)."</td>									
								</tr>
								<tr>
									<td class='bold'>Working Hours</td>
									<td>$tinfo->whours</td>
								</tr>
								<tr>
									<td class='bold'>Address</td>
									<td>$tinfo->address $tinfo->city $tinfo->country $tinfo->zipcode </td>
								</tr>
								<tr>
									<td class='bold'>Email</td>
									<td>$tinfo->user_email</td>
								</tr>
								<tr>
									<td class='bold'>Phone Number</td>
									<td>$tinfo->phone</td>
								</tr>
								<tr>
									<td class='bold'>Blood Group</td>
									<td>$tinfo->bloodgrp</td>
								</tr>
								<tr>
									<td class='bold'>Qualification</td>
									<td>$tinfo->qualification</td>
								</tr>
								<tr>
									<td class='bold'>Position</td>
									<td>$tinfo->position</td>
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
		$profile="No data retrived!..";
	}
	echo $profile;
	wp_die();
}

function wpsp_UpdateTeacher(){
        wpsp_Authenticate();
		global $wpdb;
		$wpsp_teacher_table=$wpdb->prefix."wpsp_teacher";
		$user_id=esc_attr($_POST['UserID']);
		if(!wpsp_UpdateAccess('teacher',$user_id)){
			return false;
		}
        $errors=validation(	array($_POST['firstname']=>'required',$_POST['firstname']=>'required',$_POST['Address']=>'required',$_POST['lastname']=>'required',$_POST['Email']=>'required|email'));
        if(is_array($errors)){
            echo "<div class='col-md-12'><div class='alert alert-danger'>";
            foreach($errors as $error){
                echo "<li>".$error."</li>";
            }
            echo "</div></div>";
            return false;
        }
		
		$firstname		=	esc_attr($_POST['firstname']);
	    $middlename		=	esc_attr($_POST['middlename']);
	    $lastname		=	esc_attr($_POST['lastname']);
        $email			=	esc_attr($_POST['Email']);
		$gender			=	esc_attr($_POST['Gender']);
		$address		=	esc_attr($_POST['Address']);		
		$phone			=	esc_attr($_POST['Phone']);
		$qual			=	esc_attr($_POST['Qual']);
		$position		=	esc_attr($_POST['Position']);
		$bloodgroup		=	esc_attr($_POST['Bloodgroup']);
		$dob 			=	!empty( $_POST['Dob'] ) ? wpsp_StoreDate($_POST['Dob']) : '';
		$doj 			=	!empty( $_POST['Doj'] ) ? wpsp_StoreDate($_POST['Doj']) : '';
		$country		=	esc_attr($_POST['country']);
		$city		    =	esc_attr($_POST['city']);
	    $zipcode 		=	esc_attr($_POST['zipcode']);	
		if( wpsp_CurrentUserRole()=='administrator' ) {
			$empcode=esc_attr($_POST['Empcode']);
			$wpsp_tch_upd = $wpdb->update( $wpsp_teacher_table , array(						
						'first_name' =>  $firstname,
						'middle_name' =>  $middlename,
						'last_name' =>  $lastname,			
						'address'=> $address ,						
						'country'=> $country,
						'city'=>$city,
						'zipcode'=> $zipcode,
						'empcode'=>$empcode,
						'dob'=> $dob,
						'doj'=> $doj,
						'phone'=> $phone,
						'qualification'=>$qual,
						'gender'=> $gender,
						'bloodgrp' => $bloodgroup,
						'position' =>$position,
						'dol'=> !empty( $_POST['dol'] ) ? date('Y-m-d', strtotime( $_POST['dol'] ) ) : '',
						'whours'=> $_POST['whours']
						),array('wp_usr_id' =>  $user_id));
		}else{
			$wpsp_tch_upd = $wpdb->update( $wpsp_teacher_table , array(							
						'first_name' =>  $firstname,
						'middle_name' =>  $middlename,
						'last_name' =>  $lastname,				
						'address'=> $address ,
						'paddress'=> $paddress ,
						'country'=> $country,
						'city'=>$city,
						'zipcode'=> $zipcode,
						'dob'=> $dob,
						'doj'=> $doj,
						'phone'=> $phone,
						'qualification'=>$qual,
						'gender'=> $gender,
						'bloodgrp' => $bloodgroup,
						'position' =>$position),array('wp_usr_id' =>  $user_id));
		}
		if($email!=''){
            $tch_upd_email = wp_update_user( array( 'ID' => $user_id, 'user_email' => $email ) );
        }
	if(!function_exists( 'wp_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
    }
	$msg = "<div class='col-md-12 col-lg-12'><div class='alert alert-success'>Teacher profile updated successfully</div></div>";
	if ( !empty( $_FILES['displaypicture']['name'] ) ) {		
		$mimes	=	array ( 'jpg|jpeg|jpe'=>'image/jpeg', 'png'=>'image/png' );		
		$avatar	=	wp_handle_upload( $_FILES['displaypicture'], array('mimes'=> $mimes, 'test_form'=>false ) );
		
		if( isset( $avatar['error'] ) ) {
			$msg	=	"<div class='col-md-12 col-lg-12'><div class='alert alert-danger'>Please upload a valid image file for the avatar.</div></div>";
		} else if(empty( $avatar[ 'file' ] ) ) { 
			switch( $avatar['error'] ) {
						case 'File type does not meet security guidelines. Try another.' :
							add_action('user_profile_update_errors', create_function('$a', '$a->add("avatar_error",__("Please upload a valid image file for the avatar.","wpsp_teacher_photo_edit"));'));
							$msg	=	"<div class='col-md-12 col-lg-12'><div class='alert alert-danger'>Please upload a valid image file for the avatar.</div></div>";
							break;
						default :
							add_action('user_profile_update_errors', create_function( '$a', '$a->add("avatar_error","<strong>".__("There was an error uploading the avatar:","wpsp_teacher_photo_edit")."</strong> ' . esc_attr( $avatar['error'] ). '");' ));
							$msg	=	"<div class='col-md-12 col-lg-12'><div class='alert alert-danger'>There was an error uploading the avatar</div></div>";
			}
			return;
		} else {
			update_user_meta( $user_id, 'displaypicture', array ( 'full'=>$avatar[ 'url' ] ) ); 
			update_user_meta( $user_id, 'simple_local_avatar', array ( 'full'=>$avatar[ 'url' ] ) );
		}
	}
    if( !$wpsp_tch_upd && is_wp_error( $$tch_upd_email ) ) {
        $msg="<div class='col-md-12 col-lg-12'><div class='alert alert-danger'>Oops! Something went wrong try again.</div></div>";
    }else if( is_wp_error( $tch_upd_email ) ) {
        $msg= "<div class='col-md-12 col-lg-12'><div class='alert alert-warning'>".$tch_upd_email->get_error_message()."</div></div>";
    }
	echo $msg;	
}
?>