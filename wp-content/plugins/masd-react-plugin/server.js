const express = require('express');
const bodyParser = require('body-parser');
const webPush = require('web-push');
const path = require('path');

const app = express();

app.use(bodyParser.json());

// Replace with your VAPID keys
const publicVapidKey = 'BHIgWK4l09nhi0kuAc6h6Yiya9bztKVtW_nykFwWiuW4H4lVx571TaQMurUAB1-UZrUQmIWRPtExiTPise838Bo';
const privateVapidKey = 'GVo1KNRra-6l-30GBUx14-aD0FlruJtrlFgzT6R7cGA';

webPush.setVapidDetails('mailto:ejuchheim@masd.k12.ms.us', publicVapidKey, privateVapidKey);

// Serve static files from the React app
app.use(express.static(path.join(__dirname, 'build')));

// Store subscriptions in memory (for simplicity)
const subscriptions = [];

app.post('/save-subscription', (req, res) => {
  const subscription = req.body;
  console.log('Subscription received:', subscription);
  subscriptions.push(subscription);
  res.status(201).json({ message: 'Subscription saved successfully.' });
});

app.post('/send-notification', (req, res) => {
  const { title, body, icon, url } = req.body;
  const payload = JSON.stringify({ title, body, icon, url });

  const sendNotifications = subscriptions.map(subscription =>
    webPush.sendNotification(subscription, payload).catch(err => {
      console.error('Error sending notification:', err);
    })
  );

  Promise.all(sendNotifications).then(() => res.status(200).json({ message: 'Notifications sent successfully.' }));
});

app.get('*', (req, res) => {
  res.sendFile(path.join(__dirname, 'build', 'index.html'));
});

const PORT = process.env.PORT || 5001;
app.listen(PORT, () => {
  console.log(`Server started on port ${PORT}`);
});
