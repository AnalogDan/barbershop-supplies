<style>
    .pagination{
        font-weight: bold;
        display: flex;
        gap 10px;
        margin-top: 20px;
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
    <?php if ($currentPageNum > 1): ?>
        <a class="next" href="<?php echo buildLinkWithParams(['page' => $currentPageNum - 1]); ?>">&lt; Prev</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <?php if ($i == $currentPageNum): ?>
            <span class="page current"><?php echo $i; ?></span>
        <?php else: ?>
            <a class="page" href="<?php echo buildLinkWithParams(['page' => $i]); ?>"><?php echo $i; ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($currentPageNum < $totalPages): ?>
        <a class="next" href="<?php echo buildLinkWithParams(['page' => $currentPageNum + 1]); ?>">Next &gt;</a>
    <?php endif; ?>
</div>