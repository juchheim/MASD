const fetch = require('node-fetch');

const payload = JSON.stringify({
  title: 'Test Push Notification',
  body: 'This is a test message.',
  icon: 'path/to/icon.png',
  url: 'https://yourdomain.org'
});

fetch('http://localhost:5001/send-notification', {
  method: 'POST',
  body: JSON.stringify({
    title: 'Test Push Notification',
    body: 'This is a test message.',
    icon: 'path/to/icon.png',
    url: 'https://yourdomain.org'
  }),
  headers: {
    'Content-Type': 'application/json'
  }
}).then(response => {
  return response.json();
}).then(data => {
  console.log('Notification sent:', data);
}).catch(error => {
  console.error('Error sending notification:', error);
});
