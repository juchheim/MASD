const API_KEY = 'AIzaSyC6fx9c_ePhFy3DHDeOFB-5iSFUtjBwtwk'; // Replace with your actual API key
const CALENDAR_ID = 'c_976739d677b4da8761df4c1311e26da023be0790bfe4fa41c151f1d2d5a29c88@group.calendar.google.com'; // Replace with your calendar ID

const getEvents = async () => {
  const response = await fetch(
    `https://www.googleapis.com/calendar/v3/calendars/${CALENDAR_ID}/events?key=${API_KEY}`
  );
  const data = await response.json();
  return data.items || [];
};

export default getEvents;
