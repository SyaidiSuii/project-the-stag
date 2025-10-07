/**
 * Form Confirmation Handler
 * Automatically converts all forms with onsubmit="return confirm(...)" to use modern modal
 */

document.addEventListener('DOMContentLoaded', function() {
    // Find all forms with inline confirm
    const forms = document.querySelectorAll('form[onsubmit*="confirm("]');

    forms.forEach(form => {
        const onsubmitAttr = form.getAttribute('onsubmit');

        // Extract confirm message using regex
        const match = onsubmitAttr.match(/confirm\(['"](.+?)['"]\)/);
        if (!match) return;

        const confirmMessage = match[1];

        // Remove the inline onsubmit attribute
        form.removeAttribute('onsubmit');

        // Add new event listener with modern confirm
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Determine if it's a delete action
            const isDelete = form.querySelector('input[name="_method"][value="DELETE"]') !== null;

            const confirmed = await showConfirm(
                isDelete ? 'Confirm Delete' : 'Confirm Action',
                confirmMessage.replace(/\\n/g, '\n'),
                isDelete ? 'danger' : 'warning',
                isDelete ? 'Delete' : 'Confirm',
                'Cancel'
            );

            if (confirmed) {
                // Submit the form
                form.submit();
            }
        });
    });
});
