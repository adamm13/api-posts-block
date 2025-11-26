<?php
/**
 * Server-side rendering for the API Posts Block.
 *
 * Simple, readable helper functions to fetch articles and build HTML.
 *
 * @package APIPostsBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Show admin notice with connection test.
 */
function api_posts_block_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Test connectivity to Dev.to
	$url      = 'https://dev.to/api/articles?per_page=1';
	$args     = array(
		'timeout'    => 5,
		'sslverify'  => false,
		'user-agent' => 'WordPress/' . get_bloginfo( 'version' ),
	);
	$response = wp_remote_get( $url, $args );

	if ( is_wp_error( $response ) ) {
		echo '<div class="notice notice-error"><p>';
		echo '<strong>API Posts Block:</strong> Your server cannot reach Dev.to. Error: ' . esc_html( $response->get_error_message() );
		echo '</p></div>';
	} else {
		$code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $code ) {
			echo '<div class="notice notice-warning"><p>';
			echo '<strong>API Posts Block:</strong> Dev.to API returned status ' . intval( $code ) . '. This may indicate a firewall or proxy issue.';
			echo '</p></div>';
		}
	}
}

add_action( 'admin_notices', 'api_posts_block_admin_notice' );

/**
 * Render the block on the front end.
 *
 * Reads block attributes, fetches articles, and returns HTML.
 *
 * @param array $attributes Block attributes
 * @return string HTML output
 */
function api_posts_block_render_callback( $attributes ) {
	$columns           = isset( $attributes['columns'] ) ? (int) $attributes['columns'] : 3;
	$show_image        = isset( $attributes['showImage'] ) ? (bool) $attributes['showImage'] : true;
	$show_reading_time = isset( $attributes['showReadingTime'] ) ? (bool) $attributes['showReadingTime'] : true;

	// Make sure columns is either 2 or 3
	$columns = in_array( $columns, array( 2, 3 ), true ) ? $columns : 3;

	// Get articles from Dev.to (cached)
	$articles = api_posts_block_fetch_articles();

	if ( empty( $articles ) ) {
		return '<div class="api-posts-block-error" style="padding: 20px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; color: #856404;">
			<strong>API Posts Block:</strong> Unable to fetch articles from Dev.to. 
			<br/>Check your server\'s internet connection and firewall rules. 
			<br/>See WordPress error logs (wp-content/debug.log) for details.
		</div>';
	}

	// Start building the HTML
	$html = '<div class="api-posts-block-wrapper" data-columns="' . esc_attr( $columns ) . '">';
	$html .= '<div class="api-posts-block-grid">';

	foreach ( $articles as $article ) {
		$html .= api_posts_block_render_card( $article, $show_image, $show_reading_time );
	}

	$html .= '</div></div>';

	return $html;
}

/**
 * Fetch articles from Dev.to and cache the response.
 *
 * Returns an array of articles or an empty array on failure.
 *
 * @return array
 */
function api_posts_block_fetch_articles() {
	$cache_key = 'api_posts_block_articles';
	$cached    = get_transient( $cache_key );

	if ( false !== $cached ) {
		return $cached;
	}

	$url      = 'https://dev.to/api/articles?per_page=10&sort=-published_at';
	$args     = array(
		'timeout'    => 10,
		'sslverify'  => false, // Disable SSL check if firewall/proxy issues
		'user-agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ),
	);
	$response = wp_remote_get( $url, $args );

	// Check for connection errors
	if ( is_wp_error( $response ) ) {
		$error_msg = $response->get_error_message();
		error_log( 'API Posts Block: Failed to reach Dev.to API - ' . $error_msg );
		
		// If server can't reach Dev.to, use demo articles for testing
		return api_posts_block_get_demo_articles();
	}

	// Get the response body
	$body     = wp_remote_retrieve_body( $response );
	$articles = json_decode( $body, true );

	// Log if decode fails or no articles
	if ( ! $articles || ! is_array( $articles ) ) {
		$status = wp_remote_retrieve_response_code( $response );
		error_log( 'API Posts Block: Invalid response or no articles. Status: ' . $status . ', Body: ' . substr( $body, 0, 200 ) );
		
		// Fall back to demo articles
		return api_posts_block_get_demo_articles();
	}

	// Cache for one hour
	set_transient( $cache_key, $articles, HOUR_IN_SECONDS );

	return $articles;
}

/**
 * Get demo/fallback articles for testing when API is unavailable.
 *
 * @return array
 */
