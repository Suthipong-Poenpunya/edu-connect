// Firebase Configuration - ค่ากำหนด Firebase
const firebaseConfig = {
    apiKey: "AIzaSyDnZ-sh1RhmND1_N_drLOi9uL4KUdFpB0Q",
    authDomain: "edu-connect-a8315.firebaseapp.com",
    projectId: "edu-connect-a8315",
    storageBucket: "edu-connect-a8315.firebasestorage.app",
    messagingSenderId: "33042525923",
    appId: "1:33042525923:web:be51fde4766b06fd3a9fd6",
    measurementId: "G-QGG90GGG3Y"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Export Firebase services
const auth = firebase.auth();
const db = firebase.firestore();

// Enable offline persistence (optional)
db.enablePersistence()
    .catch((err) => {
        if (err.code == 'failed-precondition') {
            console.log('Multiple tabs open, persistence disabled');
        } else if (err.code == 'unimplemented') {
            console.log('Browser does not support persistence');
        }
    });

console.log("Firebase initialized successfully");
