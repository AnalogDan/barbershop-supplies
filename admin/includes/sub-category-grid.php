<style>
    .product-grid{
        display: grid;
        grid-template-columns: 1fr 1fr 200px;
        gap: 20px;
        align-items: center;
        margin: 40px 60px 40px 60px;
        text-align: center;
    }
    .header{
        font-weight: bold;
        padding: 8px;
        border-bottom: 1px solid black;
        color: black;
        text-align: center;
    }
    .name {
        transition: outline 0.3s ease;
    }
    .name:focus {
        outline: 0.5px solid black;
        background: #e2e2e2;
    }
    .parent-category{
        font-size: 13px;
        padding: 7px 12px;
        border: 0.5px solid #000;
        border-radius: 0px;
        background-color: #e2e2e2;
        /* appearance: none;  */
    }
</style>

<div class="product-grid">
    <div class="header name">Name</div>
    <div class="header parent-categ">Parent category</div>
    <div class="header action">Action</div>

    <div class="name" contenteditable="true">Trimmers</div>
    <select id="parent-category" name="parent-category" class="parent-category">
        <option value="clippers">Tools & electricals</option>
        <option value="combs">Hair products</option>
        <option value="scissors">Beard care</option>
        <option value="furniture">Furniture & Mirrors</option>
        <option value="accessories">Barber Accessories</option>
    </select>
    <div class="action">
        <span class="delete-icon" style="cursor: pointer; margin-left: 10px;">
            <i class ="fas fa-trash" style="color: black;"></i>
        </span>
    </div>

    <?php
    for ($i = 0; $i < 10; $i++) {
        ?>
        <div class="name" contenteditable="true">Trimmers</div>
        <select id="parent-category" name="parent-category" class="parent-category">
            <option value="clippers">Tools & electricals</option>
            <option value="combs">Hair products</option>
            <option value="scissors">Beard care</option>
            <option value="furniture">Furniture & Mirrors</option>
            <option value="accessories">Barber Accessories</option>
        </select>
        <div class="action">
            <span class="delete-icon" style="cursor: pointer; margin-left: 10px;">
                <i class ="fas fa-trash" style="color: black;"></i>
            </span>
        </div>
    <?php }
        ?>
</div>