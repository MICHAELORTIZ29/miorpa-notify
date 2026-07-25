self.addEventListener('install', function () {
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(
        self.clients.claim()
    );
});

self.addEventListener('message', function (event) {
    const data = event.data || {};

    if (data.type !== 'PAYMENT_RECEIVED') {
        return;
    }

    const payment = data.payment || {};

    const amount = payment.amount || '0.00';
    const provider = payment.provider || 'Pago';
    const payer = payment.payer_name || 'Cliente no identificado';

    event.waitUntil(
        self.registration.showNotification(
            `Nuevo ${provider}: S/ ${amount}`,
            {
                body: `${payer} realizó un pago.`,
                icon: '/logo.png',
                badge: '/logo.png',
                tag: `miorpa-payment-${payment.public_id || Date.now()}`,
                renotify: true,
                requireInteraction: true,
                data: {
                    detail_url: payment.detail_url || '/business/payments'
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

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const detailUrl =
        event.notification.data?.detail_url
        || '/business/payments';

    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        }).then(function (clientList) {
            for (const client of clientList) {
                if ('focus' in client) {
                    client.navigate(detailUrl);
                    return client.focus();
                }
            }

            if (clients.openWindow) {
                return clients.openWindow(detailUrl);
            }

            return null;
        })
    );
});