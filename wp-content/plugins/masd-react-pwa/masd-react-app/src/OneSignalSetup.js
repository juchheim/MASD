import OneSignal from 'react-onesignal';

export const initializeOneSignal = async () => {
  await OneSignal.init({
    appId: '451958ec-b4c5-4974-bb7b-94b4113da643', // Replace with your actual OneSignal App ID
    notifyButton: {
      enable: true,
    },
    allowLocalhostAsSecureOrigin: true // Useful for local development
  });

  OneSignal.showSlidedownPrompt(); // Automatically prompts users to subscribe
};
