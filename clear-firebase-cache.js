// Clear Firebase Cache and Storage
// Run this in console before adding courses

async function clearFirebaseCache() {
    console.log('🧹 Clearing Firebase cache...');
    
    try {
        // Clear all Firebase data from localStorage
        Object.keys(localStorage).forEach(key => {
            if (key.startsWith('firebase') || key.includes('firestore')) {
                localStorage.removeItem(key);
                console.log(`✅ Removed ${key} from localStorage`);
            }
        });
        
        // Clear all Firebase data from sessionStorage
        Object.keys(sessionStorage).forEach(key => {
            if (key.startsWith('firebase') || key.includes('firestore')) {
                sessionStorage.removeItem(key);
                console.log(`✅ Removed ${key} from sessionStorage`);
            }
        });
        
        // Clear IndexedDB (Firebase uses this for local cache)
        if ('indexedDB' in window) {
            const databases = await indexedDB.databases();
            for (const db of databases) {
                if (db.name.includes('firebase') || db.name.includes('firestore')) {
                    await indexedDB.deleteDatabase(db.name);
                    console.log(`✅ Deleted IndexedDB: ${db.name}`);
                }
            }
        }
        
        console.log('🎉 Firebase cache cleared successfully!');
        console.log('🔄 Please refresh the page and try again.');
        
    } catch (error) {
        console.error('❌ Error clearing cache:', error);
    }
}

// Auto-run when script is loaded
console.log('🧹 Firebase Cache Cleaner loaded. Run clearFirebaseCache() to clear all Firebase data.');

// Export function
window.clearFirebaseCache = clearFirebaseCache;
