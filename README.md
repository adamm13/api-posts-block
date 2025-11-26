# API Posts Block - WordPress Gutenberg Plugin

A professional WordPress Gutenberg block plugin that fetches and displays articles from the Dev.to API in a beautifully styled responsive card layout.

## Features

- 📚 **Dev.to API Integration**: Automatically fetches up to 10 latest articles from Dev.to
- 🎨 **Responsive Grid Layout**: Choose between 2 or 3 column layouts with mobile responsiveness
- 🎯 **Customizable Display Options**: Toggle cover images, reading time, and reaction counts
- ⚡ **Server-Side Rendering**: PHP server rendering for better performance and SEO
- 🔄 **API Caching**: Results cached for 1 hour to reduce API calls
- 📱 **Mobile Optimized**: Fully responsive design that works on all devices
- ✨ **Modern UI**: Clean, minimal card design with smooth interactions
- 🎛️ **Gutenberg Inspector Controls**: Sidebar settings for easy customization

## Quick Start

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/api-posts-block.git
   cd api-posts-block
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

3. Build the plugin:
   ```bash
   npm run build
   ```

4. Copy the plugin to WordPress plugins directory:
   ```bash
   # On Mac/Linux
   cp -r api-posts-block ~/path/to/wordpress/wp-content/plugins/

   # On Windows (PowerShell)
   Copy-Item -Path "api-posts-block" -Destination "C:\path\to\wordpress\wp-content\plugins\" -Recurse
   ```

5. Activate the plugin:
   - Go to WordPress admin dashboard
   - Navigate to Plugins
   - Find "API Posts Block" and click Activate

6. Add the block to a page or post:
   - Create/edit a page or post
   - Click the "+" button to add a block
   - Search for "API Posts Block"
   - Configure settings using the inspector panel on the right

## Development

### Running in Development Mode

```bash
npm run dev
```

This will start the development server with file watching and automatic recompilation.

### Building for Production

```bash
npm run build
```

This generates optimized, minified JavaScript and CSS files in the `build/` folder.

### Linting

Check JavaScript code quality:
```bash
npm run lint:js
```

Check styling issues:
```bash
npm run lint:style
```

### Code Formatting

Format code according to WordPress standards:
```bash
npm run format
```

## Block Settings (Inspector Controls)

### Layout Settings
- **Number of Columns**: Choose between 2 or 3 columns for the grid layout

### Display Options
- **Show Cover Image**: Toggle to display/hide article cover images
- **Show Reading Time**: Toggle to display/hide estimated reading time
- **Show Reactions Count**: Toggle to display/hide the number of reactions

## Project Structure

```
api-posts-block/
├── src/
│   ├── index.js                 # Block registration and main entry
│   ├── components/
│   │   └── APIPostsBlock.jsx    # React component with API fetching
│   └── styles/
│       ├── block.scss           # Main block styles
│       └── editor.scss          # Editor-specific styles
├── includes/
│   └── render-block.php         # Server-side rendering callback
├── build/                       # Compiled output (generated)
│   ├── index.js
│   ├── style.css
│   └── editor.css
├── api-posts-block.php          # Main plugin file
├── package.json                 # Dependencies and scripts
├── webpack.config.js            # Webpack configuration
└── README.md                    # This file
```

## Technical Details

### Frontend (React Component)

The block uses React with WordPress components to:
- Fetch articles asynchronously from Dev.to API
- Display a responsive card grid
- Handle loading and error states
- Provide inspector controls for customization
- Support server-side rendering

### Backend (PHP)

The plugin includes:
- Block registration with proper attributes
- Server-side rendering callback function
- API fetching with error handling
- Data caching using WordPress transients (1 hour TTL)
- Security functions: `sanitize_text_field()`, `wp_kses_post()`, `esc_url()`, etc.

### API Integration

- **Endpoint**: `https://dev.to/api/articles`
- **Parameters**: `per_page=10&sort=-published_at`
- **Cache Duration**: 1 hour (adjustable in `render-block.php`)
- **Error Handling**: Graceful fallback with user-friendly error messages

### Styling

The block uses SCSS for:
- CSS variables for theming
- Responsive mobile-first design
- CSS Grid for flexible layouts
- Smooth animations and transitions
- Professional card styling with hover effects

## Assumptions & Decisions

1. **Server-Side Rendering**: Used SSR (PHP callback) to ensure content loads immediately and is SEO-friendly, while React is used for editor-only functionality.

2. **Caching Strategy**: Articles are cached for 1 hour using WordPress transients. This reduces API calls and improves performance.

3. **Responsive Design**: Mobile-first approach ensures excellent experience on all devices. On mobile, the grid collapses to a single column.

4. **No Image Display in Frontend**: While images are fetched and available, the default is to show them but users can toggle this off. The focus is on text content.

5. **Column Flexibility**: Users can choose 2 or 3 columns. The grid automatically adjusts on smaller screens.

6. **Additional Display Options**: Besides the required elements (title, description, date), the block includes:
   - Cover image (optional)
   - Reading time (optional)
   - Author name (default shown)
   - Reaction count (optional)

7. **Error Handling**: The block gracefully handles API errors and displays user-friendly messages rather than breaking the page.

8. **WordPress Standards**: Used WordPress hooks, sanitization functions, and Gutenberg patterns throughout for maximum compatibility and security.

## Browser Support

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Limitations & Future Enhancements

- **No Pagination**: Currently fetches 10 articles. Could add pagination in future updates.
- **Limited Caching**: Uses WordPress transients. Could implement more advanced caching strategies.
- **API Only**: Currently hardcoded to Dev.to API. Could add settings to fetch from different sources.

## Troubleshooting

### Block not appearing after activation
- Ensure you've run `npm run build` to generate the build files
- Clear your browser cache
- Flush WordPress object cache if using caching plugins

### API errors
- Check internet connection
- Verify Dev.to API is accessible
- Check WordPress error logs

### Styling issues
- Clear WordPress cache
- Hard refresh browser (Ctrl+Shift+R)
- Ensure SCSS compiled correctly to CSS

## Dependencies

- **WordPress 5.0+** (for Gutenberg support)
- **@wordpress/scripts**: Build tools and CLI
- **@wordpress/blocks**: Block API
- **@wordpress/components**: UI components
- **@wordpress/element**: React library
- **@wordpress/i18n**: Internationalization
- **sass**: SCSS compiler

## License

GPL v2 or later - See LICENSE file for details

## Contributing

Contributions are welcome! Please feel free to submit pull requests or open issues for bugs and feature requests.

## Support

For issues, questions, or suggestions, please open an issue on the GitHub repository.

---

**Author**: Developer  
**Version**: 1.0.0  
**Last Updated**: November 2025
