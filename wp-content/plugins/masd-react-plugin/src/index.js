/* global OneSignal */
import React from 'react';
import { createRoot } from 'react-dom/client';
import './index.css';
import App from './App';
import reportWebVitals from './reportWebVitals';

const container = document.getElementById('root');
const root = createRoot(container);

root.render(
  <React.StrictMode>
    <App />
  </React.StrictMode>
);

// Initialize OneSignal
window.OneSignal = window.OneSignal || [];
OneSignal.push(function() {
  OneSignal.init({
    appId: "451958ec-b4c5-4974-bb7b-94b4113da643",
    notifyButton: {
      enable: true, // Optional
    },
    path: '/',
    serviceWorkerPath: 'OneSignalSDKWorker.js',
    serviceWorkerUpdaterPath: 'OneSignalSDKUpdaterWorker.js'
  });
});

// Request notification permission
OneSignal.push(function() {
  OneSignal.isPushNotificationsEnabled(function(isEnabled) {
    if (!isEnabled) {
      OneSignal.push(function() {
        OneSignal.showSlidedownPrompt();
      });
    }
  });
});

// Function to handle subscription
async function subscribeUser() {
  if ('serviceWorker' in navigator && 'PushManager' in window) {
    try {
      const registration = await navigator.serviceWorker.ready;
      const subscription = await registration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array('BHIgWK4l09nhi0kuAc6h6Yiya9bztKVtW_nykFwWiuW4H4lVx571TaQMurUAB1-UZrUQmIWRPtExiTPise838Bo')
      });

      // Send subscription to your server
      await fetch('/save-subscription', {
        method: 'POST',
        body: JSON.stringify(subscription),
        headers: {
          'Content-Type': 'application/json'
        }
      });
      console.log('User is subscribed:', subscription);
    } catch (error) {
      console.error('Failed to subscribe the user:', error);
    }
  } else {
    console.error('Service Worker or PushManager not supported');
  }
}

function urlBase64ToUint8Array(base64String) {
  const padding = '='.repeat((4 - base64String.length % 4) % 4);
  const base64 = (base64String + padding)
    .replace(/-/g, '+')
    .replace(/_/g, '/');
  const rawData = window.atob(base64);
  const outputArray = new Uint8Array(rawData.length);
  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }
  return outputArray;
}

subscribeUser();

reportWebVitals();
