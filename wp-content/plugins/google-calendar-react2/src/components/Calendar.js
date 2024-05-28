import React, { useEffect, useState } from 'react';
// React is a JavaScript library for building user interfaces. We are importing useEffect and useState hooks.

import { format, startOfMonth, endOfMonth, startOfWeek, endOfWeek, addMonths, subMonths, addDays, isSameMonth, isSameDay } from 'date-fns';
// date-fns is a library for manipulating dates. We are importing various functions for date calculations.

import getEvents from '../services/googleCalendarService';
// This imports a function that fetches events from Google Calendar. This function needs to be defined in the mentioned path.

import './Calendar.css';
// Importing the CSS file for styling the calendar component.

const Calendar = () => {
  // Calendar is a functional component.

  const [events, setEvents] = useState([]);
  // State to store the list of events fetched from Google Calendar.

  const [currentMonth, setCurrentMonth] = useState(new Date());
  // State to store the currently displayed month. Initialized to the current date.

  const [expandedEventId, setExpandedEventId] = useState(null);
  // State to track which event's details are expanded. Initialized to null, meaning no event is expanded initially.

  const [loading, setLoading] = useState(true);
  // State to track if the calendar is loading. Initialized to true to show the loading bar initially.

  useEffect(() => {
    // useEffect hook is used to perform side effects in functional components. Here it is used to fetch events when the component mounts.

    const fetchEvents = async () => {
      // Function to fetch events from Google Calendar.
      try {
        const events = await getEvents();
        // Await the response from getEvents function and store it in the events variable.
        setEvents(events);
        // Update the events state with the fetched events.
      } catch (error) {
        console.error('Error fetching events:', error);
        // Log any errors that occur during the fetch.
      }
    };

    const initialSetup = async () => {
      // Function to perform initial setup, including fetching events.
      await fetchEvents();
      // Call the fetchEvents function and wait for it to complete.
      setLoading(false);
      // Set loading to false after events are fetched to stop showing the loading bar.
    };

    initialSetup();
    // Call the initialSetup function when the component mounts.
  }, []); // Empty dependency array means this effect runs only once when the component mounts.

  const handleEventClick = (eventId) => {
    // Function to handle clicking on an event. It toggles the expanded state of the clicked event.
    setExpandedEventId(expandedEventId === eventId ? null : eventId);
    // If the clicked event is already expanded, collapse it by setting expandedEventId to null, otherwise set it to the clicked event's ID.
  };

  const renderHeader = () => (
    // Function to render the calendar header, which includes the navigation arrows and the current month and year.
    <div className="header row flex-middle">
      <div className="col col-start">
        <div className="icon" onClick={() => setCurrentMonth(subMonths(currentMonth, 1))}>&#9664;</div>
        {/* Clicking this arrow navigates to the previous month by calling setCurrentMonth with subMonths. */}
      </div>
      <div className="col col-center">
        <span className="month-year">{format(currentMonth, 'MMMM yyyy')}</span>
        {/* Displays the current month and year in bold. */}
      </div>
      <div className="col col-end" onClick={() => setCurrentMonth(addMonths(currentMonth, 1))}>
        <div className="icon">&#9654;</div>
        {/* Clicking this arrow navigates to the next month by calling setCurrentMonth with addMonths. */}
      </div>
    </div>
  );

  const renderDays = () => {
    // Function to render the days of the week (e.g., Sunday, Monday).
    const days = [];
    const date = startOfWeek(new Date());
    // Get the start of the current week (Sunday).

    for (let i = 0; i < 7; i++) {
      // Loop to create elements for each day of the week.
      days.push(
        <div className="col col-center" key={i}>
          {format(addDays(date, i), 'EEEE')}
          {/* Formatting and displaying each day of the week. */}
        </div>
      );
    }

    return <div className="days row">{days}</div>;
    // Return the days as a row.
  };

  const renderCells = () => {
    // Function to render the days of the month.
    const monthStart = startOfMonth(currentMonth);
    // Get the start of the current month.
    const monthEnd = endOfMonth(monthStart);
    // Get the end of the current month.
    const startDate = startOfWeek(monthStart);
    // Get the start of the week that contains the start of the month.
    const endDate = endOfWeek(monthEnd);
    // Get the end of the week that contains the end of the month.

    const rows = [];
    // Array to hold the rows of days.
    let days = [];
    // Array to hold the days in a week.
    let day = startDate;
    // Start from the start date.
    let formattedDate = '';

    while (day <= endDate) {
      // Loop until the end date is reached.
      for (let i = 0; i < 7; i++) {
        // Loop for each day in the week.
        formattedDate = format(day, 'd');
        // Format the day number.
        const cloneDay = day;
        // Clone the day to use in the event handler.
        days.push(
          <div
            className={`col cell ${!isSameMonth(day, monthStart)
              ? 'disabled' : isSameDay(day, new Date()) ? 'selected' : ''}`}
            // Apply appropriate classes for styling: 'disabled' if not in the same month, 'selected' if it's today.
            key={day}
            onClick={() => handleEventClick(cloneDay)}
            // Add click handler to toggle the event details.
          >
            <span className="number">{formattedDate}</span>
            {/* Displaying the day number. */}
            <div className="events">
              {events.filter(event => isSameDay(new Date(event.start.dateTime || event.start.date), cloneDay)).map(event => (
                <div
                  key={event.id}
                  className={`event ${expandedEventId === event.id ? 'expanded' : ''}`}
                  // Add classes to style the event: 'expanded' if it is expanded.
                  onClick={(e) => {
                    e.stopPropagation();
                    // Prevent the event click from bubbling up to the cell click.
                    handleEventClick(event.id);
                    // Call the event click handler.
                  }}
                >
                  <div className="event-summary">{event.summary}</div>
                  {/* Displaying the event summary. */}
                  {expandedEventId === event.id && (
                    <div className="event-details">
                      {/* Conditionally render the event details if the event is expanded. */}
                      <div>{new Date(event.start.dateTime || event.start.date).toLocaleString()}</div>
                      {/* Display the event date and time. */}
                      {event.description && <div>{event.description}</div>}
                      {/* Display the event description if it exists. */}
                    </div>
                  )}
                </div>
              ))}
            </div>
          </div>
        );
        day = addDays(day, 1);
        // Move to the next day.
      }
      rows.push(
        <div className="row" key={day}>{days}</div>
        // Add the days as a row.
      );
      days = [];
      // Reset the days array for the next week.
    }

    return <div className="body">{rows}</div>;
    // Return the rows as the body of the calendar.
  };

  return (
    <div className="calendar">
      {loading && <div className="loading-bar"></div>}
      {/* Display a loading bar while events are being fetched. */}
      {!loading && (
        <>
          {renderHeader()}
          {/* Render the header. */}
          {renderDays()}
          {/* Render the days of the week. */}
          {renderCells()}
          {/* Render the cells for each day of the month. */}
        </>
      )}
    </div>
  );
};

export default Calendar;
// Export the Calendar component as the default export.
