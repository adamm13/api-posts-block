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
# API Posts Block

A small Gutenberg block that pulls the latest articles from Dev.to and shows them as clean, simple cards.

This plugin is meant to be easy to install and use — no build tools are required on the server because the compiled files are included in `build/`.

What it does
- Fetches up to 10 articles from Dev.to `https://dev.to/api/articles`
- Displays title, excerpt, published date, and optional cover image and reading time
- Lets you pick a 2- or 3-column layout and toggle images/reading time
- Server-rendered for better SEO and caching (results cached for 1 hour)

Quick install
1. Copy the `api-posts-block` folder into your WordPress `wp-content/plugins/` directory.
2. Activate the plugin in the WordPress admin.
3. Add the block to a post or page by searching for "API Posts Block" in the block inserter.

Notes for power users
- If you want to rebuild the assets, run `npm install` and `npm run build` locally (Node.js required). The repo includes `package.json` and source files.

Troubleshooting
- If the block won't preview in the editor, open your browser console and check for JavaScript errors.
- If articles don't appear, make sure your server can reach `https://dev.to` and that there are no blocking firewalls.

Structure you should care about
- `api-posts-block.php` — plugin bootstrap and block registration
- `includes/render-block.php` — server render + API fetch + caching
- `build/` — compiled JS/CSS used by WordPress (no build step required to run)

If you'd like, I can help you publish this to GitHub, add a settings page, or add pagination/load-more. What would you like next?
```
