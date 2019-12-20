var cacheName = 'hello-pwa';
var filesToCache = [
  './',
  './syskankin.github.io/src/php/esign/Main.php',
  './syskankin.github.io/scripts/canvas.js',
];

/* サービスワーカー起動して、コンテンツをキャッシュする */
self.addEventListener('install', function(e) {
  e.waitUntil(
    caches.open(cacheName).then(function(cache) {
      return cache.addAll(filesToCache);
    })
  );
});

/* オフライン時はキャッシュからコンテンツを取得する */
self.addEventListener('fetch', function(e) {
  e.respondWith(
    caches.match(e.request).then(function(response) {
      return response || fetch(e.request);
    })
  );
});
