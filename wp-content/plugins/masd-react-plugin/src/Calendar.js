import React, { useEffect, useState } from 'react';
import './Calendar.css';

const Calendar = () => {
  const [events, setEvents] = useState([]);
  const [selectedEvent, setSelectedEvent] = useState(null);
  const [currentDate, setCurrentDate] = useState(new Date());
  const [error, setError] = useState(null);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const fetchEvents = async () => {
      setLoading(true);
      const calendarId = 'c_976739d677b4da8761df4c1311e26da023be0790bfe4fa41c151f1d2d5a29c88@group.calendar.google.com';
      const apiKey = 'AIzaSyC6fx9c_ePhFy3DHDeOFB-5iSFUtjBwtwk';

      const startOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1).toISOString();
      const endOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0).toISOString();

      try {
        const response = await fetch(
          `https://www.googleapis.com/calendar/v3/calendars/${calendarId}/events?key=${apiKey}&timeMin=${startOfMonth}&timeMax=${endOfMonth}`
        );

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Fetched events:', data.items);
        setEvents(data.items);
      } catch (error) {
        console.error('Error fetching calendar events:', error);
        setError(error);
      } finally {
        setLoading(false);
      }
    };

    fetchEvents();
  }, [currentDate]);

  const handleEventClick = (event) => {
    setSelectedEvent(event);
  };

  const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

  const renderDaysOfWeek = () => {
    return daysOfWeek.map((day) => (
      <div key={day} className="calendar-day-header">
        {day}
      </div>
    ));
  };

  const renderDaysInMonth = () => {
    const startDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1).getDay();
    const daysInMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0).getDate();
    const days = [];

    // Add empty cells at the start of the month
    for (let i = 0; i < startDay; i++) {
      days.push(<div key={`empty-start-${i}`} className="calendar-day empty"></div>);
    }

    // Add days with events
    for (let day = 1; day <= daysInMonth; day++) {
      const dateString = `${currentDate.getFullYear()}-${(currentDate.getMonth() + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
      const dayEvents = events.filter((event) => event.start.date === dateString || event.start.dateTime?.startsWith(dateString));
      days.push(
        <div key={day} className="calendar-day">
          <div className="date">{day}</div>
          {dayEvents.map((event) => (
            <div key={event.id} className="calendar-event" onClick={() => handleEventClick(event)}>
              <div className="event-summary">{event.summary}</div>
              {selectedEvent && selectedEvent.id === event.id && (
                <div className="event-details">
                  <p>{event.description}</p>
                  <p>{new Date(event.start.dateTime || event.start.date).toLocaleString()}</p>
                </div>
              )}
            </div>
          ))}
        </div>
      );
    }

    // Add empty cells at the end of the month to ensure full width
    const totalCells = startDay + daysInMonth;
    const emptyCells = (7 - (totalCells % 7)) % 7; // Ensure it doesn't add 7 empty cells when full row is already completed
    for (let i = 0; i < emptyCells; i++) {
      days.push(<div key={`empty-end-${i}`} className="calendar-day empty"></div>);
    }

    return days;
  };

  const handlePreviousMonth = () => {
    setCurrentDate(new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, 1));
  };

  const handleNextMonth = () => {
    setCurrentDate(new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1));
  };

  if (error) {
    return <div>Error: {error.message}</div>;
  }

  return (
    <div className="calendar-wrapper">
      <div className="calendar-header">
        <button onClick={handlePreviousMonth}>&lt;</button>
        <span className="calendar-month">
          {currentDate.toLocaleString('default', { month: 'long', year: 'numeric' })}
        </span>
        <button onClick={handleNextMonth}>&gt;</button>
      </div>
      {loading ? (
        <div className="loading">Loading events...</div>
      ) : (
        <div className="calendar-grid">
          {renderDaysOfWeek()}
          {renderDaysInMonth()}
        </div>
      )}
    </div>
  );
};

export default Calendar;
