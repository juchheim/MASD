import React, { useEffect, useState } from 'react';
import axios from 'axios';

function Calendar({ calendarId, apiKey }) {
  const [events, setEvents] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    axios
      .get(
        `https://www.googleapis.com/calendar/v3/calendars/${calendarId}/events?key=${apiKey}`
      )
      .then((response) => {
        setEvents(response.data.items);
        setLoading(false);
      })
      .catch((error) => {
        console.error('Error fetching calendar events:', error);
        setLoading(false);
      });
  }, [calendarId, apiKey]);

  if (loading) {
    return <p>Loading events...</p>;
  }

  return (
    <div>
      <h1>Upcoming Events</h1>
      <ul>
        {events.map((event) => (
          <li key={event.id}>
            <h3>{event.summary}</h3>
            <p>{new Date(event.start.dateTime || event.start.date).toLocaleString()}</p>
          </li>
        ))}
      </ul>
    </div>
  );
}

export default Calendar;
