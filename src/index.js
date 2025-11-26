const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;
const { useState, useEffect, createElement: el } = wp.element;
const { InspectorControls, useBlockProps } = wp.blockEditor;
const { PanelBody, RangeControl, ToggleControl, Spinner, Notice } = wp.components;

const APIPostsBlock = ( { attributes, setAttributes } ) => {
	const { columns, showImage, showReadingTime } = attributes;
	const [ articles, setArticles ] = useState( [] );
	const [ loading, setLoading ] = useState( true );
	const [ error, setError ] = useState( null );

	const blockProps = useBlockProps( {
		className: `api-posts-block columns-${ columns }`,
	} );

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
		} = article;

		const publishedDate = publishedAt
			? new Date( publishedAt ).toLocaleDateString( 'en-US', {
					year: 'numeric',
					month: 'long',
					day: 'numeric',
			  } )
			: '';

		return el(
			'article',
			{ key: article.id, className: 'api-posts-block-card' },
			showImage && coverImage
				? el(
						'div',
						{ className: 'api-posts-block-card-image' },
						el( 'img', { src: coverImage, alt: title } )
				  )
				: null,
			el(
				'div',
				{ className: 'api-posts-block-card-content' },
				el(
					'h3',
					{ className: 'api-posts-block-card-title' },
					el( 'a', { href: url, target: '_blank', rel: 'noopener noreferrer' }, title )
				),
				description
					? el( 'p', { className: 'api-posts-block-card-description' }, description )
					: null,
				el(
					'div',
					{ className: 'api-posts-block-card-meta' },
					publishedDate
						? el( 'span', { className: 'api-posts-block-card-date' }, publishedDate )
						: null,
					showReadingTime && readingTime > 0
						? el(
								'span',
								{ className: 'api-posts-block-card-reading-time' },
								`${ readingTime } min read`
						  )
						: null
				)
			)
		);
	};

	return el(
		el.Fragment,
		null,
		el(
			InspectorControls,
			null,
			el(
				PanelBody,
				{ title: __( 'Layout Settings', 'api-posts-block' ), initialOpen: true },
				el( RangeControl, {
					label: __( 'Number of Columns', 'api-posts-block' ),
					value: columns,
					onChange: ( value ) => setAttributes( { columns: value } ),
					min: 2,
					max: 3,
					help: __( 'Select 2 or 3 columns for the grid layout', 'api-posts-block' ),
				} )
			),
			el(
				PanelBody,
				{ title: __( 'Display Options', 'api-posts-block' ), initialOpen: true },
				el( ToggleControl, {
					label: __( 'Show Cover Image', 'api-posts-block' ),
					checked: showImage,
					onChange: ( value ) => setAttributes( { showImage: value } ),
					help: __( 'Display cover images for articles', 'api-posts-block' ),
				} ),
				el( ToggleControl, {
					label: __( 'Show Reading Time', 'api-posts-block' ),
					checked: showReadingTime,
					onChange: ( value ) => setAttributes( { showReadingTime: value } ),
					help: __( 'Display estimated reading time', 'api-posts-block' ),
				} )
			)
		),
		el(
			'div',
			blockProps,
			loading
				? el(
						'div',
						{ className: 'api-posts-block-loading' },
						el( Spinner, null ),
						el( 'p', null, __( 'Loading articles...', 'api-posts-block' ) )
				  )
				: null,
			error && ! loading
				? el( Notice, { status: 'error', isDismissible: false }, error )
				: null,
			! loading && ! error && articles.length > 0
				? el(
						'div',
						{ className: 'api-posts-block-grid' },
						articles.map( ( article ) => renderCard( article ) )
				  )
				: null,
			! loading && ! error && articles.length === 0
				? el( Notice, { status: 'warning', isDismissible: false }, __( 'No articles found', 'api-posts-block' ) )
				: null
		)
	);
};

registerBlockType( 'api-posts-block/posts', {
	title: __( 'API Posts Block', 'api-posts-block' ),
	description: __( 'Display articles from Dev.to API in a card layout', 'api-posts-block' ),
	category: 'widgets',
	icon: 'welcome-learn-more',
	keywords: [
		__( 'posts', 'api-posts-block' ),
		__( 'articles', 'api-posts-block' ),
		__( 'api', 'api-posts-block' ),
	],
	attributes: {
		columns: {
			type: 'number',
			default: 3,
		},
		showImage: {
			type: 'boolean',
			default: true,
		},
		showReadingTime: {
			type: 'boolean',
			default: true,
		},
	},
	edit: APIPostsBlock,
	save: () => null,
} );
