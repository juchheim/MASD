// src/NotificationTest.js
import React, { useState } from 'react';

const NotificationTest = () => {
  const [permission, setPermission] = useState(null);

  const handleRequestPermission = () => {
    Notification.requestPermission().then(result => {
      console.log('Notification permission result:', result);
      setPermission(result);
    });
  };

  return (
    <div>
      <h1>Notification Test</h1>
      <button onClick={handleRequestPermission}>Request Notification Permission</button>
      {permission && <p>Permission: {permission}</p>}
    </div>
  );
};

export default NotificationTest;
