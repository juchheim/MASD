const axios = require('axios');
const express = require('express');
const bodyParser = require('body-parser');
const cron = require('node-cron');
const moment = require('moment');
const notificationService = require('notification-service-sdk'); // Hypothetical notification service

const app = express();
const PORT = process.env.PORT || 3000;

const calendarId = 'c_976739d677b4da8761df4c1311e26da023be0790bfe4fa41c151f1d2d5a29c88@group.calendar.google.com';
const apiKey = 'AIzaSyC6fx9c_ePhFy3DHDeOFB-5iSFUtjBwtwk';

app.use(bodyParser.json());

// Function to fetch events
const fetchEvents = async () => {
  try {
    const response = await axios.get(
      `https://www.googleapis.com/calendar/v3/calendars/${calendarId}/events?key=${apiKey}`
    );
    return response.data.items;
  } catch (error) {
    console.error('Error fetching calendar events:', error);
    return [];
  }
};

// Function to check for upcoming events within 7 days
const checkUpcomingEvents = async () => {
  const events = await fetchEvents();
  const now = moment();
  const upcomingEvents = events.filter((event) => {
    const eventDate = moment(event.start.dateTime || event.start.date);
    return eventDate.diff(now, 'days') <= 7 && eventDate.diff(now, 'days') >= 0;
  });

  if (upcomingEvents.length > 0) {
    // Trigger notifications for upcoming events
    console.log('Upcoming events within 7 days:', upcomingEvents);
    upcomingEvents.forEach(event => {
      notificationService.sendNotification({
        title: 'Upcoming Event',
        message: `Don't miss the event: ${event.summary} on ${moment(event.start.dateTime || event.start.date).format('MMMM Do YYYY, h:mm a')}`,
        // Add other notification details as needed
      });
    });
  }
};

// Schedule the check to run every minute
cron.schedule('* * * * *', () => {
  console.log('Running check for upcoming events');
  checkUpcomingEvents();
});

app.listen(PORT, () => {
  console.log(`Server is running on port ${PORT}`);
});
