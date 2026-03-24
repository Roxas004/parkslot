const tableBody = document.getElementById('queue-table');
let dragSrcEl = null;

tableBody.querySelectorAll('tr').forEach(tr => {
    tr.draggable = true;

    tr.addEventListener('dragstart', (e) => {
        dragSrcEl = e.currentTarget;
        e.dataTransfer.effectAllowed = 'move';
        dragSrcEl.style.backgroundColor = '#93c5fd';
        dragSrcEl.style.opacity = '0.5';
    });

    tr.addEventListener('dragend', () => {
        dragSrcEl.style.backgroundColor = '';
        dragSrcEl.style.opacity = '';
        tableBody.querySelectorAll('tr').forEach(row => {
            row.style.backgroundColor = '';
            row.style.outline = '';
        });
    });

    tr.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        tableBody.querySelectorAll('tr').forEach(row => {
            row.style.backgroundColor = '';
            row.style.outline = '';
        });
        e.currentTarget.style.backgroundColor = '#bfdbfe';
        e.currentTarget.style.outline = '2px solid #3b82f6';
    });

    tr.addEventListener('drop', async (e) => {
        e.preventDefault();
        const target = e.currentTarget;
        if (dragSrcEl === target) return;

        const response = await fetch(window.swapUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                file1: dragSrcEl.dataset.id,
                file2: target.dataset.id
            })
        });

        if (response.ok) {
            window.location.reload();
        }
    });
});
