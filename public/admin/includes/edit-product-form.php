<style>
    .product-form{
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
    .product-form input:focus{
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
        margin-top: 15px;
        border: 0.5px solid #000;
        transition: background-color 0.2s ease;
    }
    .upload-circle:hover {
        background-color: #ccc;
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
        object-fit: cover;
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

</style>

<form class="product-form">
    <label for="name">Name</label>
    <input type="text" id="name" name="name" value="Wahl Senior Cordless Clipper">
    <label for="category">Category</label>
    <select id="category" name="category">
        <option value="">Select a category</option>
        <option value="clippers" selected>Clippers</option>
        <option value="combs">Combs</option>
        <option value="scissors">Scissors</option>
    </select>
    <label for="price">Price</label>
    <input type="number" id="price" name="price" min="0" step="0.01" placeholder="0.00" value="119.99">
    <label for="stock">Stock</label>
    <input type="number" id="stock" name="stock" min="0" step="1" placeholder="0" value="15">
    <label for="description">Description</label>
    <textarea id="description" name="description" rows="5" placeholder="Enter product description here...">
Product Details

*PROFESSIONAL PRECISION: Wahl's Professional 5 Star Series Senior Clipper was designed to deliver the cutting performance that experts demand; specifically designed for on scalp tapering and fading, precision fades and clipper over comb work; intended f or professional use only
*STYLISH AND FUNCTIONAL: The 5-Star Senior features a adjustable blades with zero overlap capabilities; The high impact, durable aluminum metal bottom housing is constructed to withstand all the regular wear and tear in your salon or barbershop
*ACCESSORIES INCLUDED: 5 Star Series Senior Clipper comes with all accesories required for professional barber use; the clipper, 3 attachment comb cutting guides (1/16", 1/8" and 3/16"), styling comb, cleaning brush, clipper blade oil, red blade guard and operating instructions.
*PRODUCT SPECIFICATIONS: The 5 Star Series Senior Clipper measures only 6.5 inches in length and weighs just 1 pound, 3 ounces; featuring a V9000 Electromagnetic Motor, an Adjustable 0000 Blade, and an 8 foot professional grade, chemical resistant power cord.
    </textarea>

    <label for="thumbnail">Thumbnail cutout</label>
    <div class="image-input">
        <img src="/barbershopSupplies/public/images/products/test.webp" alt="Current Thumbnail">
        <div class="overlay">
            <button class="edit-icon-btn" onclick="#">
                <i class="fa-solid fa-pen"></i>
            </button>
        </div>
    </div>

    <label for="mainImage">Main Image</label>
    <div class="image-input">
        <img src="/barbershopSupplies/public/images/products/test2.jpg" alt="Current Thumbnail">
        <div class="overlay">
            <button class="edit-icon-btn" onclick="#">
                <i class="fa-solid fa-pen"></i>
            </button>
        </div>
    </div>

    <label for="gallery">Gallery</label>
    <div class="gallery-section">
        <div class="gallery-strip">
            <div class="image-input">
                <img src="/barbershopSupplies/public/images/products/test3.jpg" alt="Gallery Image">
                <div class="overlay">
                    <button class="edit-icon-btn" onclick="#">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="image-input">
                <img src="/barbershopSupplies/public/images/products/test3.jpg" alt="Gallery Image">
                <div class="overlay">
                    <button class="edit-icon-btn" onclick="#">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="image-input">
                <img src="/barbershopSupplies/public/images/products/test3.jpg" alt="Gallery Image">
                <div class="overlay">
                    <button class="edit-icon-btn" onclick="#">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            
            <div class="upload-circle" id="gallery">+</div>
        </div>
    </div>

    <div class="button-row">
        <a href="#" class="btn btn-third">Save changes</a>
        <a href="products.php" class="btn btn-fourth">Cancel</a>
    </div>
</form>