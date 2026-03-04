// utils/loadComponent.js
async function loadComponent(elementId, filePath) {
    try {
        const response = await fetch(filePath);
        if (!response.ok) {
            throw new Error(`Lỗi HTTP! Trạng thái: ${response.status}`);
        }
        const html = await response.text();
        document.getElementById(elementId).innerHTML = html;
    } catch (error) {
        console.error(`Lỗi khi tải component từ ${filePath}:`, error);
    }
}
