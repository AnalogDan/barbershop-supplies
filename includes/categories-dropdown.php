<style>
    @font-face {
        font-family: 'Old London';
        src: url('/barbershopSupplies/public/fonts/OldLondon.ttf') format('truetype');
    }

    .upper-container{
        position:relative;
    }

    .categories-dropdown {
        box-sizing: content-box;
        height: 0;
        overflow: hidden;
        background-color: #d8d8d8ff;
        width: 100%;
        margin-bottom: 40px;
        box-sizing: border-box;
        transition: height 0.3s ease, padding 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        font-weight: 600;
    }

    .categories-inner {
        padding-top: 40px;
        padding-bottom: 40px;
        box-sizing: border-box;
    }

    .categories-dropdown-content {
        display: flex;
        flex-direction: column;
        gap: 0px;
    }

    .main-category-block {
        padding-bottom: 20px;
    }

    .main-category-title {
        font-family: 'Old London', serif;
        font-size: 32px;
        margin-bottom: 10px;
        color: black;
        text-align: center;
    }
    .main-category-link {
        color: inherit;           
        text-decoration: none;    
        cursor: pointer;
    }

    .main-category-link:hover {
        color: #000000ff;
        text-decoration: underline; 
    }

    .subcategories-list {
        max-width: 50rem;
        margin: 0 auto;
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        justify-content: center;

    }
    .subcategories-list a {
        color: #5e5e5eff;
        text-decoration: none;
    }
    .subcategories-list a:hover {
        color: #050505ff;
        text-decoration: underline; 
    }
  

    .subcategory {
        font-weight: 600; 
        padding: 6px 10px;
        background-color: transparent;
        text-decoration: none;
        border-radius: 4px;
        font-family: sans-serif;
        font-size: 16px;
    }

    .subcategory:hover {
        background-color: transparent;
    }

    /* Yellow boxes for the hardcoded links */
    .categories-dropdown .main-category-block:last-child .subcategories-list a {
        display: block;
        background: #dfd898; 
        color: #444444ff;
        padding: 6px 10px;
        border-radius: 0px;
        font-weight: 600;
        text-decoration: none;
    }
    .categories-dropdown .main-category-block:last-child .subcategories-list a:hover {
        color: #000000ff;
        background: #ece6afff;
    }

</style>



<?php
    require_once __DIR__ . '/db.php';
    $mainCategoriesQuery = $pdo->query("SELECT id, name FROM main_categories ORDER BY id ASC");
    $mainCategories = $mainCategoriesQuery->fetchAll(PDO::FETCH_ASSOC);
?>
<div id="categoriesDropdown" class="categories-dropdown">
    <div class="categories-inner">
        <div class="categories-dropdown-content">
           <?php foreach ($mainCategories as $main): ?>
                <div class="main-category-block">
                    <h2 class="main-category-title">
                        <a href="<?= buildLinkWithParams(['main' => $main['id'], 'subcategory' => null, 'page' => 1, 'query' => null, 'sort' => null, 'favorites' => null, 'sale' => null]) ?>" class="main-category-link"><?= htmlspecialchars($main['name']) ?></a>
                    </h2>
                    <div class="subcategories-list">
                        <?php
                        $stmt = $pdo->prepare("SELECT id, name FROM categories WHERE main_category_id = ?");
                        $stmt->execute([$main['id']]);
                        $subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($subcategories as $sub): ?>
                            <a href="<?= buildLinkWithParams(overrides: ['subcategory' => $sub['id'], 'main' => null, 'page' => 1, 'query' => null, 'sort' => null, 'favorites' => null, 'sale' => null]) ?>"><?= htmlspecialchars($sub['name']) ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?> 
            <!-- Qiuck links -->
            <div class="main-category-block">
                <div class="subcategories-list">
                    <a href="<?= buildLinkWithParams(['main' => null, 'subcategory' => null, 'page' => 1, 'query' => null, 'sort' => null,    'favorites' => 1, 'sale' => null]) ?>">My Favorites</a>  
                    <a href="<?= buildLinkWithParams(['main' => null, 'subcategory' => null, 'page' => 1, 'query' => null, 'sort' => null, 'favorites' => null, 'sale' => null]) ?>">All</a>
                    <a href="<?= buildLinkWithParams(['main' => null, 'subcategory' => null, 'page' => 1, 'query' => null, 'sort' => null, 'favorites' => null,    'sale' => 1]) ?>">Sales</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const toggle = document.getElementById('categoriesToggle');
    const dropdown = document.getElementById('categoriesDropdown');
    const chevron = document.getElementById('chevronIcon');

    toggle.addEventListener('click', () => {
        const isOpen = dropdown.classList.contains('open');

        if (isOpen) {
            dropdown.style.height = '0';
            dropdown.classList.remove('open');
        } else {
            dropdown.classList.add('open');
            dropdown.style.height = dropdown.scrollHeight + 'px'; 
        }
        chevron.classList.toggle('down', !isOpen);
    });

    document.addEventListener('DOMContentLoaded', () => {
        const shouldOpen = sessionStorage.getItem('openCategoriesDropdown');
        if (!shouldOpen) return;
        sessionStorage.removeItem('openCategoriesDropdown');
        // Scroll to the toggle
        toggle.scrollIntoView({ behavior: 'smooth', block: 'start' });
        // Open only if closed
        if (!dropdown.classList.contains('open')) {
            toggle.click();
        }
    });
</script>