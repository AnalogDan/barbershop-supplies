<?php

require_once __DIR__ . '/../config.php';
require_once BASE_PATH . 'includes/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require BASE_PATH . 'phpmailer/vendor/autoload.php';

function createMailer()
{
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USER;
    $mail->Password = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = SMTP_PORT;
    $mail->setFrom(
        SMTP_FROM_EMAIL,
        SMTP_FROM_NAME
    );
    $mail->addReplyTo(
        'newvisionbsadm@gmail.com',
        'New Vision Barber Supplies'
    );
    $mail->isHTML(true);
    return $mail;
}

function sendActivationEmail($email, $token)
{
    $activation_link = "http://localhost/public_html/actions/activate.php?token=" . $token;
    //$activation_link = "http://newvision-barbersupplies.com/actions/activate.php?token=" . $token;
    $mail = createMailer();
    $mail->addAddress($email);
    $mail->Subject = "Activate your account";
    $mail->Body = "
    Please click the link to activate your account:
    <a href='$activation_link'>$activation_link</a>
    ";
    $mail->AltBody = "Activation link: $activation_link";
    $mail->send();
}

function sendOrderConfirmationEmail($orderId)
{
    //Fetch data :D
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT
            o.*,
            a.full_name,
            a.street,
            a.city,
            a.state,
            a.zip,
            a.email,
            a.phone
        FROM orders o
        JOIN addresses a ON o.address_id = a.id
        WHERE o.id = ?
    ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$order) {
        return false;
    }
    $itemStmt = $pdo->prepare("
        SELECT
            product_name,
            price,
            quantity
        FROM order_items
        WHERE order_id = ?
    ");
    $itemStmt->execute([$orderId]);
    $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

    //Write item summary
    $items_html = "";
    foreach ($items as $item) {
        $line_total = $item['price'] * $item['quantity'];
        $items_html .= "
            <tr>
                <td>{$item['product_name']}</td>
                <td>{$item['quantity']}</td>
                <td>$" . number_format($item['price'], 2) . "</td>
                <td>$" . number_format($line_total, 2) . "</td>
            </tr>
        ";
    }
    $items_table = "
    <table border='1' cellpadding='8' cellspacing='0'>
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
        </tr>
        $items_html
    </table>
    ";

    $subject = "Your Order #" . $order['number'];
    $body = "
    <h2>Thank you for your order!</h2>
    <p><strong>Order Number:</strong> {$order['number']}</p>
    <p><strong>Total:</strong> $" . number_format($order['total'], 2) . "</p>
    <h3>Shipping Address</h3>
    <p>
    {$order['full_name']}<br>
    {$order['street']}<br>
    {$order['city']}, {$order['state']} {$order['zip']}<br>
    {$order['email']}<br>
    {$order['phone']}
    </p>
    <h3>Items</h3>
    $items_table
    <h3>Shipping Method</h3>
    <p>
    <strong>{$order['shipping_service_name']}</strong><br>
    $" . number_format($order['shipping_cost'], 2) . "<br>
    Estimated delivery: {$order['delivery_eta']}
    </p>

    <p>
    When your order ships, we’ll send you a tracking link.
    </p>
    ";

    //send email
    $mail = createMailer();
    $mail->addAddress($order['email']);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
    return true;
}

function sendNewOrderNotificationEmail($orderId)
{
    //fetch info
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT
            o.*,
            a.full_name,
            a.email,
            a.phone,
            a.street,
            a.city,
            a.state,
            a.zip
        FROM orders o
        JOIN addresses a ON o.address_id = a.id
        WHERE o.id = ?
    ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$order) {
        return false;
    }
    $itemStmt = $pdo->prepare("
        SELECT product_name, quantity, price
        FROM order_items
        WHERE order_id = ?
    ");
    $itemStmt->execute([$orderId]);
    $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

    //Build email body
    $items_html = "";
    foreach ($items as $item) {
        $line_total = $item['price'] * $item['quantity'];
        $items_html .= "
            <tr>
                <td>{$item['product_name']}</td>
                <td>{$item['quantity']}</td>
                <td>$" . number_format($item['price'], 2) . "</td>
                <td>$" . number_format($line_total, 2) . "</td>
            </tr>
        ";
    }
    $items_table = "
    <table border='1' cellpadding='8' cellspacing='0'>
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
        </tr>
        $items_html
    </table>
    ";
    $body = "
    <h2>🛒 New Order Received</h2>
    <p><strong>Order #:</strong> {$order['number']}</p>
    <p><strong>Total:</strong> $" . number_format($order['total'], 2) . "</p>
    <h3>Customer</h3>
    <p>
    {$order['full_name']}<br>
    {$order['email']}<br>
    {$order['phone']}<br>
    </p>
    <h3>Shipping Address</h3>
    <p>
    {$order['street']}<br>
    {$order['city']}, {$order['state']} {$order['zip']}<br>
    </p>
    <h3>Items</h3>
    $items_table
    <h3>Shipping Method</h3>
    <p>
    <strong>Method:</strong> {$order['shipping_service_name']}<br>
    <strong>Cost:</strong> $" . number_format($order['shipping_cost'], 2) . "<br>
    <strong>Estimated Delivery:</strong> {$order['delivery_eta']}<br>
    </p>";

    //send email
    $mail = createMailer();
    $mail->addAddress("Newvisionbsadm@gmail.com");
    $mail->Subject = "New Order #" . $order['number'];
    $mail->Body = $body;
    $mail->send();

    return true;
}

function sendTrackingEmail($orderId)
{
    //fetch info, if not tracking info, skip
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT
            o.*,
            a.full_name,
            a.email
        FROM orders o
        JOIN addresses a ON o.address_id = a.id
        WHERE o.id = ?
    ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$order) {
        return false;
    }
    if (empty($order['tracking_number'])) {
        return false;
    }
    $trackingNumber = $order['tracking_number'] ?? '';
    $trackingUrl = '';
    if (!empty($trackingNumber)) {
        $trackingUrl =
            'https://www.ups.com/track?tracknum=' .
            urlencode($trackingNumber);
    }

    //Build email body
    $body = "
    <h2>Your order has shipped!</h2>
    <p>
    Good news, {$order['full_name']}!
    </p>
    <p>
    Your order <strong>{$order['number']}</strong> has been shipped.
    </p>
    ";
    if (!empty($trackingNumber)) {
        $body .= "
        <p>
            <strong>Tracking Number:</strong><br>
            {$trackingNumber}
        </p>
        ";
    }
    if (!empty($trackingUrl)) {
        $body .= "
        <p>
            <strong>Track your package:</strong><br>
            <a href='{$trackingUrl}'>{$trackingUrl}</a>
        </p>
        ";
    }
    $body .= "
    <p>
    Thank you for shopping with New Vision Barber Supplies.
    </p>
    ";

    //send email
    $mail = createMailer();
    $mail->addAddress($order['email']);
    $mail->Subject = "Your Order Has Shipped - Order #" . $order['number'];
    $mail->Body = $body;
    $mail->send();
    return true;
}
