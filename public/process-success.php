<?php
require_once __DIR__ . '/../includes/header.php';
if (!isset($_GET['session_id'])) {
    echo 'Missing session ID.';
    exit;
}
$sessionId = $_GET['session_id'];
?>

<style>
    html, body {
        height: 100%;
        margin: 0;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
        background: #f6f7f9;
        color: #222;
    }

    .processing-wrapper {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .processing-card {
        background: #ffffff;
        max-width: 420px;
        width: 100%;
        padding: 32px;
        border-radius: 14px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        text-align: center;
    }

    .spinner {
        width: 48px;
        height: 48px;
        border: 4px solid #e5e7eb;
        border-top-color: #dfd898;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 24px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    h2 {
        margin: 0 0 12px;
        font-size: 1.4rem;
        font-weight: 600;
    }

    p {
        margin: 0 0 18px;
        color: #555;
        font-size: 0.95rem;
    }

    .session-id {
        background: #f3f4f6;
        border-radius: 8px;
        padding: 12px;
        font-size: 0.85rem;
        color: #333;
        word-break: break-all;
    }

    .note {
        font-size: 0.85rem;
        color: #777;
        margin-top: 16px;
    }
</style>

<!DOCTYPE html>
<html lang="en">
    <body>
        <div class="processing-wrapper">
            <div class="processing-card">
                <h2 id="status-title">Processing your payment</h2>
                <p id="status-message">Please wait while we confirm your transaction.</p>

                <div class="spinner" id="spinner"></div>

                <p class="note" id="status-note">Do not refresh or close this page.</p>
            </div>
        </div>
    </body>
    <script>

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
                if (data.status === 'failed' || data.status === 'expired') {
                    clearInterval(interval);
                    const title = document.getElementById('status-title');
                    const message = document.getElementById('status-message');
                    const note = document.getElementById('status-note');
                    const spinner = document.getElementById('spinner');
                    if (spinner) spinner.style.display = 'none';
                    if (title) title.textContent = 'Payment not completed';
                    if (message) {
                        message.textContent =
                            'Your payment was not completed and no charge was made.';
                    }
                    if (note) {
                        note.innerHTML =
                            '<a href="/barbershopSupplies/public/cart.php">Return to cart</a>';
                    }
                    return;
                }
                if (data.status === 'pending' || data.status === 'processing') {
                }

                if (attempts >= maxAttempts) {
                    clearInterval(interval);
                    console.warn('Max attempts reached, payment may still be processing.');
                    // Update UI for the user
                    const title = document.getElementById('status-title');
                    const message = document.getElementById('status-message');
                    const note = document.getElementById('status-note');
                    const spinner = document.getElementById('spinner');
                    if (spinner) spinner.style.display = 'none';
                    if (title) title.textContent = 'Still processing your payment';
                    if (message) {
                        message.textContent =
                            'Your payment is taking a little longer than usual. This can happen, and no action is needed.';
                    }
                    if (note) {
                        note.innerHTML =
                            'You can safely wait on this page or refresh it in a moment.<br>' +
                            'If you are charged, your order will be completed automatically.';
                    }
                }

            } catch (err) {
                clearInterval(interval);
                console.error('Polling error:', err);
            }
        }, 2000);
    </script>
</html>