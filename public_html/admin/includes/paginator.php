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

<div class="pagination">
    <?php if ($currentPage > 1): ?>
        <a class="prev" href="?main_page=<?= $currentPage - 1 ?><?= $searchQuery ? '&query=' . urlencode($searchQuery) : '' ?>">&lt; Prev</a>
    <?php endif; ?>

    <?php
    $window = 2;

    $start = max(1, $currentPage - $window);
    $end   = min($totalPages, $currentPage + $window);

    if ($start > 1) {
        echo '<a class="page" href="?main_page=1' . ($searchQuery ? '&query=' . urlencode($searchQuery) : '') . '">1</a>';
        if ($start > 2) echo '<span class="dots">…</span>';
    }

    for ($i = $start; $i <= $end; $i++) {
        if ($i == $currentPage) {
            echo '<span class="page current">' . $i . '</span>';
        } else {
            echo '<a class="page" href="?main_page=' . $i . ($searchQuery ? '&query=' . urlencode($searchQuery) : '') . '">' . $i . '</a>';
        }
    }

    if ($end < $totalPages) {
        if ($end < $totalPages - 1) echo '<span class="dots">…</span>';
        echo '<a class="page" href="?main_page=' . $totalPages . ($searchQuery ? '&query=' . urlencode($searchQuery) : '') . '">' . $totalPages . '</a>';
    }
    ?>

    <?php if ($currentPage < $totalPages): ?>
        <a class="next" href="?main_page=<?= $currentPage + 1 ?><?= $searchQuery ? '&query=' . urlencode($searchQuery) : '' ?>">Next &gt;</a>
    <?php endif; ?>
</div>