// CommunityHub Lightweight Development-Friendly Service Worker
// Network-First strategy to ensure zero stale data during development

const CACHE_NAME = 'communityhub-shell-v1';
const STATIC_ASSETS = [
  '/manifest.webmanifest',
  '/images/icons/icon-192x192.png',
  '/images/icons/icon-512x512.png',
  '/images/logos/communityhub.jpeg'
];

// Install: Cache static shell assets
self.addEventListener('install', (event) => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(STATIC_ASSETS).catch((err) => {
        console.warn('[PWA] Cache addAll warning:', err);
      });
    })
  );
});

// Activate: Clean old caches and claim clients immediately
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames
          .filter((name) => name !== CACHE_NAME)
          .map((name) => caches.delete(name))
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch: Strictly Network-First
self.addEventListener('fetch', (event) => {
  // Only handle GET requests
  if (event.request.method !== 'GET') {
    return;
  }

  // Network-First for everything: try network first, fallback to cache only if network fails completely (offline)
  event.respondWith(
    fetch(event.request)
      .then((networkResponse) => {
        return networkResponse;
      })
      .catch(() => {
        return caches.match(event.request);
      })
  );
});