const WorkboxWebpackPlugin = require('workbox-webpack-plugin');

module.exports = {
  // Other existing configuration...

  plugins: [
    // Other existing plugins...

    new WorkboxWebpackPlugin.InjectManifest({
      swSrc: './src/service-worker.js',
      swDest: 'service-worker.js',
    }),
  ],
};