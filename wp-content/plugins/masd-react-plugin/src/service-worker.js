/* eslint-disable no-restricted-globals */

import { precacheAndRoute } from 'workbox-precaching';

// The self.__WB_MANIFEST will be replaced by an array of assets at build time
precacheAndRoute(self.__WB_MANIFEST);

// Other event listeners (optional)
self.addEventListener('install', (event) => {
  console.log('Service worker installing...');
  // Add a call to skipWaiting here if you want to trigger
  // the update immediately on the next load
});

self.addEventListener('activate', (event) => {
  console.log('Service worker activating...');
});

self.addEventListener('fetch', (event) => {
  console.log('Fetching:', event.request.url);
  // You can add custom fetch handling here if needed
});

/* eslint-enable no-restricted-globals */
