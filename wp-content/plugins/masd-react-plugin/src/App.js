// src/App.js
import React, { useState } from 'react';
import NotificationTest from './NotificationTest';
import Modal from './Modal';
import { requestNotificationPermission, subscribeUserToPush } from './index';

function App() {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [permissionError, setPermissionError] = useState(null);

  const handleOpenModal = () => {
    console.log('Opening modal for notification permission');
    setIsModalOpen(true);
  };

  const handleCloseModal = () => {
    console.log('Closing modal');
    setIsModalOpen(false);
    setPermissionError(null);
  };

  const handleConfirm = () => {
    console.log('User confirmed notification permission');
    setIsModalOpen(false);
    requestNotificationPermission().then((permission) => {
      console.log('Notification permission granted:', permission);
      subscribeUserToPush();
    }).catch((error) => {
      console.error('Failed to get notification permission:', error);
      setPermissionError('Failed to get notification permission. Please allow notifications in your browser settings.');
    });
  };

  return (
    <div className="App">
      <header className="App-header">
        <h1>Welcome to Our Website</h1>
        <button onClick={handleOpenModal}>Enable Notifications</button>
        {permissionError && <p className="error">{permissionError}</p>}
        {isModalOpen && (
          <Modal
            message="We'd like to send you notifications for important updates. Do you allow?"
            onConfirm={handleConfirm}
            onCancel={handleCloseModal}
          />
        )}
        <NotificationTest />
      </header>
    </div>
  );
}

export default App;
