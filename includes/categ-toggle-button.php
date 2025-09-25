<style>
    .categories-toggle {
        width: max-content;
        font-family: 'Old London', serif;
        font-size: 44px;
        display: inline-flex;
        align-items: center;
        cursor: pointer;
        user-select: none;
        transition: color 0.3s;
        margin: 10px 20px;
        padding: 12px 20px;
        color: black;
    }

    .chevron svg {
        transition: transform 0.3s ease;
    }

    .chevron.down svg {
        transform: rotate(90deg); 
    }
</style>

<div id="categoriesToggle" class="categories-toggle">
    Categories
    <span id="chevronIcon" class="chevron">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="butt" stroke-linejoin="miter">
            <polyline points="9 6 15 12 9 18" />
        </svg>
    </span>
</div>