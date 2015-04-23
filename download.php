<?php
if ( isset( $_REQUEST['file'] ) && !empty( $_REQUEST['file'] ) ) {
	$file = $_GET['file'];

	header( 'Content-Type: application/zip' );
	header( 'Content-Disposition: attachment; filename="' . $file . '"' );
	readfile( $file );
	unlink( $file );
	
	exit;
}
?>