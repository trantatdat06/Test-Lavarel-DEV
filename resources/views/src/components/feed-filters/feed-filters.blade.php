<style>
    .feed-filters-container { display: flex; align-items: center; gap: 12px; font-family: inherit; }
    .filter-label { font-weight: 700; color: #1c1e21; font-size: 14px; }
    .filter-list { display: flex; gap: 8px; overflow-x: auto; scrollbar-width: none; }
    .filter-btn { 
        background: #fff; border: 1px solid #e4e6eb; padding: 6px 16px; 
        border-radius: 20px; font-size: 14px; font-weight: 600; color: #1c1e21; 
        cursor: pointer; white-space: nowrap; transition: 0.2s;
    }
    .filter-btn:hover { background: #f2f2f2; }
    .filter-btn.active { background: #1877f2; color: #fff; border-color: #1877f2; }
</style>

<div class="feed-filters-container">
    <span class="filter-label">Lọc:</span>
    <div class="filter-list">
        <button class="filter-btn active">Tất cả</button>
        <button class="filter-btn">Thành tựu</button>
        <button class="filter-btn">Dự án môn học</button>
        <button class="filter-btn">Bài tập lớn</button>
    </div>
</div>