function api_posts_block_get_demo_articles() {
	$images = array(
		'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=400&h=300&fit=crop',
		'https://images.unsplash.com/photo-1555066931-4365d440a117?w=400&h=300&fit=crop',
		'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=400&h=300&fit=crop',
		'https://images.unsplash.com/photo-1516534775068-bb4f5e1b5be3?w=400&h=300&fit=crop',
		'https://images.unsplash.com/photo-1552664730-d307ca884978?w=400&h=300&fit=crop',
	);

	return array(
		array(
			'id'                     => 1,
			'title'                  => 'Getting Started with WordPress Gutenberg Blocks',
			'description'            => 'Learn how to build custom Gutenberg blocks with React and PHP. This comprehensive guide covers block registration, attributes, and server-side rendering.',
			'url'                    => 'https://dev.to',
			'published_at'           => gmdate( 'c', time() - 86400 * 3 ),
			'cover_image'            => $images[0],
			'reading_time_minutes'   => 8,
		),
		array(
			'id'                     => 5,
			'title'                  => 'WordPress API Performance Tips',
			'description'            => 'Optimize your WordPress site with caching strategies, API calls, and transients. Discover best practices for handling external data.',
			'url'                    => 'https://dev.to',
			'published_at'           => gmdate( 'c', time() - 86400 * 5 ),
			'cover_image'            => $images[1],
			'reading_time_minutes'   => 6,
		),
		array(
			'id'                     => 1,
			'title'                  => 'Building Responsive Web Layouts',
			'description'            => 'Master CSS Grid and Flexbox to create beautiful, responsive layouts that work on all devices. Includes modern techniques and browser support.',
			'url'                    => 'https://dev.to',
			'published_at'           => gmdate( 'c', time() - 86400 * 7 ),
			'cover_image'            => $images[2],
			'reading_time_minutes'   => 10,
		),
		array(
			'id'                     => 5,
			'title'                  => 'JavaScript Async/Await Explained',
			'description'            => 'Understand asynchronous programming in JavaScript with clear examples. Learn the differences between promises, callbacks, and async/await.',
			'url'                    => 'https://dev.to',
			'published_at'           => gmdate( 'c', time() - 86400 * 9 ),
			'cover_image'            => $images[3],
			'reading_time_minutes'   => 7,
		),
		array(
			'id'                     => 1,
			'title'                  => 'PHP Security Best Practices',
			'description'            => 'Protect your PHP applications from common vulnerabilities. Learn about input validation, SQL injection prevention, and secure authentication.',
			'url'                    => 'https://dev.to',
			'published_at'           => gmdate( 'c', time() - 86400 * 10 ),
			'cover_image'            => $images[4],
			'reading_time_minutes'   => 9,
		),
	);
}

/**
 * Build the HTML for one article card.
 *
 * Sanitizes data and returns a small HTML snippet for the card.
 *
 * @param array $article Article data
 * @param bool  $show_image Whether to show the cover image
 * @param bool  $show_reading_time Whether to show reading time
 * @return string HTML card markup
 */
function api_posts_block_render_card( $article, $show_image, $show_reading_time ) {
	$title          = isset( $article['title'] ) ? sanitize_text_field( $article['title'] ) : 'No Title';
	$description    = isset( $article['description'] ) ? wp_kses_post( $article['description'] ) : '';
	$url            = isset( $article['url'] ) ? esc_url( $article['url'] ) : '#';
	$published_at   = isset( $article['published_at'] ) ? sanitize_text_field( $article['published_at'] ) : '';
	$cover_image    = isset( $article['cover_image'] ) ? esc_url( $article['cover_image'] ) : '';
	$reading_time   = isset( $article['reading_time_minutes'] ) ? (int) $article['reading_time_minutes'] : 0;

	// Format published date
	$published_date = '';
	if ( ! empty( $published_at ) ) {
		$published_date = gmdate( 'F j, Y', strtotime( $published_at ) );
	}

	$html = '<article class="api-posts-block-card">';

	// Cover image
	if ( $show_image && ! empty( $cover_image ) ) {
		$html .= '<div class="api-posts-block-card-image">';
		$html .= '<img src="' . $cover_image . '" alt="' . esc_attr( $title ) . '" />';
		$html .= '</div>';
	}

	$html .= '<div class="api-posts-block-card-content">';

	// Title
	$html .= '<h3 class="api-posts-block-card-title">';
	$html .= '<a href="' . $url . '" target="_blank" rel="noopener noreferrer">' . $title . '</a>';
	$html .= '</h3>';

	// Description
	if ( ! empty( $description ) ) {
		$html .= '<p class="api-posts-block-card-description">' . wp_kses_post( $description ) . '</p>';
	}

	// Meta information
	$html .= '<div class="api-posts-block-card-meta">';

	// Date
	if ( ! empty( $published_date ) ) {
		$html .= '<span class="api-posts-block-card-date">' . esc_html( $published_date ) . '</span>';
	}

	// Reading time
	if ( $show_reading_time && $reading_time > 0 ) {
		$html .= '<span class="api-posts-block-card-reading-time">' . esc_html( $reading_time ) . ' min read</span>';
	}

	$html .= '</div>';
	$html .= '</div>';
	$html .= '</article>';

	return $html;
}
