<style>
    .pagination{
        font-weight: bold;
        display: flex;
        gap 10px;
        margin-top: 20px;
        margin-bottom: 60px;
        justify-content: center;
        align-items: center;
    }
    .pagination a.page,
    .pagination a.next {
        text-decoration: none;
    }
    .page{
        color: gray;
        cursor: pointer;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 14px;
    }
    .prev,
    .next{
        color: gray;
        cursor: pointer;
        width: 58px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 18px;
        text-decoration: none;
    }
    .page:hover, .next:hover{
        color: black;
    }
    .page.current{
        background-color: #ddd;
        color: black;
    }
</style>

<?php
// Build a base query string to keep current filters
$baseParams = [];
if (!empty($searchQuery)) $baseParams['query'] = $searchQuery;
if (!empty($mainCategoryId)) $baseParams['main'] = $mainCategoryId;
if (!empty($subCategoryId)) $baseParams['subcategory'] = $subCategoryId;
?>

<div class="pagination">
    <?php if ($currentPage > 1): ?>
        <a class="prev" href="?<?= http_build_query(array_merge($baseParams, ['main_page' => $currentPage - 1])) ?>">&lt; Prev</a>
    <?php endif; ?>

    <?php
    $window = 2;
    $start = max(1, $currentPage - $window);
    $end   = min($totalPages, $currentPage + $window);

    if ($start > 1) {
        echo '<a class="page" href="?' .
            http_build_query(array_merge($baseParams, ['main_page' => 1])) .
            '">1</a>';
        if ($start > 2) echo '<span class="dots">…</span>';
    }

    for ($i = $start; $i <= $end; $i++) {
        if ($i == $currentPage) {
            echo '<span class="page current">' . $i . '</span>';
        } else {
            echo '<a class="page" href="?' .
                http_build_query(array_merge($baseParams, ['main_page' => $i])) .
                '">' . $i . '</a>';
        }
    }

    if ($end < $totalPages) {
        if ($end < $totalPages - 1) echo '<span class="dots">…</span>';
        echo '<a class="page" href="?' .
            http_build_query(array_merge($baseParams, ['main_page' => $totalPages])) .
            '">' . $totalPages . '</a>';
    }
    ?>

    <?php if ($currentPage < $totalPages): ?>
        <a class="next" href="?<?= http_build_query(array_merge($baseParams, ['main_page' => $currentPage + 1])) ?>">Next &gt;</a>
    <?php endif; ?>
</div>