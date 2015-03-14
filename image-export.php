<?php
/*
Plugin Name: Image Export
Plugin URI: http://www.1efthander.com/category/wordpress-plugins/image-export
Description: Image Export 플러그인은 관리자가 업로드한 이미지를 선택적으로 다운로드할 수 있도록 도와줍니다.
Version: 1.0.0
Author: 1eftHander
Author URI: http://www.1efthander.com
*/

define( 'IMAGE_EXPORT_VERSION', '1.0.0' );
define( 'DOWNLOAD_PATH', dirname( __FILE__ ) );
define( 'DOWNLOAD_FILE_NAME', 'images.zip' );

/**
 * 관리자페이지 Javascript 추가
 */
function ie_enqueue_scripts() {
	global $current_screen;
	if ( 'upload' != $current_screen->id ) return false;
	
	wp_register_script( 'image_export', plugins_url( 'image-export-min.js', __FILE__ ), false, IMAGE_EXPORT_VERSION );
	wp_enqueue_script( 'image_export' );
	$textdomains = array(
		'm001' => __( 'Image Export', 'image-export' ),
		'm002' => __( 'Please select the image you want to download.', 'image-export' )	
	);
	wp_localize_script( 'image_export', 'message', $textdomains );
	wp_localize_script( 'image_export', 'obj', array( 'link' => admin_url( 'admin-ajax.php' ), 'params' => $params ) );
}
add_action( 'admin_enqueue_scripts', 'ie_enqueue_scripts' );


/**
 * 이미지 다운로드 처리
 */
function file_size_format( $file_size ) {
	if ( $file_size < 1024 ) {
		return $file_size . ' B';
	} elseif ( $file_size < 1048576 ) {
		return round( $file_size / 1024, 2 ) . ' KB';
	} elseif ( $file_size < 1073741824 ) {
		return round( $file_size / 1048576, 2 ) . ' MB';
	} elseif ( $file_size < 1099511627776 ) {
		return round( $file_size / 1073741824, 2 ) . ' GB';
	} elseif ( $file_size < 1125899906842624 ) {
		return round( $file_size / 1099511627776, 2 ) . ' TB';
	} elseif ( $file_size < 1152921504606846976 ) {
		return round( $file_size / 1125899906842624, 2 ) . ' PB';
	} elseif ( $file_size < 1180591620717411303424 ) {
		return round( $file_size / 1152921504606846976, 2 ) . ' EB';
	} elseif ( $file_size < 1208925819614629174706176 ) {
		return round( $file_size / 1180591620717411303424, 2 ) . ' ZB';
	} else {
		return round( $file_size / 1208925819614629174706176, 2 ) . ' YB';
	}
}
function ie_execute() {
	$ids = array();
	$lists = array();
	$rets = array(
		'url' => '',
		'msg' => ''
	);
	
	$id = $_POST['id'];
	$temps = explode( ',', $id );
	
	foreach ( $temps as $temp ) {
		$ids[] = intval( $temp );
	}
	
	$args = array(
		'posts_per_page' => -1,
		'include'        => $ids,
		'post_type'      => 'attachment',
		'post_status'    => 'inherit'
	);
	
	$posts = get_posts( $args );
	
	foreach ( $posts as $post ) {
		array_push( $lists, get_attached_file( $post->ID ) );
	}
	
	require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';
	$zip_file = new PclZip( DOWNLOAD_PATH . '/' . DOWNLOAD_FILE_NAME );
	$archive = $zip_file->create( $lists, PCLZIP_OPT_REMOVE_ALL_PATH );
	$file_size = file_size_format( filesize( DOWNLOAD_PATH . '/' . DOWNLOAD_FILE_NAME ) );
	
	if ( !empty( $archive ) ) {
		$rets['url'] = plugins_url( 'download.php', __FILE__ ) . '?file=' . DOWNLOAD_FILE_NAME;
		$rets['msg'] = '<div class="updated"><p><strong>' . sprintf( __( 'Image.zip file has been downloaded. Export file size : %s', 'image-export' ) . '</strong></p></div>', $file_size );
	} else {
		$rets['msg'] = '<div class="error"><p><strong>' . __( 'Failed to create images.zip. Please contact the developer.', 'image-export' ) . '</strong></p></div>';
	}
	
	echo json_encode( $rets );
	die();
}
add_action( 'wp_ajax_ie_execute', 'ie_execute' );


/**
 * 다국어 설정
 */
function ie_textdomain() {
	load_plugin_textdomain( 'image-export', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'admin_init', 'ie_textdomain' );
?>