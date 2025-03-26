// FORGOT PASSWORD PAGE
const pages = document.querySelectorAll('.page');
const dotsContainer = document.querySelector('.dots');
let currentPage = 0;

function updateDots() {
    // Remove all existing dots
    dotsContainer.innerHTML = '';

    // Create dots based on the number of pages
    for (let i = 0; i < pages.length; i++) {
        const dot = document.createElement('li');
        dotsContainer.appendChild(dot);
    }

    // Add the 'active' class to the dot corresponding to the current page
    const newDots = document.querySelectorAll('.dots li');
    newDots[currentPage].classList.add('active');
}

function updatePage() {
    // Hide all pages
    pages.forEach(page => page.classList.remove('active'));

    // Show the current page
    pages[currentPage].classList.add('active');

    // Update the dots based on the current page
    updateDots();
}

function nextPage() {
    if (currentPage < pages.length - 1) {
        currentPage++;
        updatePage();
    }
}

function prevPage() {
    if (currentPage > 0) {
        currentPage--;
        updatePage();
    }
}

// Initialize the page and dots when the page loads
updatePage();


//--------------MODAL FUNCTIONALITY-----------
// Function to display the modal when Reset Password is clicked
function showModal() {
    var modal = document.getElementById("successModal");
    modal.style.display = "block";
}

// Function to close the modal
function closeModal() {
    var modal = document.getElementById("successModal");
    modal.style.display = "none";
}

// Close the modal when the user clicks anywhere outside of the modal content
window.onclick = function(event) {
    var modal = document.getElementById("successModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
};

// Validation of Sign Up page
function validateForm() {
    // Clear previous popup message
    const popup = document.getElementById('popup-notification');
    popup.classList.remove('show'); // Hide if it's already visible

    // Get form values
    var username = document.getElementById('username').value;
    var email = document.getElementById('email').value;
    var name = document.getElementById('name').value;
    var contactNumber = document.getElementById('contact_number').value;
    var password = document.getElementById('password').value;
    var password2 = document.getElementById('password-2').value;

    // Check if fields are empty
    if (username === '' || email === '' || name === '' || contactNumber === '' || password === '' || password2 === '') {
        showPopup('All fields are required.');
        return false; // Prevent form submission
    }

    // Validate username
    if (username.length < 3) {
        showPopup('Username must be at least 3 characters long.');
        return false; // Prevent form submission
    }

    // Validate Email with a regex
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Simple email regex pattern
    if (!emailPattern.test(email)) {
        showPopup('Please enter a valid email address.');
        return false; // Prevent form submission
    }

    // Validate Name: Ensure it contains only letters, spaces, commas, and periods, and is at least 3 characters long
    const namePattern = /^[A-Za-z\s,\.]+$/; // Regex for validating name (letters, spaces, commas, and periods only)
    if (name.length < 3) {
        showPopup('Name must be at least 3 characters long.');
        return false; // Prevent form submission
    } else if (!namePattern.test(name)) {
        showPopup('Name can only contain letters, spaces, commas, and periods.');
        return false; // Prevent form submission
    }

    // Validate Contact Number: Check if it's a valid phone number (only digits, can start with a country code)
    const contactNumberPattern = /^[0-9]{10,15}$/; // Accepts 10-15 digit numbers
    if (!contactNumberPattern.test(contactNumber)) {
        showPopup('Please enter a valid contact number (only digits, 10-15 characters).');
        return false; // Prevent form submission
    }

    // Check if passwords are empty
    if (password === '' || password2 === '') {
        showPopup('Both password fields are required.');
        return false; // Prevent form submission
    }

    // Check the length of password
    if (password.length < 3) {
        showPopup('Password must be at least 3 characters long.');
        return false; // Prevent form submission
    }

    // Check if passwords match
    if (password !== password2) {
        showPopup('Passwords do not match.');
        return false; // Prevent form submission
    }

    return true; // Allow form submission
}

// Helper function to display the popup notification
function showPopup(message) {
    const popup = document.getElementById('popup-notification');
    popup.innerHTML = message;
    popup.classList.add('show'); // Show the notification
    setTimeout(() => { popup.classList.remove('show'); }, 3000); // Hide after 3 seconds
}



  //----------eye toggle function to view the password----------------
  // Toggle visibility for the first password field
  const togglePassword = document.getElementById('togglePassword');
  const passwordInput = document.getElementById('password');

  togglePassword.addEventListener('click', () => {
  const icon = togglePassword.querySelector('i');
  const type = passwordInput.type === 'password' ? 'text' : 'password';
  passwordInput.type = type;

  // Toggle the icon
  icon.classList.toggle('fa-eye-slash');
  icon.classList.toggle('fa-eye');
  });

  // Toggle visibility for the second password field
  const togglePassword2 = document.getElementById('togglePassword2');
  const passwordInput2 = document.getElementById('password-2');

  togglePassword2.addEventListener('click', () => {
  const icon = togglePassword2.querySelector('i');
  const type = passwordInput2.type === 'password' ? 'text' : 'password';
  passwordInput2.type = type;

  // Toggle the icon
  icon.classList.toggle('fa-eye-slash');
  icon.classList.toggle('fa-eye');
  });


  //------------FORGET PASSWORD----------//











  


