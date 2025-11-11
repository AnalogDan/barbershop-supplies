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
  <a class="next" href="#">&lt; Prev</a>
  
  <a class="page" href="#">1</a>
  <a class="page" href="#">2</a>
  <span class="page current">3</span>
  <a class="page" href="#">4</a>
  <a class="page" href="#">5</a>
  
  <a class="next" href="#">Next &gt;</a>
</div>