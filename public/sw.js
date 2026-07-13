const CACHE_NAME = 'simgpi-universal-cache-v2';

// Hanya cache aset utama saat instalasi
const PRECACHE_URLS = [
    '/',
    '/manifest.json',
    '/gpi_logo.png'
];

self.addEventListener('install', event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(PRECACHE_URLS))
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(self.clients.claim());
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) return caches.delete(cacheName);
                })
            );
        })
    );
});

// STRATEGI: Network First, Fallback to Cache
self.addEventListener('fetch', event => {
    if (event.request.method !== 'GET') return;

    // Jangan cache request API atau pencarian dinamis
    const url = new URL(event.request.url);
    if (url.pathname.startsWith('/api/') || url.search.length > 0) return;

    event.respondWith(
        fetch(event.request)
            .then(networkResponse => {
                if (networkResponse && networkResponse.status === 200 && networkResponse.type === 'basic') {
                    const responseToCache = networkResponse.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(event.request, responseToCache);
                    });
                }
                return networkResponse;
            })
            .catch(() => {
                // Saat Offline, ambil dari memori (Cache)
                return caches.match(event.request);
            })
    );
});