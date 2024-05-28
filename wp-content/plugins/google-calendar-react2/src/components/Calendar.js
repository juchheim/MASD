import React, { useEffect, useState } from 'react';
import { format, startOfMonth, endOfMonth, startOfWeek, endOfWeek, addMonths, subMonths, addDays, isSameMonth, isSameDay } from 'date-fns';
import getEvents from '../services/googleCalendarService';
import './Calendar.css';

const Calendar = () => {
  const [events, setEvents] = useState([]);
  const [currentMonth, setCurrentMonth] = useState(new Date());
  const [expandedEventId, setExpandedEventId] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchEvents = async () => {
      try {
        const events = await getEvents();
        setEvents(events);
      } catch (error) {
        console.error('Error fetching events:', error);
      }
    };

    const initialSetup = async () => {
      await fetchEvents();
      setLoading(false);
    };

    initialSetup();
  }, []);

  const handleEventClick = (eventId) => {
    setExpandedEventId(expandedEventId === eventId ? null : eventId);
  };

  const renderHeader = () => (
    <div className="header row flex-middle">
      <div className="col col-start">
        <div className="icon" onClick={() => setCurrentMonth(subMonths(currentMonth, 1))}>&#9664;</div>
      </div>
      <div className="col col-center">
        <span className="month-year">{format(currentMonth, 'MMMM yyyy')}</span>
      </div>
      <div className="col col-end" onClick={() => setCurrentMonth(addMonths(currentMonth, 1))}>
        <div className="icon">&#9654;</div>
      </div>
    </div>
  );

  const renderDays = () => {
    const days = [];
    const date = startOfWeek(new Date());

    for (let i = 0; i < 7; i++) {
      days.push(
        <div className="col col-center" key={i}>
          {format(addDays(date, i), 'EEEE')}
        </div>
      );
    }

    return <div className="days row">{days}</div>;
  };

  const renderCells = () => {
    const monthStart = startOfMonth(currentMonth);
    const monthEnd = endOfMonth(monthStart);
    const startDate = startOfWeek(monthStart);
    const endDate = endOfWeek(monthEnd);

    const rows = [];
    let days = [];
    let day = startDate;
    let formattedDate = '';

    while (day <= endDate) {
      for (let i = 0; i < 7; i++) {
        formattedDate = format(day, 'd');
        const cloneDay = day;
        days.push(
          <div
            className={`col cell ${!isSameMonth(day, monthStart)
              ? 'disabled' : isSameDay(day, new Date()) ? 'selected' : ''}`}
            key={day}
            onClick={() => handleEventClick(cloneDay)}
          >
            <span className="number">{formattedDate}</span>
            <div className="events">
              {events.filter(event => isSameDay(new Date(event.start.dateTime || event.start.date), cloneDay)).map(event => (
                <div
                  key={event.id}
                  className={`event ${expandedEventId === event.id ? 'expanded' : ''}`}
                  onClick={() => handleEventClick(event.id)}
                >
                  <div className="event-summary">{event.summary}</div>
                  {expandedEventId === event.id && (
                    <div className="event-details">
                      <div>{new Date(event.start.dateTime || event.start.date).toLocaleString()}</div>
                      {event.description && <div>{event.description}</div>}
                    </div>
                  )}
                </div>
              ))}
            </div>
          </div>
        );
        day = addDays(day, 1);
      }
      rows.push(
        <div className="row" key={day}>{days}</div>
      );
      days = [];
    }

    return <div className="body">{rows}</div>;
  };

  return (
    <div className="calendar">
      {loading && <div className="loading-bar"></div>}
      {!loading && (
        <>
          {renderHeader()}
          {renderDays()}
          {renderCells()}
        </>
      )}
    </div>
  );
};

export default Calendar;
