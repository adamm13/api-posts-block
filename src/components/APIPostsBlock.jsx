import React, { useState, useEffect } from 'react';
import {
	InspectorControls,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	ToggleControl,
	Spinner,
	Notice,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import '../styles/block.scss';

const APIPostsBlock = ( { attributes, setAttributes } ) => {
	const { columns, showImage, showReadingTime, showReactions } = attributes;
	const [ articles, setArticles ] = useState( [] );
	const [ loading, setLoading ] = useState( true );
	const [ error, setError ] = useState( null );

	const blockProps = useBlockProps( {
		className: `api-posts-block columns-${ columns }`,
	} );

	// Fetch articles on component mount
	useEffect( () => {
		fetchArticles();
	}, [] );

	const fetchArticles = async () => {
		setLoading( true );
		setError( null );

		try {
			const response = await fetch(
				'https://dev.to/api/articles?per_page=10&sort=-published_at'
			);

			if ( ! response.ok ) {
				throw new Error( 'Failed to fetch articles' );
			}

			const data = await response.json();
			setArticles( data );
		} catch ( err ) {
			setError(
				err.message ||
					__( 'Failed to fetch articles from Dev.to API', 'api-posts-block' )
			);
			setArticles( [] );
		} finally {
			setLoading( false );
		}
	};

	const renderCard = ( article ) => {
		const {
			title,
			description,
			url,
			published_at: publishedAt,
			cover_image: coverImage,
			reading_time_minutes: readingTime,
			reactions_count: reactionsCount,
			user,
		} = article;

		const publishedDate = publishedAt
			? new Date( publishedAt ).toLocaleDateString( 'en-US', {
					year: 'numeric',
					month: 'long',
					day: 'numeric',
			  } )
			: '';

		const authorName = user?.name || 'Unknown';

		return (
			<article key={ article.id } className="api-posts-block-card">
				{ showImage && coverImage && (
					<div className="api-posts-block-card-image">
						<img src={ coverImage } alt={ title } />
					</div>
				) }

				<div className="api-posts-block-card-content">
					<h3 className="api-posts-block-card-title">
						<a href={ url } target="_blank" rel="noopener noreferrer">
							{ title }
						</a>
					</h3>

					{ description && (
						<p className="api-posts-block-card-description">
							{ description }
						</p>
					) }

					<div className="api-posts-block-card-meta">
						{ publishedDate && (
							<span className="api-posts-block-card-date">
								{ publishedDate }
							</span>
						) }

						{ authorName && (
							<span className="api-posts-block-card-author">
								by { authorName }
							</span>
						) }

						{ showReadingTime && readingTime > 0 && (
							<span className="api-posts-block-card-reading-time">
								{ readingTime } min read
							</span>
						) }

						{ showReactions && reactionsCount > 0 && (
							<span className="api-posts-block-card-reactions">
								❤️ { reactionsCount }
							</span>
						) }
					</div>
				</div>
			</article>
		);
	};

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Layout Settings', 'api-posts-block' ) }
					initialOpen={ true }
				>
					<RangeControl
						label={ __( 'Number of Columns', 'api-posts-block' ) }
						value={ columns }
						onChange={ ( value ) =>
							setAttributes( { columns: value } )
						}
						min={ 2 }
						max={ 3 }
						help={ __(
							'Select 2 or 3 columns for the grid layout',
							'api-posts-block'
						) }
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'Display Options', 'api-posts-block' ) }
					initialOpen={ true }
				>
					<ToggleControl
						label={ __( 'Show Cover Image', 'api-posts-block' ) }
						checked={ showImage }
						onChange={ ( value ) =>
							setAttributes( { showImage: value } )
						}
						help={ __(
							'Display cover images for articles',
							'api-posts-block'
						) }
					/>

					<ToggleControl
						label={ __(
							'Show Reading Time',
							'api-posts-block'
						) }
						checked={ showReadingTime }
						onChange={ ( value ) =>
							setAttributes( { showReadingTime: value } )
						}
						help={ __(
							'Display estimated reading time',
							'api-posts-block'
						) }
					/>

					<ToggleControl
						label={ __( 'Show Reactions Count', 'api-posts-block' ) }
						checked={ showReactions }
						onChange={ ( value ) =>
							setAttributes( { showReactions: value } )
						}
						help={ __(
							'Display number of reactions on each article',
							'api-posts-block'
						) }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ loading && (
					<div className="api-posts-block-loading">
						<Spinner />
						<p>
							{ __(
								'Loading articles...',
								'api-posts-block'
							) }
						</p>
					</div>
				) }

				{ error && ! loading && (
					<Notice status="error" isDismissible={ false }>
						{ error }
					</Notice>
				) }

				{ ! loading && ! error && articles.length > 0 && (
					<div className="api-posts-block-grid">
						{ articles.map( ( article ) =>
							renderCard( article )
						) }
					</div>
				) }

				{ ! loading && ! error && articles.length === 0 && (
					<Notice status="warning" isDismissible={ false }>
						{ __( 'No articles found', 'api-posts-block' ) }
					</Notice>
				) }
			</div>
		</>
	);
};

export default APIPostsBlock;
