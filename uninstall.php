<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'minuteread_wpm' );
delete_option( 'minuteread_position' );
delete_option( 'minuteread_label' );
delete_option( 'minuteread_format' );
delete_option( 'minuteread_enable' );
delete_option( 'minuteread_post_types' );
