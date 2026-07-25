const CACHE_NAME = 'miorpa-notify-v5';

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

function showPaymentNotification(data) {
    const payment = data.payment || data;

    const title =
        data.title ||
        `Nuevo ${payment.provider || 'pago'}: S/ ${
            payment.amount || '0.00'
        }`;

    const body =
        data.body ||
        `${payment.payer_name || 'Cliente no identificado'} realizó un pago.`;

    const detailUrl =
        data.url ||
        data.detail_url ||
        payment.detail_url ||
        '/business/payments';

    const paymentId =
        data.payment_id ||
        payment.public_id ||
        Date.now().toString();

    return self.registration.showNotification(
        title,
        {
            body: body,
            icon: data.icon || '/logo-icon-192.png',
            badge: data.badge || '/logo-icon-192.png',
            tag: data.tag || `miorpa-payment-${paymentId}`,
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
    );
};

/*
 * Notificación enviada por Laravel Web Push.
 */
self.addEventListener('push', function (event) {
    let data = {};

    try {
        if (event.data) {
            data = event.data.json();
        }
    } catch (error) {
        data = {
            title: 'Nuevo pago recibido',
            body: event.data
                ? event.data.text()
                : 'Se recibió un nuevo pago.'
        };
    }

    event.waitUntil(
        showPaymentNotification(data)
    );
});

/*
 * Notificación enviada directamente desde la página.
 */
self.addEventListener('message', function (event) {
    const data = event.data || {};

    if (data.type !== 'PAYMENT_RECEIVED') {
        return;
    }

    event.waitUntil(
        showPaymentNotification(data)
    );
});

self.addEventListener(
    'notificationclick',
    function (event) {
        event.notification.close();

        const data =
            event.notification.data || {};

        const detailUrl =
            data.url ||
            data.detail_url ||
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