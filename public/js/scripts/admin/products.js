function handleSearch(event) {
    const searchValue = event.target.value.trim();
    const currentUrl = new URL(window.location.href);

    if (searchValue) {
        currentUrl.searchParams.set('search', searchValue);
    } else {
        currentUrl.searchParams.delete('search');
    }

    if (event.key === 'Enter') {
        window.location.href = currentUrl.toString();
    }
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes = document.getElementsByClassName('row-checkbox');

    selectAllCheckbox.checked = !selectAllCheckbox.checked;
    for (let checkbox of rowCheckboxes) {
        checkbox.checked = selectAllCheckbox.checked;
    }
}

document.getElementById('selectAll').addEventListener('change', function() {
    const rowCheckboxes = document.getElementsByClassName('row-checkbox');
    for (let checkbox of rowCheckboxes) {
        checkbox.checked = this.checked;
    }
});