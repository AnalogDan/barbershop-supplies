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
    $queryParams = [];
    if (!empty($searchQuery)) $queryParams['query'] = $searchQuery;
    if (!empty($filter)) $queryParams['filter'] = $filter;
?>

<div class="pagination">
    <?php if ($currentPagee > 1): ?>
        <?php 
            $queryParams['page'] = $currentPagee - 1;
        ?>
        <a class="next" href="?<?= http_build_query($queryParams) ?>">&lt; Prev</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <?php $queryParams['page'] = $i; ?>
        <?php if ($i == $currentPagee): ?>
            <span class="page current"><?= $i ?></span>
        <?php else: ?>
            <a class="page" href="?<?= http_build_query($queryParams) ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($currentPagee < $totalPages): ?>
        <?php $queryParams['page'] = $currentPagee + 1; ?>
        <a class="next" href="?<?= http_build_query($queryParams) ?>">Next &gt;</a>
    <?php endif; ?>
</div>