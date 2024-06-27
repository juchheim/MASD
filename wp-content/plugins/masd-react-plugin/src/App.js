/* global OneSignal */
import React, { useState } from 'react';
import Modal from './Modal'; // Ensure Modal component is correctly imported
import './index.css'; // Ensure you import the CSS file if modal styles are in index.css

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

  const handleConfirm = async () => {
    console.log('User confirmed notification permission');
    setIsModalOpen(false);
    try {
      window.OneSignal.push(function() {
        OneSignal.showSlidedownPrompt();
      });
    } catch (error) {
      console.error('Failed to get notification permission or subscribe user:', error);
      setPermissionError('Failed to get notification permission or subscribe user. Please allow notifications in your browser settings.');
    }
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
      </header>
    </div>
  );
}

export default App;
