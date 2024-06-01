const express = require('express');
const bodyParser = require('body-parser');
const webPush = require('web-push');

const app = express();

app.use(bodyParser.json());

// Replace with your VAPID keys
const publicVapidKey = 'BI1d_JBeZYk_pFwdfElVYIhTj779_0kR50d0SP03S-PiM6apT68b50Uwbj2SqXvPcuqoM5_8q4DRPDC9R-atS1s';
const privateVapidKey = '9H7M1hF277RdXQBpDSBEk9Gue9WmwUzEE9USl3MKwp0';

webPush.setVapidDetails('mailto:ejuchheim@masd.k12.ms.us', publicVapidKey, privateVapidKey);

// Store subscriptions in memory (for simplicity)
const subscriptions = [];

app.post('/save-subscription', (req, res) => {
  const subscription = req.body;
  console.log('Subscription received:', subscription); // Debug log
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

const PORT = process.env.PORT || 5001;
app.listen(PORT, () => {
  console.log(`Server started on port ${PORT}`);
});
