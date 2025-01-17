<?php

$root = dirname( __DIR__, 8 );

if ( file_exists( __DIR__ . '/local-config.php' ) ) {
	include __DIR__ . '/local-config.php';
} else {
	include __DIR__ . '/default-local-config.php';
}

define( 'WP_PHP_BINARY', 'php' );

if ( ! defined( 'DOMAIN_CURRENT_SITE' ) ) {
	define( 'DOMAIN_CURRENT_SITE', getenv( 'HTTP_HOST' ) );
}

$config_defaults = [
	'ABSPATH'                   => $root . '/wp/',
	'BLOG_ID_CURRENT_SITE'      => 1,
	'BOOTSTRAP'                 => getenv( 'BOOTSTRAP' ),
	'DB_HOST'                   => 'localhost',
	'DB_NAME'                   => getenv( 'DB_NAME' ),
	'DB_PASSWORD'               => getenv( 'DB_PASSWORD' ),
	'DB_USER'                   => getenv( 'DB_USER' ),
	'WP_CONTENT_URL'            => 'http://' . DOMAIN_CURRENT_SITE . '/wp-content',
	'WP_CONTENT_DIR'            => $root . DIRECTORY_SEPARATOR . 'wp-content',
	'WP_DEBUG'                  => true,
	'WP_ENVIRONMENT_TYPE'       => 'local',
	'WP_PHP_BINARY'             => 'php',
	'WP_SITE_ROOT'              => $root . DIRECTORY_SEPARATOR,
	'WP_TESTS_CONFIG_FILE_PATH' => __FILE__,
	'WP_TESTS_DIR'              => $root,
	'WP_TESTS_DOMAIN'           => getenv( 'HTTP_HOST' ),
	'WP_TESTS_MULTISITE'        => getenv( 'WP_TESTS_MULTISITE' ),
	'WP_TESTS_TABLE_PREFIX'     => 'wplibs_',
	'WP_UNIT_DIR'               => getenv( 'WP_UNIT_DIR' ),
];

foreach ( $config_defaults as $config_default_key => $config_default_value ) {
	if ( ! defined( $config_default_key ) ) {
		define( $config_default_key, $config_default_value );
	}
}
