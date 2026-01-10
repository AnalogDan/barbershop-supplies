<style>
    .pagination{
        font-weight: bold;
        display: flex;
        gap: 10px;
        margin-top: -70px;
        margin-bottom: 100px;
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
        font-size: 18px;
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



<div class="pagination">
    <!-- Prev -->
    <?php if ($currentPageNum > 1): ?>
        <a class="prev" href="<?= buildLinkWithParams(['page' => $currentPageNum - 1]) ?>">&lt; Prev</a>
    <?php endif; ?>

    <?php
        $range = 2;
        $start = max(2, $currentPageNum - $range);
        $end   = min($totalPages - 1, $currentPageNum + $range);
    ?>

    <!-- Page 1 -->
    <?php if ($currentPageNum == 1): ?>
        <span class="page current">1</span>
    <?php else: ?>
        <a class="page" href="<?= buildLinkWithParams(['page' => 1]) ?>">1</a>
    <?php endif; ?>

    <!-- Left ellipsis -->
    <?php if ($start > 2): ?>
        <span class="ellipsis">…</span>
    <?php endif; ?>

    <!-- Middle pages -->
    <?php for ($i = $start; $i <= $end; $i++): ?>
        <?php if ($i == $currentPageNum): ?>
            <span class="page current"><?= $i ?></span>
        <?php else: ?>
            <a class="page" href="<?= buildLinkWithParams(['page' => $i]) ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <!-- Right ellipsis -->
    <?php if ($end < $totalPages - 1): ?>
        <span class="ellipsis">…</span>
    <?php endif; ?>

    <!-- Last page -->
    <?php if ($totalPages > 1): ?>
        <?php if ($currentPageNum == $totalPages): ?>
            <span class="page current"><?= $totalPages ?></span>
        <?php else: ?>
            <a class="page" href="<?= buildLinkWithParams(['page' => $totalPages]) ?>"><?= $totalPages ?></a>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Next -->
    <?php if ($currentPageNum < $totalPages): ?>
        <a class="next" href="<?= buildLinkWithParams(['page' => $currentPageNum + 1]) ?>">Next &gt;</a>
    <?php endif; ?>
</div>