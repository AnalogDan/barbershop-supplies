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
    .next{
        color: gray;
        cursor: pointer;
        width: 46px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 14px;
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
        <a class="next" href="?<?= http_build_query(array_merge($baseParams, ['main_page' => $currentPage - 1])) ?>">&lt; Prev</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <?php if ($i == $currentPage): ?>
            <span class="page current"><?= $i ?></span>
        <?php else: ?>
            <a class="page" href="?<?= http_build_query(array_merge($baseParams, ['main_page' => $i])) ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($currentPage < $totalPages): ?>
        <a class="next" href="?<?= http_build_query(array_merge($baseParams, ['main_page' => $currentPage + 1])) ?>">Next &gt;</a>
    <?php endif; ?>
</div>