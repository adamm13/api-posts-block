import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import APIPostsBlock from './components/APIPostsBlock.jsx';
import './styles/block.css';

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
	save: () => null, // Uses server-side rendering
} );
