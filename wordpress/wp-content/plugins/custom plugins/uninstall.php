<?php

defined('WP_UNINSTALL_PLUGIN') or die('access denied');
global $wpdb;
$table_name = $wpdb->prefix . "contactus"; 

$sql = "DROP TABLE IF EXISTS $table_name";

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );