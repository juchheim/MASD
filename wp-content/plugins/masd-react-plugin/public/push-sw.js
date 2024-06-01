// public/push-sw.js

self.addEventListener('push', function(event) {
    const data = event.data.json();
    const title = data.title;
    const options = {
      body: data.body,
      icon: data.icon,
    };
  
    event.waitUntil(
      self.registration.showNotification(title, options)
    );
  });
  
  self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
      clients.openWindow(event.notification.data.url)
    );
  });
  
  self.addEventListener('install', function(event) {
    console.log('Service Worker installing.');
    self.skipWaiting();
  });
  
  self.addEventListener('activate', function(event) {
    console.log('Service Worker activating.');
  });
  