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

<div class="pagination">
    <?php if ($currentPage2 > 1): ?>
        <a class="next" href="?sub_page=<?= $currentPage2 - 1 ?>&grid=sub<?= $searchQuery ? '&query=' . urlencode($searchQuery) : '' ?>">&lt; Prev</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages2; $i++): ?>
        <?php if ($i == $currentPage2): ?>
            <span class="page current"><?= $i ?></span>
        <?php else: ?>
            <a class="page" href="?sub_page=<?= $i ?>&grid=sub<?= $searchQuery ? '&query=' . urlencode($searchQuery) : '' ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($currentPage2 < $totalPages2): ?>
        <a class="next" href="?sub_page=<?= $currentPage2 + 1 ?>&grid=sub<?= $searchQuery ? '&query=' . urlencode($searchQuery) : '' ?>">Next &gt;</a>
    <?php endif; ?>
</div>