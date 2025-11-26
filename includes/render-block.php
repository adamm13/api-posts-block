<?php
/**
 * Server-side rendering callback for the API Posts Block
 *
 * @package APIPostsBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render callback for the API Posts Block
 *
 * @param array $attributes Block attributes
 * @return string HTML output
 */
function api_posts_block_render_callback( $attributes ) {
	$columns           = isset( $attributes['columns'] ) ? (int) $attributes['columns'] : 3;
	$show_image        = isset( $attributes['showImage'] ) ? (bool) $attributes['showImage'] : true;
	$show_reading_time = isset( $attributes['showReadingTime'] ) ? (bool) $attributes['showReadingTime'] : true;

	// Validate columns value
	$columns = in_array( $columns, array( 2, 3 ), true ) ? $columns : 3;

	// Fetch articles from Dev.to API
	$articles = api_posts_block_fetch_articles();

	if ( empty( $articles ) ) {
		return '<div class="api-posts-block-error">Failed to fetch articles. Please try again later.</div>';
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
 * Fetch articles from Dev.to API
 *
 * @return array Array of articles or empty array on failure
 */
function api_posts_block_fetch_articles() {
	$cache_key = 'api_posts_block_articles';
	$cached    = get_transient( $cache_key );

	if ( false !== $cached ) {
		return $cached;
	}

	$url      = 'https://dev.to/api/articles?per_page=10&sort=-published_at';
	$response = wp_remote_get( $url );

	if ( is_wp_error( $response ) ) {
		return array();
	}

	$body     = wp_remote_retrieve_body( $response );
	$articles = json_decode( $body, true );

	if ( empty( $articles ) || ! is_array( $articles ) ) {
		return array();
	}

	// Cache for 1 hour
	set_transient( $cache_key, $articles, HOUR_IN_SECONDS );

	return $articles;
}

/**
 * Render a single article card
 *
 * @param array   $article Article data
 * @param bool    $show_image Show cover image
 * @param bool    $show_reading_time Show reading time
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
