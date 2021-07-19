// Give the service worker access to Firebase Messaging.
// Note that you can only use Firebase Messaging here, other Firebase libraries
// are not available in the service worker.
importScripts('https://www.gstatic.com/firebasejs/7.7.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.7.0/firebase-messaging.js');

// Initialize the Firebase app in the service worker by passing in the
// messagingSenderId.
firebase.initializeApp({
  apiKey: "AIzaSyDf9swdY3iDjzvUbIeJLc8Dn0bBT2JKA50",
  authDomain: "soft-fire-13472.firebaseapp.com",
  databaseURL: "https://soft-fire-13472.firebaseio.com",
  projectId: "soft-fire-13472",
  storageBucket: "soft-fire-13472.appspot.com",
  messagingSenderId: "17944470208",
  appId: "1:17944470208:web:9d74c717c89256118c8325"
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function(payload) {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  // Customize notification here
  const notificationTitle = payload.data.title;
  const notificationOptions = {
    dir: 'rtl',
    body: payload.notification.body,
    image: payload.notification.image,
    icon: payload.notification.image
  };

  return self.registration.showNotification(notificationTitle,
    notificationOptions);
});