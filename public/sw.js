/*
 * Kwang-Lim Chapel PWA service worker.
 *
 * Security model: this portal serves per-user, authenticated HTML. Nothing that could
 * contain one user's data is ever written to the cache, so after logout (or on a shared
 * device) the next person can never be shown a previous user's page:
 *   - navigations / HTML: network-only, falling back to the static /offline.html page
 *   - non-GET requests: never intercepted
 *   - API, exports, uploaded files (/storage), auth routes: network-only, never cached
 *   - only fingerprinted Vite assets (/build/) and a few public static files are cached
 */
const CACHE_NAME = 'au-chapel-v5';
const PRECACHE = [
  '/offline.html',
  '/manifest.json',
  '/favicon.svg',
  '/umc-logo.svg'
];
const STATIC_FILES = new Set(['/offline.html', '/manifest.json', '/favicon.svg', '/favicon.ico', '/umc-logo.svg']);

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => cache.addAll(PRECACHE))
      .catch(() => {})
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  // Drop old cache versions only (the previous version also deleted the fresh precache)
  event.waitUntil(
    caches.keys()
      .then((keys) => Promise.all(keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))))
      .then(() => self.clients.claim())
  );
});

function isCacheableAsset(url) {
  if (url.origin !== self.location.origin) return false;
  if (url.search) return false;
  return url.pathname.startsWith('/build/') || STATIC_FILES.has(url.pathname);
}

self.addEventListener('fetch', (event) => {
  const request = event.request;

  // Never touch POST/PUT/DELETE etc.
  if (request.method !== 'GET') return;

  const url = new URL(request.url);
  const accept = request.headers.get('accept') || '';
  const isNavigation = request.mode === 'navigate' || accept.includes('text/html');

  // Authenticated pages: always go to the network and never store the response.
  if (isNavigation) {
    event.respondWith(
      fetch(request).catch(() => caches.match('/offline.html'))
    );
    return;
  }

  // Everything except known-static, same-origin assets bypasses the service worker.
  if (!isCacheableAsset(url)) return;

  // Static assets: cache-first (Vite filenames are content-hashed).
  event.respondWith(
    caches.match(request).then((cached) => {
      if (cached) return cached;
      return fetch(request).then((response) => {
        if (response.ok && response.type === 'basic') {
          const clone = response.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
        }
        return response;
      });
    })
  );
});

