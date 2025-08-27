<?php
    session_start();
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: login.php");
        exit;
    }
	require_once __DIR__ . '/../includes/db.php';

    $orderNumber = $_GET['order'];

    $sql = "SELECT
            o.id, 
            o.number,
            a.full_name,
            a.street,
            a.city,
            a.state, 
            a.zip,
            u.phone,
            o.placed_at,
            o.delivery_eta_end,
            o.delivery_eta_start,
            o.payment_method,
            o.subtotal,
            o.sales_tax,
            o.shipping_cost,
            o.total,
            o.status
            FROM orders o
            LEFT JOIN addresses a ON o.address_id = a.id
            LEFT JOIN users u ON o.user_id = u.id
            WHERE o.number = :orderNumber";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['orderNumber' => $orderNumber]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    $orderId = $order['id'];

    $sqlProducts = "SELECT
            oi.quantity, 
            p.cutout_image,
            oi.product_name,
            oi.price
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :orderId";
    $stmt2 = $pdo->prepare($sqlProducts);
    $stmt2->execute(['orderId' => $orderId]);
    $products = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>

<style>
    .tiny-message{
        margin: 60px 60px;
        font-size: 17px;
        font-weight: bold;
        color: #929292ff;
    }

    .info-column{
        display: flex;
        flex-direction: column;
        gap: 30px;
        font-size: 17px;
        color: black;
        margin-bottom: 10px;
    }
    .info-row{
        margin-left: 60px;
        display: flex;
        gap: 8px;
    }
    .label{
        font-weight: bold;
        min-width: 160px;
    }
    .value{
        color: #929292ff;
        font-weight: bold;
    }
    .status-drop{
        font-size: 16px;
        padding: 7px 12px;
        border: 0.5px solid #000;
        border-radius: 0px;
        background-color: #e2e2e2;
    }

    .product-grid{
        margin: 0 auto;
        width: 60%;
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 10px;
    }
    .product-row{
        display: grid;
        grid-template-columns: auto 40px 1fr auto;
        align-items: center;
        gap: 10px;
        padding: 6px 15px;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
        color: #333;
        font-size: 14px;
    }
    .product-row > * {
        justify-self: center;
    }
    .thumbnail img{
        margin-left: 100px;
        width: 60px;
        height: 60px;
        object-fit: contain;
    }
    .button-row {
        display: flex;
        gap: 60px; 
        justify-content: center;
        width: fit-content;
        margin: 60px auto;
    }
</style>

<html lang="en">
    <?php include 'includes/admin_head.php'; ?>
    <body>
        <?php $currentPage = 'orders'; ?>
        <?php include 'includes/admin_navbar.php'; ?>
        <main>
            <div class="section-title">
                <h2>Order details</h2>
            </div>
            <div class="tiny-message">You can only edit the order status.</div>

            <div class="info-column">
                <div class="info-row">
                    <span class="label">Order number: </span>
                    <span class="value">#<?= htmlspecialchars($order['number']) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Shipping name: </span>
                    <span class="value"><?= htmlspecialchars($order['full_name']) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Shipping address: </span>
                    <span class="value">
                        <?= htmlspecialchars($order['zip'] . ', ' . $order['street'] . ', ' . $order['city'] . ', ' . $order['state']) ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="label">Phone: </span>
                    <span class="value"><?= htmlspecialchars($order['phone']) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Placed on date: </span>
                    <span class="value"><?= date('m/d/Y', strtotime($order['placed_at'])) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Est. delivery date: </span>
                    <span class="value">
                        <?= !empty($order['delivery_eta_start']) ? date('m/d/Y', strtotime($order['delivery_eta_start'])) : 'N/A' ?>
                        - 
                        <?= !empty($order['delivery_eta_end']) ? date('m/d/Y', strtotime($order['delivery_eta_end'])) : 'N/A' ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="label">Payment method: </span>
                    <span class="value"><?= htmlspecialchars($order['payment_method']) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Subtotal: </span>
                    <span class="value">$<?= number_format((float)$order['subtotal'], 2) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Sales tax: </span>
                    <span class="value"><?= htmlspecialchars($order['sales_tax']) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Shipping cost: </span>
                    <span class="value"><?= htmlspecialchars($order['shipping_cost']) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Total price: </span>
                    <span class="value"><?= htmlspecialchars($order['total']) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Order status: </span>
                    <select id="status" name="status" class="status-drop">
                        <?php
                        $statuses = ['delivered', 'in-transit', 'processing', 'canceled', 'failed'];
                        foreach ($statuses as $status) {
                            $selected = ($order['status'] === $status) ? 'selected' : '';
                            echo "<option value='$status' $selected>" . ucfirst($status) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="info-row">
                    <span class="label">List of products: </span>
                </div>
                <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-row">
                        <div class="qty"><?= htmlspecialchars($product['quantity']) ?>x</div>
                        <div class="thumbnail">
                            <img src="<?= '/barbershopSupplies/public/' . htmlspecialchars($product['cutout_image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                        </div>
                        <div class="name"><?= htmlspecialchars($product['product_name']) ?></div>
                        <div class="price">$<?= number_format((float)$product['price'], 2) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            </div>
            <div class="button-row">
                <button id="save-changes" class="btn btn-5">Save changes</button>
                <a href="orders.php" class="btn btn-6">Cancel</a>
            </div>
        </main>
        <?php include 'includes/admin_footer.php'; ?>
        <?php include 'includes/modals.php'; ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const saveButton = document.getElementById('save-changes'); 
                const statusSelect = document.getElementById('status');
                const currentStatus = statusSelect.value;
                const orderNumber = "<?= htmlspecialchars($order['number']) ?>";

                saveButton.addEventListener('click', () => {
                    const newStatus = statusSelect.value;

                    if (newStatus === currentStatus) {
                        showAlertModal("No changes detected");
                        return;
                    }

                    fetch('/barbershopSupplies/admin/includes/update-order.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ order_number: orderNumber, status: newStatus })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlertModal("Product updated successfully!", 
                                                () => location.reload()
                                            );                            
                        } else {
                            showAlertModal('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlertModal('An error occurred while updating the status.');
                    });
                });
            });

            function showAlertModal(message, onOk){
            const template = document.getElementById('alertModal');
            const modal = template.content.cloneNode(true).querySelector('.modal-overlay');
            document.body.appendChild(modal);
            modal.querySelector('p').textContent = message;
            modal.classList.add('show');
            const okBtn = modal.querySelector('#confirmOk');
            function cleanup() {
                okBtn.removeEventListener('click', okHandler);
                modal.remove();
            }
            function okHandler(){
                cleanup();
                if (typeof onOk === 'function'){ onOk()}
                else{};
            }
            okBtn.addEventListener('click', okHandler);
        }
        </script>
    </body>
</html>