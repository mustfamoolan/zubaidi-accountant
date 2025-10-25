const CACHE_NAME = 'zubaidi-accountant-v1';
const urlsToCache = [
    '/',
    '/css/app.css',
    '/js/app.js',
    '/js/number-formatter.js',
    '/images/logo-dark.png',
    '/images/logo-light.png',
    '/images/logo-sm.png'
];

// تثبيت Service Worker
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('فتح الذاكرة المؤقتة');
                return cache.addAll(urlsToCache);
            })
    );
});

// تفعيل Service Worker
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('حذف الذاكرة المؤقتة القديمة:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// استراتيجية Cache First
self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                // إرجاع النسخة المخزنة أو تحميل من الشبكة
                return response || fetch(event.request);
            })
    );
});
