const webPush = require('web-push');

// Replace these with your VAPID keys
const publicVapidKey = 'BHv1cdAdWvew9bbplgF0Eff8Tinb8YVmUwzUXhqF1sefRHnVg5sPR3fLztuH0bELDmrQlha1RTx_XtewzIe0Y9Q';
const privateVapidKey = 'etuYx6akPhBKbIWpZ51TLn6dxSZp9A8oc0OtS_5-1Wg';

webPush.setVapidDetails('mailto:ejuchheim@masd.k12.ms.us', publicVapidKey, privateVapidKey);

// Replace with the subscription object you received from the client
const subscription = {
  endpoint: 'https://fcm.googleapis.com/fcm/send/abcdefg...', // Replace with your actual endpoint
  keys: {
    auth: 'xSr-Jl5...', // Replace with your actual auth key
    p256dh: 'BOPu6Q99lK...' // Replace with your actual p256dh key
  }
};

const payload = JSON.stringify({
  title: 'Test Push Notification',
  body: 'This is a test message.',
  icon: 'path/to/icon.png',
  url: 'https://yourdomain.org'
});

webPush.sendNotification(subscription, payload).catch(error => {
  console.error(error);
});
