// Show the modal
function showModal(modalId, redirectUrl) {
    var modal = document.getElementById(modalId);
    modal.style.display = 'block';

    // Close the modal when clicking the close button
    document.getElementById('closeBtn').onclick = function() {
        modal.style.display = 'none';
        window.location.href = redirectUrl; // Redirect after closing
    }

    // Close the modal when clicking the OK button
    document.getElementById('modalOkBtn').onclick = function() {
        modal.style.display = 'none';
        window.location.href = redirectUrl; // Redirect after closing
    }

    // Close the modal if the user clicks anywhere outside of it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
}
