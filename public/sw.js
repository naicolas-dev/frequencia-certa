const CACHE_NAME = 'frequencia-certa-v1';
const ASSETS_TO_CACHE = [
    '/',
    '/offline',
    '/img/icons/icon-192x192.png',
    '/img/icons/icon-512x512.png',
    // Adicione aqui outros arquivos estáticos essenciais se precisar
];

// 1. Instalação: Cache dos arquivos estáticos essenciais
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(ASSETS_TO_CACHE);
        })
    );
});

// 2. Ativação: Limpa caches antigos se a versão mudar
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME) {
                        return caches.delete(cache);
                    }
                })
            );
        })
    );
});

// 3. Interceptação de Requisições (Fetch)
self.addEventListener('fetch', (event) => {
    event.respondWith(
        fetch(event.request)
            .catch(() => {
                return caches.match(event.request).then((response) => {
                    if (response) {
                        return response;
                    }
                    // Se não tiver internet e não estiver no cache, mostra página offline (opcional)
                    // return caches.match('/offline'); 
                });
            })
    );
});