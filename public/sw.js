const CACHE_NAME = 'miorpa-notify-v4';

self.addEventListener('install', function () {
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(
        Promise.all([
            self.clients.claim(),

            caches.keys().then(function (cacheNames) {
                return Promise.all(
                    cacheNames
                        .filter(function (cacheName) {
                            return cacheName !== CACHE_NAME;
                        })
                        .map(function (cacheName) {
                            return caches.delete(cacheName);
                        })
                );
            })
        ])
    );
});

/*
 * Permite que la página solicite una notificación
 * enviando un mensaje al Service Worker.
 */
self.addEventListener('message', function (event) {
    const data = event.data || {};

    if (data.type !== 'PAYMENT_RECEIVED') {
        return;
    }

    const payment = data.payment || {};

    const paymentId =
        payment.public_id ||
        Date.now().toString();

    const amount =
        payment.amount || '0.00';

    const provider =
        payment.provider || 'Pago';

    const payer =
        payment.payer_name ||
        'Cliente no identificado';

    const detailUrl =
        payment.detail_url ||
        '/business/payments';

    event.waitUntil(
        self.registration.showNotification(
            `Nuevo ${provider}: S/ ${amount}`,
            {
                body: `${payer} realizó un pago.`,

                icon: '/logo-icon-192.png',

                badge: '/logo-icon-192.png',

                tag:
                    `miorpa-payment-${paymentId}`,

                renotify: true,

                requireInteraction: true,

                timestamp: Date.now(),

                data: {
                    url: detailUrl,
                    detail_url: detailUrl,
                    payment_id: paymentId
                },

                actions: [
                    {
                        action: 'open',
                        title: 'Ver pago'
                    }
                ]
            }
        )
    );
});

/*
 * Al pulsar la notificación, abre o enfoca
 * la página de pagos.
 */
self.addEventListener(
    'notificationclick',
    function (event) {
        event.notification.close();

        const notificationData =
            event.notification.data || {};

        const detailUrl =
            notificationData.url ||
            notificationData.detail_url ||
            '/business/payments';

        event.waitUntil(
            self.clients.matchAll({
                type: 'window',
                includeUncontrolled: true
            }).then(function (clientList) {
                for (const client of clientList) {
                    if (
                        'navigate' in client &&
                        'focus' in client
                    ) {
                        return client
                            .navigate(detailUrl)
                            .then(function () {
                                return client.focus();
                            });
                    }
                }

                if (self.clients.openWindow) {
                    return self.clients.openWindow(
                        detailUrl
                    );
                }

                return null;
            })
        );
    }
);

/*
 * Si el navegador cierra una notificación,
 * no necesitamos realizar ninguna operación.
 */
self.addEventListener(
    'notificationclose',
    function () {
        // Evento registrado intencionalmente.
    }
);