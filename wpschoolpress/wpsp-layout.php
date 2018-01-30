<?php
function wpsp_header(){
  echo "<!DOCTYPE html>
<html>
  <head>
    <meta charset='UTF-8'>
    <title>WPSchoolPress</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <link href='".plugins_url('css/bootstrap.min.css', __FILE__ )."' rel='stylesheet' type='text/css' />
    <!-- Font Awesome Icons -->
    <link href='".plugins_url('css/font-awesome.min.css', __FILE__ )."' rel='stylesheet' type='text/css' />
    <!-- Ionicons -->
    <link href='".plugins_url('css/ionicons.min.css', __FILE__ )."'  rel='stylesheet' type='text/css' />
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link href='".plugins_url('css/skins/_all-skins.css', __FILE__ )."'  rel='stylesheet' type='text/css' />
    <link href='".plugins_url('plugins/jQueryUI/jquery-ui.min.css', __FILE__ )."'  rel='stylesheet' type='text/css' />
    <link href='".plugins_url('css/pnotify.min.css', __FILE__ )."' rel='stylesheet' type='text/css' />
    ";

	if( is_user_logged_in() ) {
		echo "<link href='".plugins_url('plugins/datatables/dataTables.responsive.css', __FILE__ )."'  rel='stylesheet' type='text/css' />";
		echo "<link href='".plugins_url('plugins/datatables/dataTables.bootstrap.css', __FILE__ )."'  rel='stylesheet' type='text/css' />"; 
	} 
  if ( is_page( 'sch-student' ) ) {
    echo "<link href='".plugins_url('plugins/gallery/blueimp-gallery.min.css', __FILE__ )."'  rel='stylesheet' type='text/css' />";
  }
  
  if( is_page('sch-dashboard') ) {
	 echo "<link href='".plugins_url('plugins/fullcalendar/fullcalendar.min.css', __FILE__ )."'  rel='stylesheet' type='text/css' />";
  }
  
  if ( is_page( 'sch-events' ) ) { 
    echo "<link href='".plugins_url('plugins/fullcalendar/fullcalendar.min.css', __FILE__ )."'  rel='stylesheet' type='text/css' />";
	echo "<link href='".plugins_url('plugins/timepicker/bootstrap-timepicker.css', __FILE__ )."' rel='stylesheet' type='text/css' />";
  }
  
  if ( is_page( 'sch-messages' ) || is_page('sch-payment') || is_page('sch-parent') )
  {
     echo "<link href='".plugins_url('plugins/multiselect/jquery.multiselect.css', __FILE__ )."'  rel='stylesheet' type='text/css' />";
  }
	
  if( is_page( 'sch-settings' ) ){
	echo "<link href='".plugins_url('plugins/timepicker/bootstrap-timepicker.css', __FILE__ )."' rel='stylesheet' type='text/css' />";
  }
  
  echo "
  <!-- Theme style -->
    <link href='".plugins_url('css/AdminLTE.css', __FILE__ )."'  rel='stylesheet' type='text/css' />";
    
  echo "<link href='".plugins_url('css/custome.css', __FILE__ )."' rel='stylesheet' type='text/css' />";

  
  echo "<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src='https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js'></script>
        <script src='https://oss.maxcdn.com/respond/1.4.2/respond.min.js'></script>
    <![endif]-->";
    echo " 
  </head>";
}
  function wpsp_topbar()
{
    global $current_user, $wpdb, $wpsp_settings_data,$post,$current_user_name;	
    $loc_avatar	=	get_user_meta( $current_user->ID,'simple_local_avatar',true);
	$role		=	isset( $current_user->roles[0] ) ? $current_user->roles[0] : '';
    $img_url	=	$loc_avatar ? $loc_avatar['full'] : WPSP_PLUGIN_URL.'img/avatar.png';	
	$schoolname	=	isset( $wpsp_settings_data['sch_name'] ) && !empty( $wpsp_settings_data['sch_name'] ) ? $wpsp_settings_data['sch_name'] : __( 'WPSchoolPress','WPSchoolPress' );
	$imglogo	=	isset( $wpsp_settings_data['sch_logo'] ) ? $wpsp_settings_data['sch_logo'] : '';
	$schoolyear	=	isset( $wpsp_settings_data['sch_wrkingyear'] ) ? $wpsp_settings_data['sch_wrkingyear'] : '';
	$postname	=	isset( $post->post_name ) ? $post->post_name :'';
	$roles		=	$current_user->roles;	
	$query	=	'';
	$current_user_name	=	$current_user->user_login;
	if( in_array( 'teacher', $roles ) ) {
		$table	= $wpdb->prefix."wpsp_teacher";
		$query	=	"SELECT CONCAT_WS(' ', first_name, middle_name, last_name ) AS full_name FROM $table WHERE wp_usr_id=$current_user->ID";
	} else if( in_array( 'student', $roles ) ) {
		$table  = 	$wpdb->prefix."wpsp_student";
		$query	=	"SELECT CONCAT_WS(' ', s_fname, s_mname, s_lname ) AS full_name FROM $table WHERE wp_usr_id=$current_user->ID";
	} else if( in_array( 'parent', $roles ) )	{
		$table  = 	$wpdb->prefix."wpsp_student";
		$query	=	"SELECT CONCAT_WS(' ', p_fname, p_mname, p_lname ) AS full_name FROM $table WHERE parent_wp_usr_id=$current_user->ID";
	}
	if( !empty( $query ) ) {
		$full_name = $wpdb->get_var( $query );
		$current_user_name	=	!empty( $full_name ) ? $full_name : $current_user_name;
	}
	
	
	?>
	<body class='skin-blue sidebar-mini fixed <?php echo $postname;?>'>
    <div class='wrapper'>
      <header class='main-header'>
        <!-- Logo -->
        <a href='<?php echo site_url('sch-dashboard');?>' class='logo'>
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class='logo-mini'>
			<?php if( !empty($imglogo) ) { ?>
				<img src="<?php echo $imglogo; ?>" class="img img-circle school-logo" style="" width="50px" height="50px">
			<?php } ?></span>
          <!-- logo for regular state and mobile devices -->
          <span class='logo-lg'><?php echo $schoolname;?></span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class='navbar navbar-static-top' role='navigation'>
          <!-- Sidebar toggle button-->
          <a href='#' class='sidebar-toggle' data-toggle='offcanvas' role='button'>
            <span class='sr-only'>Toggle navigation</span>
          </a>
		  <?php if ( !empty($schoolyear ) ) { ?>
			<button class="btn btn-success gap-academicyear ">Academic year <span class="badge"> <?php echo $schoolyear; ?></span></button>
		  <?php } ?>
          <!-- Navbar Right Menu -->
          <div class='navbar-custom-menu'>
            <ul class='nav navbar-nav'>                  
                  <li class="dropdown messages-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-envelope-o"></i>
						<?php $totalMsg	=	wpsp_UnreadCount(); ?>
                        <span class="label label-success"><?php echo $totalMsg; ?></span>
                        </a>                        
                    </li>
            <!-- User Account: style can be found in dropdown.less -->
              <li class='dropdown user user-menu'>
                <a href='#' class='dropdown-toggle' data-toggle='dropdown'>
                  <span class='hidden-xs'>
				  <?php echo $current_user_name;?>
				  <?php //echo $current_user->ID.':'.$current_user->roles[0]; ?>
				  <i class="fa fa-angle-down pull-right" style="margin-top:4px;"></i></span>
                </a>
                <ul class='dropdown-menu'>
                  <!-- User image -->
                  <li class='user-header'>
                    <img src='<?php echo $img_url; ?>' class='img-circle' alt='User Image' />
                    <p><?php echo $current_user_name;?></p>
                  </li>
                  <!-- Menu Footer-->
                  <li class='user-footer'>
				  <?php if( $role == 'administrator' ) { ?>
						<div class='pull-left'>
						  <a href="<?php echo admin_url(); ?> " class='btn btn-primary'>WP Admin</a>
						</div>
				  <?php } ?>
                    <div class='pull-right'>
                      <a href='<?php echo wp_logout_url();?>' class='btn btn-danger'>Sign out</a>
                    </div>
                  </li>
                </ul>
              </li>
              <!-- Control Sidebar Toggle Button -->              
            </ul>
          </div>
        </nav>
      </header>
<?php
}
function wpsp_sidebar()
{
  global $current_user, $wp_roles, $current_user_name;
  $current_user_role=$current_user->roles[0];
  $page=get_the_title();
  $dashboard_page=$message_page=$student_page=$teacher_page=$parent_page=$class_page=$attendance_page=$subject_page=$mark_page=$exam_page=$event_page=$timetable_page=$import_page=$notify_page=$sms_page=$transport_page=$settings_page=$settings_general_page=$settings_wrkhours_page=$settings_subfield_page=$leave_page=$teacher_attendance_page=$settings_chgpw_page = $viewpayment= $addpayment =$payment_page_main='';
  switch( $page )
  {
    case 'Dashboard':
      $dashboard_page="active";
      break;
    case 'Messages':
      $message_page="active";
       break;
    case 'Student':
      $student_page="active";
      break;
    case 'Teacher':
      $teacher_page="active";
      break;
    case 'Parent':
      $parent_page="active";
      break;
    case 'Class':
      $class_page="active";
      break;
    case 'Attendance':
      $attendance_page="active";
      break;
    case 'Subject':
      $subject_page="active";
      break;
    case 'Exams':
      $exam_page="active";
      break;
	case 'Marks':
      $mark_page="active";
      break;
    case 'ImportHistory':
      $import_page="active";
      break;
	case 'Notify':
      $notify_page="active";
      break;
  case 'Payment':
	  if( isset( $_GET['type'] ) && $_GET['type'] =='addpayment' ) 
		$addpayment="active";
	  else
		$viewpayment="active";
	  $payment_page_main = "class='treeview active'";
      break;
    case 'Events':
      $event_page="active";
      break;
    case 'Transport':
      $transport_page="active";
      break;
    case 'LeaveCalendar':
          $leave_page="active";
          break;
    case 'Timetable' :
        $timetable_page='active';
          break;
	case 'Settings':
		$settings_page="class='treeview active'";
		if(isset($_GET['sc']) && $_GET['sc']=='subField')
			$settings_subfield_page="active";
		else if(isset($_GET['sc']) && $_GET['sc']=='WrkHours')
			$settings_wrkhours_page="active";		
		else
			$settings_general_page="active";
      break;
	case 'ChangePassword' :
        $settings_chgpw_page="active";
        break;
	case 'TeacherAttendance':
		$teacher_attendance_page="active";
		break;
  } 
  
  $loc_avatar=get_user_meta($current_user->ID,'simple_local_avatar',true);
  if( $current_user->ID == 1 )
	$img_url	=	  WPSP_PLUGIN_URL.'img/admin.png';  
 else
	$img_url	=	$loc_avatar ? $loc_avatar['full'] : WPSP_PLUGIN_URL.'img/avatar.png';

  echo "<!-- Left side column. contains the logo and sidebar -->
      <aside class='main-sidebar'>
        <!-- sidebar: style can be found in sidebar.less -->
        <section class='sidebar'>
          <!-- Sidebar user panel -->
          <div class='user-panel'>
            <div class='pull-left image'> 
      <img src='".$img_url."' class='img-circle' alt='User Image' />
             
            </div>
            <div class='pull-left info'>
              <p>".$current_user_name."</p>
            </div>
          </div>
           <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class='sidebar-menu'>       
            <li class=".$dashboard_page.">
             <a href='".site_url('sch-dashboard')."'>
                <i class='fa fa-dashboard'></i>
        <span>".__('Dashboard','WPSchoolPress')."</span>
              </a>
            </li>
            <li class=".$message_page.">
             <a href='".site_url('sch-messages')."'>
                <i class='fa fa-inbox'></i>
        <span>".__('Messages','WPSchoolPress')."</span><span class='pull-right label label-primary pull-right'>".wpsp_UnreadCount()."</span>
              </a>
            </li>
            <li class=".$student_page.">
              <a href='".site_url('sch-student')."'>
                <i class='fa fa-users'></i>
                <span>".__('Students','WPSchoolPress')."</span>
              </a>
            </li>
            <li class=".$teacher_page.">
              <a href='".site_url('sch-teacher')."'>
                <i class='fa fa-users'></i>
                <span>".__('Teachers','WPSchoolPress')."</span>
              </a>
            </li>
            <li class=".$parent_page.">
              <a href='".site_url('sch-parent')."'>
                <i class='fa fa-users'></i>
                <span>".__('Parents','WPSchoolPress')."</span>
              </a>
            </li>
      <li class=".$class_page.">
        <a href='".site_url('sch-class')."'>
          <i class='fa fa-bell'></i><span>".__('Classes','WPSchoolPress')."</span>
        </a>
            </li>
      <li class=".$attendance_page.">
        <a href='".site_url('sch-attendance')."'>
          <i class='fa fa-table'></i><span>".__('Attendance','WPSchoolPress')."</span>
        </a>
            </li>
      <li class=".$subject_page.">
        <a href='".site_url('sch-subject')."'>
          <i class='fa fa-book'></i><span>".__('Subjects','WPSchoolPress')."</span>
        </a>
      </li>
      <li class=".$mark_page.">
        <a href='".site_url('sch-marks')."'>
          <i class='fa fa-check-square-o'></i><span>".__('Marks','WPSchoolPress')."</span>
        </a>
            </li>
      <li class=".$exam_page.">
        <a href='".site_url('sch-exams')."'>
          <i class='fa fa-edit'></i><span>".__('Exams','WPSchoolPress')."</span>
        </a>
            </li>
      <li class=".$event_page.">
        <a href='".site_url('sch-events')."'>
          <i class='fa fa-calendar'></i><span>".__('Events','WPSchoolPress')."</span>
        </a>
            </li>
      <li class=".$timetable_page.">
        <a href='".site_url('sch-timetable')."'>
          <i class='fa fa-clock-o'></i><span>".__('Time Table','WPSchoolPress')."</span>
        </a>
      </li> ";
    if($current_user_role=='administrator' || $current_user_role=='teacher') {
        echo "<li class=" . $import_page . ">
        <a href='" . site_url('sch-importhistory') . "'>
          <i class='fa fa-upload'></i><span>" . __('Import History', 'WPSchoolPress') . "</span>
        </a>
       </li>
        <li class=".$notify_page.">
        <a href='".site_url('sch-notify')."'>
          <i class='fa fa-bullhorn'></i><span>".__('Notify','WPSchoolPress')."</span>
        </a>
      </li>     
      ";
    }
	/*if($current_user_role=='administrator' || $current_user_role=='teacher') {	
		echo "<li ".$payment_page_main.">
              <a href='#'>
                <i class='fa fa-cog'></i>
                <span>".__('Payment','WPSchoolPress')."</span>
                <i class='fa fa-angle-left pull-right'></i>
              </a>
              <ul class='treeview-menu'>
                <li class='".$viewpayment."'><a href='".site_url('sch-payment')."'><i class='fa fa-wrench'></i>".__('View Fees','WPSchoolPress')."</a></li>
				<li class='".$addpayment."'><a href='".site_url('sch-payment?type=addpayment')."'><i class='fa fa-check-square-o'></i>".__('Add Fees','WPSchoolPress')."</a></li>				
			</ul>
        </li>";
	} else {
		echo "<li ".$payment_page_main.">
            <a href='".site_url('sch-payment')."'>
				<i class='fa fa-cog'></i>
					<span>".__('View Fees','WPSchoolPress')."</span>                
            </a></li>";
	}*/
     echo "<li class=".$transport_page.">
        <a href='".site_url('sch-transport')."'>
          <i class='fa fa-road'></i><span>".__('Transport','WPSchoolPress')."</span>
        </a>
       </li>";
      if($current_user_role=='administrator')
      {
          echo "<li class=".$teacher_attendance_page.">
        <a href='".site_url('sch-teacherattendance')."'>
          <i class='fa fa-signal'></i><span>".__('Teacher Attendance','WPSchoolPress')."</span>
        </a>
       </li>";
        echo"
		
		<li ".$settings_page.">
              <a href='#'>
                <i class='fa fa-cog'></i>
                <span>".__('Settings','WPSchoolPress')."</span>
                <i class='fa fa-angle-left pull-right'></i>
              </a>
              <ul class='treeview-menu'>
                <li class='".$settings_general_page."'><a href='".site_url('sch-settings')."'><i class='fa fa-wrench'></i>".__('General Settings','WPSchoolPress')."</a></li>
                <li class='".$settings_subfield_page."'><a href='".site_url('sch-settings?sc=subField')."'><i class='fa fa-check-square-o'></i>".__('Subject Mark Fields','WPSchoolPress')."</a></li>
				<li class='".$settings_wrkhours_page."'><a href='".site_url('sch-settings?sc=WrkHours')."'><i class='fa fa-clock-o'></i>".__('Working Hours','WPSchoolPress')."</a></li>				
              </ul>
            </li>";
      }
	  echo "<li class='".$settings_chgpw_page."'><a href='".site_url('sch-changepassword')."'><i class='fa fa-key fa-fw'></i>".__('Change Password','WPSchoolPress')."</a></li>";
      echo "<li class='".$leave_page."'><a href='".site_url('sch-leavecalendar')."'><i class='fa fa-strikethrough'></i>".__('Leave Calendar','WPSchoolPress')."</a></li>
          </ul>
        </section>
      </aside>";
}
function wpsp_body_start()
{
  echo "<!-- Content Wrapper. Contains page content -->
      <div class='content-wrapper'>
        <!-- Content Header (Page header) -->
        
        <!-- Main content -->";
         
}
function wpsp_body_end()
{
  echo "</div><!-- /.content-wrapper -->
      <footer class='main-footer'>
        <div class='pull-right hidden-xs'>
          <b>Version</b> ".WPSP_PLUGIN_VERSION."
        </div>
        <strong>Copyright &copy;".date('Y')." <a href='http://wpschoolpress.com'>WPSchoolPress</a>.</strong> All rights reserved.
      </footer>
    <!-- Control Sidebar -->
      <aside class='control-sidebar control-sidebar-dark'>
        <!-- Create the tabs -->
        
      </aside><!-- /.control-sidebar -->
    </div><!-- ./wrapper -->";
}
function wpsp_footer()
{
  echo "
    <script src='".plugins_url("plugins/jQuery/jQuery-2.1.4.min.js",__FILE__)."'></script>
  <script>
    jQuery(function($) {
    ajax_url ='".admin_url( 'admin-ajax.php' )."';
    date_format='mm/dd/yy';
    $('.content-wrapper').on('click',function(){
      $('.control-sidebar').removeClass('control-sidebar-open');
    });
    
  });
  </script>
  <!-- jQuery Validate -->
  <script src='".plugins_url("plugins/jQuery/jquery.validate.min.js",__FILE__)."'></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src='".plugins_url("js/lib/bootstrap.min.js",__FILE__)."' type='text/javascript'></script>
    <!-- FastClick -->
    <script src='".plugins_url("plugins/fastclick/fastclick.min.js",__FILE__)."'></script>
    <!-- AdminLTE App -->
    <script src='".plugins_url("js/lib/app.js' type='text/javascript",__FILE__)."'></script>
  <script src='".plugins_url("js/lib/pnotify.min.js' type='text/javascript",__FILE__)."'></script>
    <!-- SlimScroll 1.3.0 -->
    <script src='".plugins_url("plugins/slimScroll/jquery.slimscroll.min.js",__FILE__)."' type='text/javascript'></script>
  <script src='".plugins_url("plugins/jQueryUI/jquery-ui.min.js",__FILE__)."' type='text/javascript'></script>
  ";
  if( is_user_logged_in() ) {
		echo "<script src='".plugins_url("plugins/datatables/jquery.dataTables.min.js",__FILE__)."' > </script>";
		echo "<script src='".plugins_url("plugins/datatables/dataTables.bootstrap.min.js",__FILE__)."' > </script>";	 
  }
  if (is_page('sch-dashboard') ) {    
	echo "<script src='".plugins_url("plugins/fullcalendar/moment.min.js",__FILE__)."' > </script>";
	echo "<script src='".plugins_url("plugins/fullcalendar/fullcalendar.min.js",__FILE__)."' > </script>";
	echo "<script src='".plugins_url("plugins/timepicker/bootstrap-timepicker.js",__FILE__)."' type='text/javascript' > </script>";
	echo "<script src='".plugins_url("js/wpsp-dashboard.js",__FILE__)."' > </script>";
  }
  if ( is_page( 'sch-student' ) ) 
  {
      echo "<script src='".plugins_url("plugins/fileupload/jquery.fileupload.js",__FILE__)."' > </script>";
      echo "<script src='".plugins_url("plugins/fileupload/jquery.iframe-transport.js",__FILE__)."' > </script>";
      echo "<script src='".plugins_url("plugins/gallery/jquery.blueimp-gallery.min.js",__FILE__)."' > </script>";
      echo "<script src='".plugins_url("js/wpsp-student.js",__FILE__)."' > </script>";
  }
  if ( is_page( 'sch-teacher' ) ) 
  { 
      echo "<script src='".plugins_url("js/wpsp-teacher.js",__FILE__)."' > </script>";
  }
  if ( is_page( 'sch-parent' ) ) 
  { 
      echo "<script src='".plugins_url("js/wpsp-parent.js",__FILE__)."' > </script>";
  }
  if ( is_page( 'sch-class' ) ) 
  {
    echo "<script src='".plugins_url("js/wpsp-class.js",__FILE__)."' > </script>";         
  }
  if ( is_page( 'sch-attendance' ) ) 
  {
    echo "<script src='".plugins_url("js/wpsp-attendance.js",__FILE__)."' > </script>";        
  }
  if ( is_page( 'sch-subject' ) ) 
  {
    echo "<script src='".plugins_url("js/wpsp-subject.js",__FILE__)."' > </script>";         
  }
  if ( is_page( 'sch-exams' ) ) 
  {
    echo "<script src='".plugins_url("js/wpsp-exam.js",__FILE__)."' > </script>";         
  }
  if ( is_page( 'sch-marks' ) ) 
  {
      echo "<script src='".plugins_url("js/wpsp-mark.js",__FILE__)."' > </script>";         
  }
  if ( is_page( 'sch-timetable' ) ) 
  {
        echo "<script src='".plugins_url("plugins/jQueryUI/jquery.easyui.min.js",__FILE__)."' > </script>";
        echo "<script src='".plugins_url("js/wpsp_timetable.js",__FILE__)."' > </script>";
  }
   if ( is_page( 'sch-settings' ) ) 
  {
	echo "<script src='".plugins_url("plugins/timepicker/bootstrap-timepicker.js",__FILE__)."' type='text/javascript' > </script>";
	echo "<script src='".plugins_url("js/wpsp-settings.js",__FILE__)."' > </script>";
	
  }
   if (is_page('sch-importhistory')) 
  {
	echo "<script src='".plugins_url("js/wpsp-importhistory.js",__FILE__)."' > </script>";    
	
  }
  if (is_page('sch-transport'))
  {
    echo "<script src='".plugins_url("js/wpsp-transport.js",__FILE__)."' > </script>";
  }
  if (is_page('sch-teacherattendance'))
  {
    echo "<script src='".plugins_url("js/wpsp-teacherattendance.js",__FILE__)."' > </script>";
  }
  if (is_page('sch-notify'))
  {
    echo "<script src='".plugins_url("js/wpsp-notify.js",__FILE__)."' > </script>";
  }
  if (is_page('sch-events')) 
  {
	echo "<script src='".plugins_url("plugins/fullcalendar/moment.min.js",__FILE__)."' > </script>";
	echo "<script src='".plugins_url("plugins/fullcalendar/fullcalendar.min.js",__FILE__)."' > </script>";
	echo "<script src='".plugins_url("plugins/timepicker/bootstrap-timepicker.js",__FILE__)."' type='text/javascript' > </script>";
	echo "<script src='".plugins_url("js/wpsp-events.js",__FILE__)."' > </script>";
  }
  if (is_page('sch-leavecalendar'))
  {
    //echo "<script src='".plugins_url("plugins/fullcalendar/moment.min.js",__FILE__)."' > </script>";
    echo "<script src='".plugins_url("js/wpsp-leavecalendar.js",__FILE__)."' > </script>";
  }
  if (is_page('sch-messages') || is_page('sch-parent'))
    {
        echo "<script src='".plugins_url("plugins/multiselect/jquery.multiselect.js",__FILE__)."' > </script>";
        echo "<script src='".plugins_url("js/wpsp-messages.js",__FILE__)."' > </script>";
    }
  if ( is_page( 'sch-changepassword' ) )
  {
    echo "<script src='".plugins_url("js/wpsp-changepassword.js",__FILE__)."' > </script>";         
  }
  if ( is_page( 'sch-payment' ) )
  {
	  echo "<script src='".plugins_url("plugins/multiselect/jquery.multiselect.js",__FILE__)."' > </script>";
  }
  do_action( 'wpsp_footer_script' );
  echo "
  </body>
  </html>";
}
?>