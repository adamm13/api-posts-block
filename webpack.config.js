const path = require('path');

// Minimal webpack config — wp-scripts usually provides a default configuration.
// This file exports a JS object to avoid JSON parse errors when webpack-cli requires it.
module.exports = {
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, 'build'),
  },
  module: {
    rules: [
      {
        test: /\\.scss$/,
        use: ['sass-loader'],
      },
    ],
  },
};
