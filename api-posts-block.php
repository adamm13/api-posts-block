<?php
/**
 * Plugin Name: API Posts Block
 * Plugin URI: https://github.com/your-username/api-posts-block
 * Description: A custom Gutenberg block that fetches and displays articles from the Dev.to API in a styled card layout
 * Version: 1.0.0
 * Author: Adam Mohammed
 * License: GPL v2 or later
 * Text Domain: api-posts-block
 * Domain Path: /languages
 *
 * @package APIPostsBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'API_POSTS_BLOCK_DIR', plugin_dir_path( __FILE__ ) );
define( 'API_POSTS_BLOCK_URL', plugin_dir_url( __FILE__ ) );
define( 'API_POSTS_BLOCK_VERSION', '1.0.0' );

/**
 * Register the API Posts Block
 */
function api_posts_block_register_block() {
	// Register block script
	wp_register_script(
		'api-posts-block-editor',
		API_POSTS_BLOCK_URL . 'build/main.js',
		array( 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-block-editor', 'wp-components' ),
		API_POSTS_BLOCK_VERSION
	);

	// Register block styles
	wp_register_style(
		'api-posts-block-style',
		API_POSTS_BLOCK_URL . 'build/style.css',
		array(),
		API_POSTS_BLOCK_VERSION
	);

	// Register block editor styles
	wp_register_style(
		'api-posts-block-editor-style',
		API_POSTS_BLOCK_URL . 'build/editor.css',
		array(),
		API_POSTS_BLOCK_VERSION
	);

	// Include block render callback
	require_once API_POSTS_BLOCK_DIR . 'includes/render-block.php';

	// Register the block
	register_block_type(
		'api-posts-block/posts',
		array(
			'editor_script'   => 'api-posts-block-editor',
			'style'           => 'api-posts-block-style',
			'editor_style'    => 'api-posts-block-editor-style',
			'render_callback' => 'api_posts_block_render_callback',
			'attributes'      => array(
				'columns'     => array(
					'type'    => 'number',
					'default' => 3,
				),
				'showImage'   => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'showReadingTime' => array(
					'type'    => 'boolean',
					'default' => true,
				),
			),
		)
	);
}

add_action( 'init', 'api_posts_block_register_block' );

/**
 * Enqueue block assets only in the editor
 */
function api_posts_block_enqueue_editor_assets() {
	wp_enqueue_script( 'api-posts-block-editor' );
	wp_enqueue_style( 'api-posts-block-editor-style' );
}

add_action( 'enqueue_block_editor_assets', 'api_posts_block_enqueue_editor_assets' );
