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
    <span class="page current">1</span>
    <span class="page">2</span>
    <span class="page">3</span>
    <span class="page">4</span>
    <span class="next">Next ></span>
</div>