<?php
if (!isset($_GET['session_id'])) {
    echo 'Missing session ID.';
    exit;
}

$sessionId = $_GET['session_id'];
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Processing payment</title>
    </head>
    <body>

        <h2>Processing your payment…</h2>
        <p>Please wait. Do not refresh or close this page.</p>

        <p>
            Stripe session ID:<br>
            <code><?= htmlspecialchars($sessionId) ?></code>
        </p>

    </body>
    <script>
        // let attempts = 0;
        // const maxAttempts = 10;

        // const interval = setInterval(async () => {
        //     attempts++;
        //     console.log('attempt:', attempts);

        //     try {
        //         const res = await fetch(
        //             '/barbershopSupplies/actions/check-checkout-status.php?session_id=<?= $sessionId ?>'
        //         );

        //         const data = await res.json();
        //         console.log('status:', data.status);

        //         if (data.status === 'paid') {
        //             clearInterval(interval);
        //             if (data.status === 'paid' && data.redirect) {
        //                 clearInterval(interval);
        //                 window.location.href = data.redirect;
        //                 return;
        //             }
        //             return;
        //         }

        //         if (attempts >= maxAttempts) {
        //             clearInterval(interval);
        //             console.warn('Max attempts reached');
        //             document.body.innerHTML = `
        //                 <h2>Still processing your payment</h2>
        //                 <p>Your payment was received, but the order is still being finalized.</p>
        //                 <p>You may safely refresh this page in a moment.</p>
        //             `;
        //         }
        //     } catch (err) {
        //         clearInterval(interval);
        //         console.error('Polling error:', err);
        //     }
        // }, 2000);

        let attempts = 0;
        const maxAttempts = 10;

        const interval = setInterval(async () => {
            attempts++;
            console.log('attempt:', attempts);

            try {
                const res = await fetch(
                    '/barbershopSupplies/actions/check-checkout-status.php?session_id=<?= $sessionId ?>'
                );

                // Log raw response for debugging
                const text = await res.text();
                console.log('RAW RESPONSE >>>', text);

                let data;
                try {
                    data = JSON.parse(text);
                } catch (err) {
                    console.error('JSON parse error:', err);
                    return;
                }

                console.log('status:', data.status, 'order_id:', data.order_id, 'token:', data.token);

                // Only redirect if paid AND order created
                if (data.status === 'paid' && data.order_id && data.token) {
                    clearInterval(interval);
                    window.location.href = `/barbershopSupplies/public/success.php?order_id=${data.order_id}&token=${data.token}`;
                    return;
                }

                if (attempts >= maxAttempts) {
                    clearInterval(interval);
                    console.warn('Max attempts reached, payment may still be processing.');
                }

            } catch (err) {
                clearInterval(interval);
                console.error('Polling error:', err);
            }
        }, 2000);
    </script>
</html>