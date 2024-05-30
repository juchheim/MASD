import React, { useEffect } from 'react';
import { BrowserRouter as Router, Route, Routes, Link } from 'react-router-dom';
import './App.css';
import Home from './Home';
import About from './About';
import Calendar from './Calendar';
import { initializeOneSignal } from './OneSignalSetup'; // Ensure this path is correct

function App() {
  useEffect(() => {
    initializeOneSignal();
  }, []);

  const calendarId = 'c_976739d677b4da8761df4c1311e26da023be0790bfe4fa41c151f1d2d5a29c88@group.calendar.google.com';
  const apiKey = 'AIzaSyC6fx9c_ePhFy3DHDeOFB-5iSFUtjBwtwk';

  return (
    <Router>
      <div className="App">
        <nav>
          <ul>
            <li>
              <Link to="/">Home</Link>
            </li>
            <li>
              <Link to="/about">About</Link>
            </li>
            <li>
              <Link to="/calendar">Calendar</Link>
            </li>
          </ul>
        </nav>
        <Routes>
          <Route path="/about" element={<About />} />
          <Route path="/calendar" element={<Calendar calendarId={calendarId} apiKey={apiKey} />} />
          <Route path="/" element={<Home />} />
        </Routes>
      </div>
    </Router>
  );
}

export default App;
