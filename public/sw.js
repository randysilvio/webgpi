// public/sw.js

const CACHE_NAME = 'simgpi-cache-v1';

// Daftar URL yang wajib di-cache agar bisa dibuka saat offline
const URLS_TO_CACHE = [
    '/',
    '/berita',
    '/admin/dashboard',
    '/admin/jurnal',
    '/admin/jurnal/create',
    // Cache asset statis (Sesuaikan nama file jika berbeda di folder public Anda)
    '/manifest.json',
    '/gpi_logo.png',
    // Font Awesome & Tailwind (di-cache saat pertama kali online)
    'https://cdn.tailwindcss.com',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'
];

/**
 * INSTALL EVENT
 * Dipanggil pertama kali saat Service Worker didaftarkan.
 * Tugas: Mengunduh dan menyimpan URLS_TO_CACHE ke dalam memori perangkat (Cache Storage).
 */
self.addEventListener('install', (event) => {
    // Memaksa SW baru segera mengambil alih (skip waiting)
    self.skipWaiting();
    
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('Opened cache');
                // Gunakan promise allSettled atau tangkap error agar jika ada 1 file gagal,
                // SW tetap terinstall.
                return Promise.all(
                    URLS_TO_CACHE.map(url => {
                        return cache.add(url).catch(error => {
                            console.error('Failed to cache:', url, error);
                        });
                    })
                );
            })
    );
});

/**
 * ACTIVATE EVENT
 * Dipanggil saat SW mulai aktif.
 * Tugas: Membersihkan cache versi lama jika ada update (misal CACHE_NAME berubah jadi v2).
 */
self.addEventListener('activate', (event) => {
    // Segera kendalikan klien yang terbuka
    event.waitUntil(self.clients.claim());

    const cacheAllowlist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheAllowlist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

/**
 * FETCH EVENT (JANTUNG MODE OFFLINE)
 * Dipanggil setiap kali browser melakukan request HTTP (klik link, load gambar, dll).
 * Strategi: "Stale-While-Revalidate" atau "Network First, fallback to Cache".
 * Di sini kita pakai "Network First" untuk halaman dinamis agar data selalu baru jika ada sinyal,
 * tapi jika tidak ada sinyal, dia akan menarik halaman dari cache.
 */
self.addEventListener('fetch', (event) => {
    // Hindari caching request yang bukan GET (seperti POST submit form)
    // Penanganan form POST saat offline sudah diurus oleh IndexedDB di create.blade.php
    if (event.request.method !== 'GET') return;

    // Hindari intercept request ke API atau URL khusus lainnya jika diperlukan
    const url = new URL(event.request.url);
    if (url.pathname.startsWith('/api/')) return;

    event.respondWith(
        fetch(event.request)
            .then((networkResponse) => {
                // Jika jaringan online dan merespon dengan baik, 
                // kita simpan respons terbaru ke dalam cache agar update.
                if (networkResponse && networkResponse.status === 200 && networkResponse.type === 'basic') {
                    const responseToCache = networkResponse.clone();
                    caches.open(CACHE_NAME)
                        .then((cache) => {
                            cache.put(event.request, responseToCache);
                        });
                }
                return networkResponse;
            })
            .catch(() => {
                // Jika fetch gagal (karena TIDAK ADA SINYAL / OFFLINE), 
                // cari di dalam cache
                return caches.match(event.request)
                    .then((cachedResponse) => {
                        if (cachedResponse) {
                            return cachedResponse;
                        }
                        
                        // Jika URL tidak ada di cache sama sekali dan sedang offline,
                        // Anda bisa mereturn halaman "Offline Fallback" jika ada.
                        // return caches.match('/offline.html');
                    });
            })
    );
});