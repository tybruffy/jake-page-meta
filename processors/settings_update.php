<?php

	global $wpdb;		
	include( TJG_AT_PATH."/lib/lessc.inc.php" );

/**
	Checking if the images were uploaded, and if either of them was, then we attempt to move the file to the plugin folder.
**/

	$lg_reset = isset( $_POST['lgReset'] );
	$sm_reset = isset( $_POST['smReset'] );

/* ==========================================================================
	Save the Theme Color
	========================================================================= */

	if ( ! empty( $_POST['primary'] ) )	{
		$primary_color = $_POST['primary'];
		update_option('admin-theme-primary-color', $_POST['primary']);
	} else {
		$primary_color = get_option("admin-theme-primary-color");
	} 

/* ==========================================================================
	Update the small logo
	========================================================================= */

	if ( ! empty( $_FILES['sm-logo']['name'] ) )	{
		$sm_ext		= pathinfo (basename($_FILES['sm-logo']['name']),PATHINFO_EXTENSION);
		$sm_name 	= 'small-logo.'.$sm_ext;
		$sm_CSS 	= "url('../img/small-logo.$sm_ext?ver=".time()."')";
		$smSwitch 	= "custom";

		if ( move_uploaded_file( $_FILES['sm-logo']['tmp_name'], TJG_AT_PATH.'/img/'.$sm_name ) )	{
			update_option( 'admin-theme-small-logo', $sm_name );
		}

	} elseif ( $sm_reset ) {
		$smSwitch = "wordpress";
		$sm_name  = get_option( "admin-theme-small-logo" ); 

		update_option( 'admin-theme-small-logo', 'wordpress' );

		if ( file_exists( TJG_AT_PATH.'/img/'.$sm_name ) ) { 
			unlink( TJG_AT_PATH.'/img/'.$sm_name ); 
		}

		$sm_CSS = "none";

	} else {
		if ( get_option( "admin-theme-small-logo" ) != 'wordpress' )	{
			$smSwitch = "custom";
			$sm_CSS   = "url('../img/".get_option("admin-theme-small-logo")."?ver=".time()."')";
		} else {
			$smSwitch = "wordpress";
			$sm_CSS   = "none";
		}
	}


/* ==========================================================================
	Custom Hover effect for small logo
	========================================================================= */

	if ( isset( $_POST['custom_hover'] ) )	{
		$custom_hover = 'on';
		update_option( 'admin-theme-custom-hover', "true" );
	} else {
		$custom_hover = 'off';
		update_option( 'admin-theme-custom-hover', 'false' );
	}



/* ==========================================================================
	Save large logo
	========================================================================= */

	if ( ! empty( $_FILES['lg-logo']['name'] ) )	{

		$lg_ext		= pathinfo (basename($_FILES['lg-logo']['name']),PATHINFO_EXTENSION);
		$lg_name 	= 'large-logo.'.$lg_ext;
		$lg_CSS 	= "url('../img/large-logo.$lg_ext?ver=".time()."')";
		$lgSwitch 	= "custom";

		if ( move_uploaded_file( $_FILES['lg-logo']['tmp_name'], TJG_AT_PATH.'/img/'.$lg_name ) )	{
			update_option( 'admin-theme-login-logo', $lg_name );
			$lg_size = getimagesize( TJG_AT_PATH.'/img/'.$lg_name );
		}

	} elseif ($lg_reset) {
		$lgSwitch = "wordpress";
		$lg_name  = get_option("admin-theme-login-logo");

		update_option( 'admin-theme-login-logo', 'wordpress' );

		if ( file_exists( TJG_AT_PATH.'/img/'.$lg_name ) ) { 
			unlink( TJG_AT_PATH.'/img/'.$lg_name ); 
		}

		$lg_CSS = "none";

	} else {
		if ( get_option( "admin-theme-login-logo" ) != 'wordpress' )	{
			$lgSwitch = "custom";
			$lg_CSS   = "url('../img/".get_option("admin-theme-login-logo")."?ver=".time()."')";
		} else {
			$lgSwitch = "wordpress";
			$lg_CSS   = "none";
		}
	}




/* ==========================================================================
	Round Corners on login screen
	========================================================================= */

	if ( isset( $_POST['rounded'] ) )	{
		$rounded = "25px";
		update_option( 'admin-theme-rounded-corners', "true" );
	} else {
		$rounded = "0px";
		update_option( 'admin-theme-rounded-corners', 'false' );
	}



/**
	Dashboard Updates
**/	
	global $dashboard_widget_array;
	foreach ( $dashboard_widget_array as $widget => $ignored )	{

		if ( isset( $_POST[$widget] ) )	{
			update_option( "admin-theme-$widget", "true" );
		} else {
			update_option( "admin-theme-$widget", 'false' );
		}

	}

/**
	Here is where we actually start saving everything to Wordpress, and adding the variables to our less file and then parsing it into an actual css file.
**/

	$CSS_vars = array(
		"primary" 	=> $primary_color,
		"sm-logo" 	=> $sm_CSS,
		"sm-hover" 	=> $custom_hover,
		"lg-logo" 	=> $lg_CSS,
		"rounded" 	=> $rounded,
		"sm-switch" => $smSwitch,
		"lg-switch" => $lgSwitch
	);

	if ( isset( $lg_size) ) {
		$CSS_vars["lg-width"]  = $lg_size[0] . "px";
		$CSS_vars["lg-height"] = $lg_size[1] . "px";
	}

	$less     = new lessc( TJG_AT_PATH . "css/overrides.less" );
	$css      = $less->parse( null, $CSS_vars );
	$css_file = fopen( TJG_AT_PATH . "css/" . $wpdb->prefix . "override.css" , "w" );

	if ( $css_file ) {
		fwrite( $css_file, $css );
		fclose( $css_file );
	}



?>