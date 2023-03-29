<?php
/**
 * WP Importer Info Class
 *
 * @package Athemes Starter Sites
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

class ATSS_WXRImportInfo {
	public $home;
	public $siteurl;
	public $title;
	public $users         = array();
	public $post_count    = 0;
	public $media_count   = 0;
	public $comment_count = 0;
	public $term_count    = 0;
	public $generator     = '';
	public $version;
}
