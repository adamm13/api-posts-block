<?php
/**
 * Plugin Name: API Posts Block
 * Plugin URI: https://github.com/your-username/api-posts-block
 * Description: A custom Gutenberg block that fetches and displays articles from the Dev.to API in a styled card layout
 * Version: 5.14
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
 * Register the block and its assets.
 */
function api_posts_block_register_block() {
    // Register the editor script.
    // This script is the compiled bundle that provides the block UI in Gutenberg.
    // We register it here so WordPress can manage dependencies and translations.
	wp_register_script(
		'api-posts-block-editor',
		API_POSTS_BLOCK_URL . 'build/main.js',
		array( 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-block-editor', 'wp-components' ),
		API_POSTS_BLOCK_VERSION
	);

	// Register the frontend stylesheet.
	// This file is enqueued automatically when the block is rendered on the front end.
	wp_register_style(
		'api-posts-block-style',
		API_POSTS_BLOCK_URL . 'build/style.css',
		array(),
		API_POSTS_BLOCK_VERSION
	);

	// Register the editor-only stylesheet.
	// These styles improve the block appearance inside the Gutenberg editor.
	wp_register_style(
		'api-posts-block-editor-style',
		API_POSTS_BLOCK_URL . 'build/editor.css',
		array(),
		API_POSTS_BLOCK_VERSION
	);

	// Include block render callback
	require_once API_POSTS_BLOCK_DIR . 'includes/render-block.php';

	// Register the block type with attributes and server-side render callback.
	// - 'editor_script' attaches the editor JS
	// - 'style' is the frontend CSS
	// - 'editor_style' is loaded only in the editor
	// - 'render_callback' makes the block server-side rendered (SSR)
	//   so the PHP function will output the final HTML on the page.

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
 * Load editor-only assets when the block editor is active.
 */
function api_posts_block_enqueue_editor_assets() {
	// When the editor is loaded, explicitly enqueue the editor script and styles.
	// This ensures the block UI and editor CSS are available for the block inspector and preview.
	wp_enqueue_script( 'api-posts-block-editor' );
	wp_enqueue_style( 'api-posts-block-editor-style' );
}

add_action( 'enqueue_block_editor_assets', 'api_posts_block_enqueue_editor_assets' );
