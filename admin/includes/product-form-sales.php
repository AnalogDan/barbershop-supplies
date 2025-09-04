<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/barbershopSupplies/includes/db.php';
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])){
        die('Invalid product ID');
    }
    $productId = (int) $_GET['id'];

    $stmt = $pdo->prepare("SELECT sale_price, sale_start, sale_end FROM products WHERE id = :id");
    $stmt->execute(['id' => $productId]);
    $sale = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$sale) {
        die('Product not found.');
    }
 
?>  

<style>
    .product-form.form-2{
        max-width: 500px;
        width: 100%;
        margin: 40px auto;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .product-form label{
        font-size: 20px;
        font-weight: bold;
        color: black;
        margin-bottom: 5px;
        display: block;
    }
    .product-form input,
    .product-form select,
    .product-form textarea{
        width: 100%;
        height: 45px;
        padding: 10px;
        border: 0.5px solid #000;
        background-color: #e2e2e2;
    }
    .product-form textarea{
        text-align: center;
        text-align: justify;
    }
    .product-form input:focus,
    .product-form textarea:focus{
        outline: none;
        box-shadow: 0 0 0 1px #7f7f7f;
    }
    .upload-circle{
        width: 40px;
        height: 40px;
        background-color: #e2e2e2;
        border-radius: 50%;
        font-size: 30px;
        font-weight: bold;
        color: black;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding-bottom: 3px;
        border: 0.5px solid #000;
    }
    .gallery-strip .upload-circle {
        margin-top: 15px; 
    }

    .image-input{
        position: relative;
        width: 70px;
        height: 70px;
        overflow: hidden;
        margin-bottom: 20px;
    }
    .image-input img{
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
    }
    .image-input .overlay{
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.6);
        opacity: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: opacity 0.3s ease;
    }
    .image-input:hover .overlay {
        opacity: 1;
    }
    .edit-icon-btn {
        font-size: 20px;
        color: #333;
        background: none;
        border: none;
        cursor: pointer;
    }

    .gallery-strip{
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }
    
    .hr{
        background-color: black;
    }
    
</style>

<form class="product-form form-2" id="productForm2" method="POST" enctype="multipart/form-data">
    <hr>
    <input type="hidden" name="id" id="productId" value="<?php echo $productId; ?>">
    <div class="section-title">
        <h2>Add/Edit Sale</h2>
    </div>
    <label for="sale">Sale price</label>
    <input type="number" id="sale" name="sale" min="0" step="0.01" placeholder="0.00" value="<?php echo htmlspecialchars($sale['sale_price']); ?>">
    <label for="start">Start date</label>
    <input type="date" id="start" name="start" 
            value="<?php echo !empty($sale['sale_start']) ? date('Y-m-d', strtotime($sale['sale_start'])) : ''; ?>">

    <label for="end">End date</label>
    <input type="date" id="end" name="end" 
            value="<?php echo !empty($sale['sale_end']) ? date('Y-m-d', strtotime($sale['sale_end'])) : ''; ?>">

    <div class="button-row">
        <button type="submit" class="btn btn-third">Save sale</button>
        <a href="products.php" class="btn btn-fourth">Cancel</a>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startInput = document.getElementById('start');
        const endInput = document.getElementById('end');
        const productId = document.getElementById('productId').value;

        const options = { timeZone: 'America/Los_Angeles', year: 'numeric', month: '2-digit', day: '2-digit' };
        const formatter = new Intl.DateTimeFormat('en-CA', options); 
        const today = formatter.format(new Date());

        startInput.setAttribute('min', today);

        startInput.addEventListener('change', function() {
            endInput.value = ''; 
            endInput.setAttribute('min', this.value);
        });  
    });
    document.getElementById('productForm2').addEventListener('submit', function(e) {
        e.preventDefault();
        showConfirmModal(
            "Are you sure?",
            () => {
                const form = e.target;
                const formData = new FormData(form);

                fetch('/barbershopSupplies/admin/includes/products-sale-handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlertModal("Sale updated successfully!", 
                            () => reload()
                        );
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error)
                    showAlertModal("Something went wrong");
                });
            },
            () => {}     
        );
    });
</script>