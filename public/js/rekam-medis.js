function updateStatus(checkbox, id) {
    const label = checkbox.closest('label');
    const statusText = label.querySelector('.status-text');
    const newStatus = checkbox.checked ? 'close' : 'progress';
    const oldStatus = checkbox.checked ? 'progress' : 'close';

    // Optimistically update UI
    label.dataset.status = newStatus;
    statusText.textContent = newStatus === 'close' ? 'Close' : 'Progress';

    fetch(`/rekam-medis/${id}/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Add success feedback
            label.classList.add('update-success');
            setTimeout(() => label.classList.remove('update-success'), 1000);
        } else {
            throw new Error(data.message || 'Failed to update status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Revert the UI changes on error
        checkbox.checked = !checkbox.checked;
        label.dataset.status = oldStatus;
        statusText.textContent = oldStatus === 'close' ? 'Close' : 'Progress';

        // Show error feedback
        label.classList.add('update-error');
        setTimeout(() => label.classList.remove('update-error'), 1000);
    });
}