// src/index.js
import React from 'react';
import { createRoot } from 'react-dom/client';
import './index.css';
import App from './App';
import * as serviceWorkerRegistration from './serviceWorkerRegistration';
import reportWebVitals from './reportWebVitals';

const container = document.getElementById('root');
const root = createRoot(container);

root.render(
  <React.StrictMode>
    <App />
  </React.StrictMode>
);

// Register the service worker
serviceWorkerRegistration.register();

console.log('Checking service worker registration...');

navigator.serviceWorker.ready.then((registration) => {
  console.log('Service Worker registered:', registration);
}).catch((error) => {
  console.error('Service Worker registration failed:', error);
});

export function requestNotificationPermission() {
  return new Promise((resolve, reject) => {
    Notification.requestPermission().then((result) => {
      console.log('Notification permission result:', result);
      if (result === 'granted') {
        resolve(result);
      } else {
        reject(new Error('Permission not granted.'));
      }
    });
  });
}

export function subscribeUserToPush() {
  if ('serviceWorker' in navigator && 'PushManager' in window) {
    navigator.serviceWorker.ready.then((registration) => {
      console.log('Service Worker ready:', registration);
      const vapidPublicKey = 'BHv1cdAdWvew9bbplgF0Eff8Tinb8YVmUwzUXhqF1sefRHnVg5sPR3fLztuH0bELDmrQlha1RTx_XtewzIe0Y9Q'; // Replace with your Public VAPID Key
      const convertedVapidKey = urlBase64ToUint8Array(vapidPublicKey);

      registration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: convertedVapidKey
      }).then((subscription) => {
        console.log('User is subscribed:', subscription);
        saveSubscription(subscription);
      }).catch((error) => {
        console.error('Failed to subscribe the user:', error);
      });
    }).catch((error) => {
      console.error('Service Worker registration failed:', error);
    });
  } else {
    console.error('Service Worker or PushManager not supported');
  }
}

function urlBase64ToUint8Array(base64String) {
  const padding = '='.repeat((4 - base64String.length % 4) % 4);
  const base64 = (base64String + padding)
    .replace(/\-/g, '+')
    .replace(/_/g, '/');

  const rawData = window.atob(base64);
  const outputArray = new Uint8Array(rawData.length);

  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }
  return outputArray;
}

function saveSubscription(subscription) {
  return fetch('http://localhost:5001/save-subscription', {
    method: 'POST',
    body: JSON.stringify(subscription),
    headers: {
      'Content-Type': 'application/json'
    }
  }).then(response => response.json())
    .then(data => console.log('Subscription saved:', data))
    .catch(error => console.error('Error saving subscription:', error));
}

reportWebVitals();
