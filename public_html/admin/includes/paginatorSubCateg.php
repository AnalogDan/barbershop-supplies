<style>
    .pagination {
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

    .page {
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

    .next {
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

    .page:hover,
    .next:hover {
        color: black;
    }

    .page.current {
        background-color: #ddd;
        color: black;
    }

    @media (max-width: 768px) {

        .pagination {
            gap: 4px;
            flex-wrap: wrap;
            margin-top: 15px;
            margin-bottom: 30px;
        }

        .page {
            width: 32px;
            height: 32px;
            font-size: 13px;
        }

        .prev,
        .next {
            width: auto;
            min-width: 50px;
            padding: 0 6px;
            font-size: 14px;
        }

        .dots {
            font-size: 14px;
        }
    }
</style>

<div class="pagination">
    <?php if ($currentPage2 > 1): ?>
        <a class="next" href="?sub_page=<?= $currentPage2 - 1 ?>&grid=sub<?= $searchQuery ? '&query=' . urlencode($searchQuery) : '' ?>">
            &lt; Prev
        </a>
    <?php endif; ?>
    <?php
    $window = 2;
    $start = max(1, $currentPage2 - $window);
    $end   = min($totalPages2, $currentPage2 + $window);
    if ($start > 1) {
        echo '<a class="page" href="?sub_page=1&grid=sub' .
            ($searchQuery ? '&query=' . urlencode($searchQuery) : '') .
            '">1</a>';

        if ($start > 2) {
            echo '<span class="dots">...</span>';
        }
    }
    for ($i = $start; $i <= $end; $i++) {
        if ($i == $currentPage2) {
            echo '<span class="page current">' . $i . '</span>';
        } else {
            echo '<a class="page" href="?sub_page=' . $i .
                '&grid=sub' .
                ($searchQuery ? '&query=' . urlencode($searchQuery) : '') .
                '">' . $i . '</a>';
        }
    }
    if ($end < $totalPages2) {
        if ($end < $totalPages2 - 1) {
            echo '<span class="dots">...</span>';
        }
        echo '<a class="page" href="?sub_page=' . $totalPages2 .
            '&grid=sub' .
            ($searchQuery ? '&query=' . urlencode($searchQuery) : '') .
            '">' . $totalPages2 . '</a>';
    }
    ?>
    <?php if ($currentPage2 < $totalPages2): ?>
        <a class="next" href="?sub_page=<?= $currentPage2 + 1 ?>&grid=sub<?= $searchQuery ? '&query=' . urlencode($searchQuery) : '' ?>">
            Next &gt;
        </a>
    <?php endif; ?>
</div>