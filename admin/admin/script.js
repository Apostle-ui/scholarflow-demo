document.addEventListener('DOMContentLoaded', function() {
    const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');

    allSideMenu.forEach(item => {
        const li = item.parentElement;

        item.addEventListener('click', function () {
            allSideMenu.forEach(i => {
                i.parentElement.classList.remove('active');
            });
            li.classList.add('active');
        });
    });

    document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
        toggle.addEventListener('click', function (e) {
            e.preventDefault(); // Prevent default anchor behavior
            const dropdownMenu = this.nextElementSibling;

            // Toggle dropdown visibility
            if (dropdownMenu.style.display === 'block') {
                dropdownMenu.style.display = 'none';
            } else {
                // Close all other dropdowns
                document.querySelectorAll('.dropdown-menu').forEach(menu => menu.style.display = 'none');
                dropdownMenu.style.display = 'block';
            }
        });
    });

    

    // TOGGLE SIDEBAR
    const menuBar = document.querySelector('#content nav .bx.bx-menu');
    const sidebar = document.getElementById('sidebar');

    menuBar.addEventListener('click', function () {
        sidebar.classList.toggle('hide');
    });

    const searchButton = document.querySelector('#content nav form .form-input button');
    const searchButtonIcon = document.querySelector('#content nav form .form-input button .bx');
    const searchForm = document.querySelector('#content nav form');

    searchButton.addEventListener('click', function (e) {
        if (window.innerWidth < 576) {
            e.preventDefault();
            searchForm.classList.toggle('show');
            if (searchForm.classList.contains('show')) {
                searchButtonIcon.classList.replace('bx-search', 'bx-x');
            } else {
                searchButtonIcon.classList.replace('bx-x', 'bx-search');
            }
        }
    });

    if (window.innerWidth < 768) {
        sidebar.classList.add('hide');
    } else if (window.innerWidth > 576) {
        searchButtonIcon.classList.replace('bx-x', 'bx-search');
        searchForm.classList.remove('show');
    }

    window.addEventListener('resize', function () {
        if (this.innerWidth > 576) {
            searchButtonIcon.classList.replace('bx-x', 'bx-search');
            searchForm.classList.remove('show');
        }
    });

    // Get the checkbox and body element
const switchMode = document.getElementById('switch-mode');
const body = document.body;

// Check if dark mode is already stored in localStorage
if (localStorage.getItem('darkMode') === 'enabled') {
    body.classList.add('dark');
    switchMode.checked = true; // Keep the checkbox checked
}

// Event listener for the checkbox change
switchMode.addEventListener('change', function () {
    // Toggle the dark mode class on body
    body.classList.toggle('dark', this.checked);
    
    // Save the dark mode state to localStorage
    if (this.checked) {
        localStorage.setItem('darkMode', 'enabled');
    } else {
        localStorage.setItem('darkMode', 'disabled');
    }
});


    
});



// Close modal on pressing the Esc key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') { // Check if the Escape key was pressed
        var addModals = [
            document.getElementById('addModal'),
            document.getElementById('addAdminModal')
        ];
        var editModals = [
            document.getElementById('editUserModal'),
            document.getElementById('editOrgModal'),
            document.getElementById('editAdminModal')
        ];
        var deleteModal = document.getElementById('deleteModal');

        // Check and close add modals
        addModals.forEach(function(modal) {
            if (modal && modal.style.display === "block") {
                // Call the corresponding close function
                if (modal.id === 'addModal') closeUserAddModal();
                if (modal.id === 'addAdminModal') closeAdminAddModal();
                if (modal.id === 'addApplicantsModal') closeApplicantAddModal();
            }
        });

        // Check and close edit modals
        editModals.forEach(function(modal) {
            if (modal && modal.style.display === "block") {
                // Call the corresponding close function
                if (modal.id === 'editUserModal') closeUserEditModal();
                if (modal.id === 'editOrgModal') closeOrganizerEditModal();
                if (modal.id === 'editAdminModal') closeAdminEditModal();
                if (modal.id === 'editApplicantsModal') closeApplicantEditModal();
            }
        });

        // Check and close delete modal
        if (deleteModal && deleteModal.style.display === "block") {
            closeUserDeleteModal();
            closeOrganizerDeleteModal();
            closeAdminDeleteModal();
            closeApplicantDeleteModal();
        }
    }
});

// -----------------------------------ACCOUNTS------------------------------------------//
function openAccountAddModal() {
    document.getElementById('addModal').style.display = "block";
}

function closeAccountAddModal() {
    document.getElementById('addModal').style.display = "none";
}

function openAccountEditModal(id, username, email, password, name, contactNumber, role) {
    document.getElementById('account_id').value = id;
    document.getElementById('username').value = username;
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
    document.getElementById('name').value = name;
    document.getElementById('contact_number').value = contactNumber;
    

    const roleDropdown = document.getElementById('role');
    roleDropdown.value = role;

    document.getElementById('editAccountModal').style.display = "block";
}

function closeAccountEditModal() {
    document.getElementById('editAccountModal').style.display = "none";
}


function openAccountDeleteModal(id) {
    document.getElementById('delete_id').value = id;
    document.getElementById('deleteModal').style.display = "block";
}

function closeAccountDeleteModal() {
    document.getElementById('deleteModal').style.display = "none";
}

// VALIDATION FOR ADDING ACCOUNTS
function validateAddAccount(event) {
    event.preventDefault(); // Prevent the form from submitting

    const username = document.getElementById('add_username').value.trim();
    const email = document.getElementById('add_email').value.trim();
    const password = document.getElementById('add_password').value;
    const name = document.getElementById('add_name').value.trim();
    const contactNumber = document.getElementById('add_contact_number').value.trim();
    const role = document.getElementById('add_role').value;

    const usernamePattern = /^[a-zA-Z0-9_]{3,20}$/; // Username pattern
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/; // Email pattern
    const namePattern = /^[a-zA-Z\s]{2,50}$/; // Name pattern
    const contactNumberPattern = /^[0-9]{11}$/; // Contact number pattern

    // Validation logic
    if (!username || !email || !password || !name || !contactNumber || !role) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Please fill out all required fields.',
        });
        return false;
    }

    if (!usernamePattern.test(username)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Username',
            text: 'Username must be 3-20 characters long and can only contain letters, numbers, and underscores.',
        });
        return false;
    }

    if (!emailPattern.test(email)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Email',
            text: 'Please enter a valid email address.',
        });
        return false;
    }

    if (password.length < 6) {
        Swal.fire({
            icon: 'error',
            title: 'Weak Password',
            text: 'Password must be at least 6 characters long.',
        });
        return false;
    }

    if (!namePattern.test(name)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Name',
            text: 'Name must be 2-50 characters long and can only contain letters and spaces.',
        });
        return false;
    }

    if (!contactNumberPattern.test(contactNumber)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Contact Number',
            text: 'Contact number must be 11 digits long.',
        });
        return false;
    }

    if (role === "") {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Role',
            text: 'Please select a valid role.',
        });
        return false;
    }

    // Success notification
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: 'Account information added successfully!',
    }).then(() => {
        // Submit the form after the user acknowledges the success message
        document.getElementById('addForm').submit();
    });
}

// VALIDATION FOR EDITING ACCOUNTS
function validateEditAccount(event) {
    event.preventDefault(); // Prevent the form from submitting

    const username = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    const name = document.getElementById('name').value.trim();
    const contactNumber = document.getElementById('contact_number').value.trim();
    const role = document.getElementById('role').value;

    const usernamePattern = /^[a-zA-Z0-9_]{3,20}$/; // Username should be 3-20 characters long, alphanumeric or underscore
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/; // Email validation pattern
    const contactPattern = /^[0-9]{11}$/; // Contact number validation pattern (example for phone numbers)

    // Check if all required fields are filled
    if (!username || !email || !password || !name || !contactNumber || !role) {
        Swal.fire({
            icon: 'error',
            title: 'Missing Information',
            text: 'Please fill out all required fields.',
        });
        return false;
    }

    // Validate username
    if (!usernamePattern.test(username)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Username',
            text: 'Username must be 3-20 characters long and can only contain letters, numbers, and underscores.',
        });
        return false;
    }

    // Validate email
    if (!emailPattern.test(email)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Email',
            text: 'Please enter a valid email address.',
        });
        return false;
    }

    // Validate password
    if (password.length < 6) {
        Swal.fire({
            icon: 'error',
            title: 'Weak Password',
            text: 'Password must be at least 6 characters long.',
        });
        return false;
    }

    // Validate contact number
    if (!contactPattern.test(contactNumber)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Contact Number',
            text: 'Contact number must be 11 digits long.',
        });
        return false;
    }

    // Success notification
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: 'Account information updated successfully!',
    }).then(() => {
        // Submit the form after the user acknowledges the success message
        document.getElementById('editForm').submit();
    });
}
// -----------------------------------END OF ACCOUNTS-----------------------------------//




//-----------------------------------LIST OF APPLICANTS-----------------------------------//

window.onclick = function(event) {
    var addModal = document.getElementById('addApplicantsModal');
    var editModal = document.getElementById('editApplicantsModal');
    var deleteModal = document.getElementById('deleteModal');
    
    // Close add modal if clicked outside
    if (event.target === addModal) {
        closeApplicantAddModal();
    }

    // Close edit modal if clicked outside
    if (event.target === editModal) {
        closeApplicantEditModal();
    }

    // Close delete modal if clicked outside (on the modal wrapper, not the content)
    if (event.target === deleteModal) {
        closeApplicantDeleteModal();
    }
};

function confirmAccept(event) {
    // Show the SweetAlert2 confirmation dialog
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you really want to accept this applicant?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, accept it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: false, // Default value: Yes on the left, No on the right
    }).then((result) => {
        if (result.isConfirmed) {
            // If the user confirms, submit the form
            event.target.closest('form').submit();
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            // If the user cancels by clicking "No"
            Swal.fire({
                title: 'Cancelled',
                text: 'The acceptance action has been cancelled.',
                icon: 'info',
                timer: 1500,
                showConfirmButton: false,
            });
        }
    });
}

function confirmReject(event) {
    // Show the SweetAlert2 confirmation dialog
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you really want to reject this applicant?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, reject it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: false,
    }).then((result) => {
        if (result.isConfirmed) {
            event.target.closest('form').submit();
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire({
                title: 'Cancelled',
                text: 'The rejection action has been cancelled.',
                icon: 'info',
                timer: 1500,
                showConfirmButton: false,
            });
        } 
    });
}

function confirmDownload(event) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you really want to download files uploaded by this applicant?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, download it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: false,
    }).then((result) => {
        if (result.isConfirmed) {
            event.target.closest('form').submit();
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire({
                title: 'Cancelled',
                text: 'The download action has been cancelled.',
                icon: 'info',
                timer: 1500,
                showConfirmButton: false,
            });
        }
    });
}

function confirmScan(event) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you really want to scan the files uploaded by this applicant?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, scan it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: false,
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit the form after confirmation
            event.target.closest('form').submit();
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire({
                title: 'Cancelled',
                text: 'The scan action has been cancelled.',
                icon: 'info',
                timer: 1500,
                showConfirmButton: false,
            });
        }
    });
}


function confirmFullScholar(event) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you really want to accept this applicant as a Full Scholar?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, accept as Full Scholar!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: false, // Yes on the left, No on the right
    }).then((result) => {
        if (result.isConfirmed) {
            // If the user confirms, submit the form
            event.target.closest('form').submit();
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire({
                title: 'Cancelled',
                text: 'The action has been cancelled.',
                icon: 'info',
                timer: 1500,
                showConfirmButton: false,
            });
        }
    });
}

function confirmHalfScholar(event) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you really want to accept this applicant as a Half Scholar?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, accept as Half Scholar!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: false,
    }).then((result) => {
        if (result.isConfirmed) {
            event.target.closest('form').submit();
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire({
                title: 'Cancelled',
                text: 'The action has been cancelled.',
                icon: 'info',
                timer: 1500,
                showConfirmButton: false,
            });
        }
    });
}



function applicants_confirmDownload(event) {
    event.preventDefault(); // Prevent default behavior

    Swal.fire({
        title: 'Confirm Download',
        text: 'Are you sure you want to download all applicant data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, download it!',
        cancelButtonText: 'Cancel',
        reverseButtons: false, // Default: Yes on the left, No on the right
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'download_all_files.php'; // Redirect to download
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire({
                title: 'Cancelled',
                text: 'The download has been cancelled.',
                icon: 'info',
                timer: 1500,
                showConfirmButton: false,
            });
        }
    });
}


function applicants_confirmDeletion(event) {
    event.preventDefault(); // Prevent form submission

    Swal.fire({
        title: 'Are you sure?',
        text: "This action will permanently delete all applicant data. This cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete all!',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'delete_all_applicants.php';
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire({
                title: 'Cancelled',
                text: 'No applicants were deleted.',
                icon: 'info',
                timer: 1500,
                showConfirmButton: false,
            });
        }
    });
}

function applicants_confirmApplicantDownload(event) {
    event.preventDefault(); // Prevent form submission

    Swal.fire({
        title: 'Confirm Download',
        text: 'Are you sure you want to download all applicant data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, download it!',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'download_applicants.php';
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire({
                title: 'Cancelled',
                text: 'The download has been cancelled.',
                icon: 'info',
                timer: 1500,
                showConfirmButton: false,
            });
        }
    });
}


function confirmRemove(event) {
    event.preventDefault(); // Prevent form submission

    // Show SweetAlert confirmation dialog
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you really want to remove this applicant?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, remove it!',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            // Find the form that contains the clicked button
            var form = event.target.closest('form');
            
            if (form) {
                // Explicitly submit the form
                form.submit();
            }
        } else {
            // Show cancellation message when action is canceled
            Swal.fire({
                title: 'Cancelled',
                text: 'The remove action has been canceled.',
                icon: 'info',
                timer: 1500,
                showConfirmButton: false,
            });
        }
    });
}




function updateGradeOptions() {
    const schoolLevel = document.getElementById('current-school-level').value;
    const gradeLevelDropdown = document.getElementById('grade-level');
    
    // Clear previous options
    gradeLevelDropdown.innerHTML = '<option value="" disabled selected>Please select</option>';
    
    if (schoolLevel === 'Senior High School') {
        // Add options for Senior High School
        const grades = ['Grade 11', 'Grade 12'];
        grades.forEach(grade => {
            const option = document.createElement('option');
            option.value = grade; // Use the format as it is
            option.textContent = grade;
            gradeLevelDropdown.appendChild(option);
        });
    } else if (schoolLevel === 'College') {
        // Add options for College
        const years = ['1st Year', '2nd Year', '3rd Year', '4th Year Undergraduate'];
        years.forEach(year => {
            const option = document.createElement('option');
            option.value = year; // Use the format as it is
            option.textContent = year;
            gradeLevelDropdown.appendChild(option);
        });
    }
    }

    
function updateFileName(inputId) {
      const input = document.getElementById(inputId);
      const fileNameDisplay = document.getElementById(`${inputId}-file-name`);
    
      if (input.files.length > 0) {
          const fileName = input.files[0].name;
          fileNameDisplay.textContent = fileName; // Display the file name
      } else {
          fileNameDisplay.textContent = ''; // Clear the display if no file is selected
      }
    }
    

// Applicant Form
function openApplicantAddModal() {
    document.getElementById('addApplicantsModal').style.display = "block";
}

function closeApplicantAddModal() {
    document.getElementById('addApplicantsModal').style.display = "none";
    // Clear the form fields if necessary
    document.getElementById('addForm').reset(); // Ensure your add form has an id 'addForm'
}

// Function to update the year level options based on school level
function updateEditGradeOptions() {
    const schoolLevel = document.getElementById('edit_school_level').value;
    const gradeLevelDropdown = document.getElementById('edit_year_level');
    
    // Clear previous options
    gradeLevelDropdown.innerHTML = '<option value="" disabled selected>Please select</option>';
    
    if (schoolLevel === 'Senior High School') {
        // Add options for Senior High School
        const grades = ['Grade 11', 'Grade 12'];
        grades.forEach(grade => {
            const option = document.createElement('option');
            option.value = grade; // Use the format as it is
            option.textContent = grade;
            gradeLevelDropdown.appendChild(option);
        });
    } else if (schoolLevel === 'College') {
        // Add options for College
        const years = ['1st Year', '2nd Year', '3rd Year', '4th Year Undergraduate'];
        years.forEach(year => {
            const option = document.createElement('option');
            option.value = year; // Use the format as it is
            option.textContent = year;
            gradeLevelDropdown.appendChild(option);
        });
    }
}

// Function to populate the edit modal with applicant data and update the grade level options
function openApplicantEditModal(
    id, firstname, middlename, lastname, gender, birthdate, email, contact_number, street,
    mother_firstname, mother_middlename, mother_lastname, mother_contact_number, mother_birthdate,
    father_firstname, father_middlename, father_lastname, father_contact_number, father_birthdate,
    school_level, year_level, school_name, certificate_registration, school_identification // File names
) {
    document.getElementById('applicant_id').value = id;
    document.getElementById('edit_firstname').value = firstname;
    document.getElementById('edit_middlename').value = middlename;
    document.getElementById('edit_lastname').value = lastname;
    document.getElementById('edit_gender').value = gender;
    document.getElementById('edit_birthdate').value = birthdate;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_contact_number').value = contact_number;
    document.getElementById('edit_street_number').value = street;
    
    // Parent Information
    document.getElementById('edit_mother_firstname').value = mother_firstname || '';
    document.getElementById('edit_mother_middlename').value = mother_middlename || '';
    document.getElementById('edit_mother_lastname').value = mother_lastname || '';
    document.getElementById('edit_mother_contact_number').value = mother_contact_number || '';
    document.getElementById('edit_mother_birthdate').value = mother_birthdate || '';
    
    document.getElementById('edit_father_firstname').value = father_firstname || '';
    document.getElementById('edit_father_middlename').value = father_middlename || '';
    document.getElementById('edit_father_lastname').value = father_lastname || '';
    document.getElementById('edit_father_contact_number').value = father_contact_number || '';
    document.getElementById('edit_father_birthdate').value = father_birthdate || '';
    
    // School Information
    document.getElementById('edit_school_level').value = school_level || '';
    updateEditGradeOptions(); // Call the function to update grade level options based on selected school level
    document.getElementById('edit_year_level').value = year_level || ''; // Pre-select the year level
    
    document.getElementById('edit_school_name').value = school_name || '';
    
    // Set file names if they exist
    document.getElementById('certificateFileName').textContent = certificate_registration || 'No file uploaded';
    document.getElementById('schoolIdFileName').textContent = school_identification || 'No file uploaded';
    
    document.getElementById('editApplicantsModal').style.display = "block"; // Show the modal
}
    
function closeApplicantEditModal() {
    document.getElementById('editApplicantsModal').style.display = "none";
}

function openApplicantDeleteModal(id) {
    document.getElementById('delete_id').value = id;
    document.getElementById('deleteModal').style.display = "block";
}

function closeApplicantDeleteModal() {
    document.getElementById('deleteModal').style.display = "none";
}

document.addEventListener('DOMContentLoaded', function () {
    const autoEligibleSwitch = document.getElementById('auto-eligible-switch');
    const actionButtons = document.querySelectorAll('.action-buttons');

    autoEligibleSwitch.addEventListener('change', function () {
        // Use SweetAlert2 for confirmation
        Swal.fire({
            title: 'Are you sure?',
            text: 'Turning on Auto-Eligibility will scan all applicants and may take a long time.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, turn it on!',
            cancelButtonText: 'Cancel',
            reverseButtons: false, // Yes on the left, No on the right
        }).then((result) => {
            if (result.isConfirmed) {
                // Show a loading message while processing
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we process all applicants.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading(); // Show loading spinner
                    },
                });

                // Trigger the PHP script via AJAX when the switch is ON
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'scan_all.php', true); // Replace with the correct PHP script path
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        try {
                            // Parse the response from PHP (JSON)
                            const response = JSON.parse(xhr.responseText);

                            if (response.status === 'success') {
                                // Close the loading message
                                Swal.close();

                                // Show success message after processing
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'All applicants have been processed successfully.',
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                }).then(() => {
                                    // Reload the page to reflect the changes
                                    window.location.reload();
                                });
                            } else {
                                // Show error message if the response indicates failure
                                Swal.fire({
                                    title: 'Error',
                                    text: response.message || 'An error occurred while processing. Please try again.',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                });
                            }
                        } catch (e) {
                            console.error('Error parsing response: ', e);

                            // Show error message for unexpected errors
                            Swal.fire({
                                title: 'Error',
                                text: 'An error occurred while processing. Please try again later.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                            });
                        }
                    } else {
                        console.error('Error executing PHP script: ' + xhr.status);

                        // Show error message for script execution errors
                        Swal.fire({
                            title: 'Error',
                            text: 'An error occurred while executing the script. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                        });
                    }
                };

                xhr.onerror = function () {
                    console.error('AJAX request failed.');
                    Swal.fire({
                        title: 'Error',
                        text: 'Unable to process the request. Please check your connection and try again.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                    });
                };

                xhr.send();
            } else {
                // If the user cancels, display a cancellation message
                Swal.fire({
                    icon: 'info',
                    title: 'Action Canceled',
                    text: 'The action has been canceled.',
                });
                // Revert the switch to its original state
                autoEligibleSwitch.checked = !autoEligibleSwitch.checked;
            }
        });
    });
});






// Next Button
function showNextForm(formNumber) {
    // Ensure form sections are correctly targeted
    const forms = document.querySelectorAll('.form-section');
    forms.forEach(form => {
      form.style.display = 'none'; // Hide all forms
    });
  
    // Display the specified form
    const selectedForm = document.getElementById(`form${formNumber}`);
    if (selectedForm) {
      selectedForm.style.display = 'block';
    } else {
      console.error(`Form with ID "form${formNumber}" not found.`);
    }
  }


// VALIDATION FOR LIST OF APPLICANTS
function showFileName(inputId, displayId) {
	const input = document.getElementById(inputId);
	const display = document.getElementById(displayId);
	// Check if a file is selected
	if (input.files && input.files.length > 0) {
		display.textContent = input.files[0].name; // Show file name
	} else {
		display.textContent = 'No file chosen'; // Default text
	}
}

function validateForm1() {
    const firstname = document.getElementById('firstname').value.trim();
    const lastname = document.getElementById('lastname').value.trim();
    const gender = document.getElementById('gender').value;
    const birthdate = document.getElementById('birthdate').value;
    const email = document.getElementById('email').value.trim();
    const contactNumber = document.getElementById('number').value.trim();
    const streetNumber = document.getElementById('street-number').value.trim();

    const namePattern = /^[a-zA-Z\s'-]+$/;
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    const contactNumberPattern = /^[0-9]{11}$/;

    // Age validation for applicant (must be 16 years or older)
    const age = calculateAge(birthdate);
    if (age < 16) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Age',
            text: 'Applicant must be at least 16 years old.',
        });
        return false;
    }

    if (!firstname || !lastname || !gender || !birthdate || !email || !contactNumber || !streetNumber) {
        Swal.fire({
            icon: 'error',
            title: 'Missing Information',
            text: 'Please fill out all required fields in the Demographic Profile section.',
        });
        return false;
    }

    if (!namePattern.test(firstname) || !namePattern.test(lastname)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Name Format',
            text: 'Names can only contain letters, spaces, hyphens, and apostrophes.',
        });
        return false;
    }

    if (!emailPattern.test(email)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Email',
            text: 'Please enter a valid email address.',
        });
        return false;
    }

    if (!contactNumberPattern.test(contactNumber)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Contact Number',
            text: 'Contact number must be exactly 11 digits.',
        });
        return false;
    }

    document.getElementById('form1').style.display = 'none';
    document.getElementById('form2').style.display = 'block';
}

function validateForm2() {
    const motherFirstname = document.getElementById('mother-firstname').value.trim();
    const motherLastname = document.getElementById('mother-lastname').value.trim();
    const motherContact = document.getElementById('mother-contact').value.trim();
    const motherBirthdate = document.getElementById('mother-birthdate').value;
    const fatherFirstname = document.getElementById('father-firstname').value.trim();
    const fatherLastname = document.getElementById('father-lastname').value.trim();
    const fatherContact = document.getElementById('father-contact').value.trim();
    const fatherBirthdate = document.getElementById('father-birthdate').value;

    const namePattern = /^[a-zA-Z\s'-]+$/;
    const contactNumberPattern = /^[0-9]{11}$/;

    // Age validation for parents (must be 18 years or older)
    const motherAge = calculateAge(motherBirthdate);
    const fatherAge = calculateAge(fatherBirthdate);

    if (motherAge < 18 || fatherAge < 18) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Age for Parents',
            text: 'Both parents must be at least 18 years old.',
        });
        return false;
    }

    if (!motherFirstname || !motherLastname || !motherContact || !motherBirthdate ||
        !fatherFirstname || !fatherLastname || !fatherContact || !fatherBirthdate) {
        Swal.fire({
            icon: 'error',
            title: 'Missing Information',
            text: 'Please fill out all required fields in the Parent Information section.',
        });
        return false;
    }

    if (!namePattern.test(motherFirstname) || !namePattern.test(motherLastname) ||
        !namePattern.test(fatherFirstname) || !namePattern.test(fatherLastname)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Parent Name Format',
            text: 'Parent names can only contain letters, spaces, hyphens, and apostrophes.',
        });
        return false;
    }

    if (!contactNumberPattern.test(motherContact) || !contactNumberPattern.test(fatherContact)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Parent Contact Numbers',
            text: 'Parent contact numbers must be exactly 11 digits.',
        });
        return false;
    }

    document.getElementById('form2').style.display = 'none';
    document.getElementById('form3').style.display = 'block';
}

function validateForm3() {
    const schoolLevel = document.getElementById('current-school-level').value;
    const gradeLevel = document.getElementById('grade-level').value;
    const schoolName = document.getElementById('school-name').value.trim();
    const certificate = document.getElementById('certificate').files.length;
    const schoolID = document.getElementById('school-identification').files.length;

    if (!schoolLevel || !gradeLevel || !schoolName || !certificate || !schoolID) {
        Swal.fire({
            icon: 'error',
            title: 'Missing Information',
            text: 'Please fill out all required fields in the School Information section.',
        });
        return false;
    }

    Swal.fire({
        icon: 'success',
        title: 'Application Submitted',
        text: 'Your application has been submitted successfully!',
    }).then(() => {
        document.querySelector('form').submit();
    });
}


// VALIDATION FOR EDITING OF APPLICANTS
function validateEdit(event) {
    event.preventDefault(); // Prevent the form from submitting

    const firstname = document.getElementById('edit_firstname').value.trim();
    const middlename = document.getElementById('edit_middlename').value.trim();
    const lastname = document.getElementById('edit_lastname').value.trim();
    const gender = document.getElementById('edit_gender').value;
    const birthdate = document.getElementById('edit_birthdate').value;
    const email = document.getElementById('edit_email').value.trim();
    const contactNumber = document.getElementById('edit_contact_number').value.trim();
    const streetNumber = document.getElementById('edit_street_number').value.trim();

    const motherFirstname = document.getElementById('edit_mother_firstname').value.trim();
    const motherLastname = document.getElementById('edit_mother_lastname').value.trim();
    const motherContact = document.getElementById('edit_mother_contact_number').value.trim();
    const motherBirthdate = document.getElementById('edit_mother_birthdate').value;
    const fatherFirstname = document.getElementById('edit_father_firstname').value.trim();
    const fatherLastname = document.getElementById('edit_father_lastname').value.trim();
    const fatherContact = document.getElementById('edit_father_contact_number').value.trim();
    const fatherBirthdate = document.getElementById('edit_father_birthdate').value;

    const schoolLevel = document.getElementById('edit_school_level').value;
    const yearLevel = document.getElementById('edit_year_level').value;
    const schoolName = document.getElementById('edit_school_name').value.trim();
    const certificate = document.getElementById('edit_certificate_upload').files.length;
    const schoolID = document.getElementById('edit_school_id_upload').files.length;

    const namePattern = /^[a-zA-Z\s'-]+$/;
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    const contactNumberPattern = /^[0-9]{11}$/;

    // Age validation for applicant (must be 16 years or older)
    const age = calculateAge(birthdate);
    if (age < 16) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Age',
            text: 'Applicant must be at least 16 years old.',
        });
        return false;
    }

    // Age validation for parents (must be 18 years or older)
    const motherAge = calculateAge(motherBirthdate);
    const fatherAge = calculateAge(fatherBirthdate);
    if (motherAge < 18 || fatherAge < 18) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Age for Parents',
            text: 'Both parents must be at least 18 years old.',
        });
        return false;
    }

    // Check if any required fields are missing
    if (!firstname || !middlename || !lastname || !gender || !birthdate || !email || !contactNumber || !streetNumber) {
        Swal.fire({
            icon: 'error',
            title: 'Missing Information',
            text: 'Please fill out all required fields in the Applicant Information section.',
        });
        return false;
    }

    if (!motherFirstname || !motherLastname || !motherContact || !motherBirthdate || 
        !fatherFirstname || !fatherLastname || !fatherContact || !fatherBirthdate) {
        Swal.fire({
            icon: 'error',
            title: 'Missing Information',
            text: 'Please fill out all required fields in the Parent Information section.',
        });
        return false;
    }

    if (!schoolLevel || !yearLevel || !schoolName) {
        Swal.fire({
            icon: 'error',
            title: 'Missing Information',
            text: 'Please fill out all required fields in the School Information section.',
        });
        return false;
    }

    // Validate names
    if (!namePattern.test(firstname) || !namePattern.test(middlename) || !namePattern.test(lastname) || 
        !namePattern.test(motherFirstname) || !namePattern.test(motherLastname) || 
        !namePattern.test(fatherFirstname) || !namePattern.test(fatherLastname)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Name Format',
            text: 'Names can only contain letters, spaces, hyphens, and apostrophes.',
        });
        return false;
    }

    // Validate email
    if (!emailPattern.test(email)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Email',
            text: 'Please enter a valid email address.',
        });
        return false;
    }

    // Validate contact numbers
    if (!contactNumberPattern.test(contactNumber) || !contactNumberPattern.test(motherContact) || !contactNumberPattern.test(fatherContact)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Contact Number',
            text: 'Contact numbers must be exactly 11 digits.',
        });
        return false;
    }

    // Success notification
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: 'Applicant information updated successfully!',
    }).then(() => {
        document.getElementById('editForm').submit(); // Submit the form
    });
}


// Helper function to calculate age based on birthdate
function calculateAge(birthdate) {
    const birthDateObj = new Date(birthdate);
    const today = new Date();
    let age = today.getFullYear() - birthDateObj.getFullYear();
    const m = today.getMonth() - birthDateObj.getMonth();

    if (m < 0 || (m === 0 && today.getDate() < birthDateObj.getDate())) {
        age--;
    }
    return age;
}


//-----------------------------------END OF LIST OF APPLICANTS-----------------------------------//



//-----------------------------------ELLIGIBLE APPLICANTS----------------------------------------//
function confirmRemoveAllEligible(event) {
    event.preventDefault(); // Prevent the default action (form submission or link navigation)

    Swal.fire({
        title: 'Are you sure?',
        text: 'This action will remove all eligible applicants and cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove all!',
        cancelButtonText: 'Cancel',
        reverseButtons: false,
    }).then((result) => {
        if (result.isConfirmed) {
            event.target.closest('form').submit();
        } else {
            // Action canceled
            Swal.fire({
                icon: 'info',
                title: 'Action Canceled',
                text: 'No applicants were removed.',
            });
        }
    });
}

function confirmDownloadEligible(event) {
    event.preventDefault(); // Prevent the default action (form submission or link navigation)

    Swal.fire({
        title: 'Are you sure?',
        text: 'You are about to download all eligible applicant data.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Yes, download!',
        cancelButtonText: 'Cancel',
        reverseButtons: false,
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'download_eligible_data.php'; 
        } else {
            // Action canceled
            Swal.fire({
                icon: 'info',
                title: 'Action Canceled',
                text: 'No data was downloaded.',
            });
        }
    });
}


// JavaScript to handle the trash button click
document.querySelectorAll('.move-back-btn').forEach(function(button) {
    button.addEventListener('click', function() {
        const applicantId = button.getAttribute('data-id');

        // Send a POST request to move the applicant back to the demographic table
        fetch('remove_eligible.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'applicant_id=' + applicantId,
        })
        .then(response => response.text())
        .then(data => {
            if (data === "success") {
                alert('Applicant has been moved back to the demographic table.');
                location.reload(); // Refresh the page to reflect changes	
            } else {
                alert('An error occurred while processing the request.');
            }
        })
        .catch(error => {
            alert('An error occurred: ' + error);
        });
    });
});

// Close the modal when pressing the Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") { // Check if the pressed key is "Escape"
        closeInterviewModal();
        closeExaminationModal(); // Call the function to close the modal
    }
});


// Function to open the Interview Modal and set the applicant_id
function openInterviewModal(button) {
    // Get the applicant_id from the button's data-id attribute
    var applicantId = button.getAttribute('data-id');
    
    // Set the value of the hidden input field in the modal
    document.querySelector('#interviewForm input[name="applicant_id"]').value = applicantId;
    
    // Show the modal (if using style display: none)
    document.getElementById('interviewModal').style.display = 'block';
}

// Close the Interview Modal
function closeInterviewModal() {
    document.getElementById('interviewModal').style.display = 'none';
}

// You can also add a click event listener to close the modal when clicking outside of the modal content
window.onclick = function(event) {
    var interviewModal = document.getElementById('interviewModal');
    // Close add modal if clicked outside
    if (event.target === interviewModal) {
        closeInterviewModal();
    }
};



// Function to open the Interview Modal and set the applicant_id
function openExaminationModal(button) {
    // Get the applicant_id from the button's data-id attribute
    var applicantId = button.getAttribute('data-id');
    
    // Set the value of the hidden input field in the modal
    document.querySelector('#examinationForm input[name="applicant_id"]').value = applicantId;
    
    // Show the modal (if using style display: none)
    document.getElementById('examinationModal').style.display = 'block';
}

// Close the Interview Modal
function closeExaminationModal() {
    document.getElementById('examinationModal').style.display = 'none';
}

// You can also add a click event listener to close the modal when clicking outside of the modal content
window.onclick = function(event) {
    var modal = document.getElementById('examinationModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}


// Function to open the modal and populate the applicant_id
function openEditScoreModal(applicant_id) {
    // Set the applicant_id to the hidden input in the modal
    document.getElementById('edit_applicant_id').value = applicant_id;

    // Show the modal
    document.getElementById('editScoreModal').style.display = 'block';
}

// Function to close the modal
function closeEditScoreModal() {
    document.getElementById('editScoreModal').style.display = 'none';
}

// Close the modal if clicked outside of the modal content
window.onclick = function(event) {
    if (event.target == document.getElementById('editScoreModal')) {
        closeEditScoreModal();
    }
};


function confirmScanScore(event) {
    event.preventDefault(); // Prevent the default action (form submission or link navigation)

    Swal.fire({
        title: 'Are you sure?',
        text: 'You are about to scan the score of this applicant.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, scan it!',
        cancelButtonText: 'Cancel',
        reverseButtons: false,
    }).then((result) => {
        if (result.isConfirmed) {
           // Submit the form after confirmation
           event.target.closest('form').submit();
        } else {
            // Action canceled
            Swal.fire({
                icon: 'info',
                title: 'Action Canceled',
                text: 'The scanning process was not initiated.',
            });
        }
    });
}

function confirmScanAllEligible(event) {
    event.preventDefault(); // Prevent the default action (form submission or link navigation)

    Swal.fire({
        title: 'Are you sure?',
        text: 'You are about to scan all eligible applicants. This may take a long time.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, scan all!',
        cancelButtonText: 'Cancel',
        reverseButtons: false,
    }).then((result) => {
        if (result.isConfirmed) {
            event.target.closest('form').submit();
        } else {
            // Action canceled
            Swal.fire({
                icon: 'info',
                title: 'Action Canceled',
                text: 'The scanning process for all applicants was not initiated.',
            });
        }
    });
}


//-----------------------------------END OF ELIGIBLE APPLICANTS-----------------------------------//




//-----------------------------------REJECTED APPLICANTS----------------------------------------//
function confirmRemoveAllRejected(event) {
    event.preventDefault(); // Prevent the default action (form submission or link navigation)

    Swal.fire({
        title: 'Are you sure?',
        text: 'You are about to remove all rejected applicants. This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove all!',
        cancelButtonText: 'Cancel',
        reverseButtons: false,
    }).then((result) => {
        if (result.isConfirmed) {
            event.target.closest('form').submit(); // Submit the form or perform the intended action
        } else {
            // Action canceled
            Swal.fire({
                icon: 'info',
                title: 'Action Canceled',
                text: 'The rejected applicants were not removed.',
            });
        }
    });
}

function confirmDownloadRejected(event) {
    event.preventDefault(); // Prevent the default action (form submission or link navigation)

    Swal.fire({
        title: 'Are you sure?',
        text: 'You are about to download all rejected applicant data.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Yes, download!',
        cancelButtonText: 'Cancel',
        reverseButtons: false,
    }).then((result) => {
        if (result.isConfirmed) {
            event.target.closest('form').submit(); // Submit the form or perform the intended action
        } else {
            // Action canceled
            Swal.fire({
                icon: 'info',
                title: 'Action Canceled',
                text: 'The download process was not initiated.',
            });
        }
    });
}


//-----------------------------------END OF REJECTED APPLICANTS-----------------------------------//


//-----------------------------------FULL AND HALF SCHOLAR APPLICANTS--------------------------------------//
function confirmDownloadFullScholar(event) {
    event.preventDefault(); // Prevent the default action (form submission or link navigation)

    Swal.fire({
        title: 'Are you sure?',
        text: "You are about to download all full scholar applicant's data.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, download!',
        cancelButtonText: 'Cancel',
        reverseButtons: false,
    }).then((result) => {
        if (result.isConfirmed) {
            event.target.closest('form').submit(); // Proceed with the form submission
        } else {
            Swal.fire({
                icon: 'info',
                title: 'Action Canceled',
                text: 'The download action for full scholar applicants was not initiated.',
            });
        }
    });
}

function confirmRemoveAllFullScholar(event) {
    event.preventDefault(); // Prevent the default action (form submission or link navigation)

    Swal.fire({
        title: 'Are you sure?',
        text: 'You are about to remove all full scholar applicants. This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove!',
        cancelButtonText: 'Cancel',
        reverseButtons: false,
    }).then((result) => {
        if (result.isConfirmed) {
            event.target.closest('form').submit(); // Proceed with the form submission
        } else {
            Swal.fire({
                icon: 'info',
                title: 'Action Canceled',
                text: 'No full scholar applicants were removed.',
            });
        }
    });
}

function confirmDownloadHalfScholar(event) {
    event.preventDefault(); // Prevent the default action (form submission or link navigation)

    Swal.fire({
        title: 'Are you sure?',
        text: "You are about to download all half scholar applicant's data.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, download!',
        cancelButtonText: 'Cancel',
        reverseButtons: false,
    }).then((result) => {
        if (result.isConfirmed) {
            event.target.closest('form').submit(); // Proceed with the form submission
        } else {
            Swal.fire({
                icon: 'info',
                title: 'Action Canceled',
                text: 'The download action for half scholar applicants was not initiated.',
            });
        }
    });
}

function confirmRemoveAllHalfScholar(event) {
    event.preventDefault(); // Prevent the default action (form submission or link navigation)

    Swal.fire({
        title: 'Are you sure?',
        text: 'You are about to remove all half scholar applicants. This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove!',
        cancelButtonText: 'Cancel',
        reverseButtons: false,
    }).then((result) => {
        if (result.isConfirmed) {
            event.target.closest('form').submit(); // Proceed with the form submission
        } else {
            Swal.fire({
                icon: 'info',
                title: 'Action Canceled',
                text: 'No half scholar applicants were removed.',
            });
        }
    });
}


//-----------------------------------END OF FULL SCHOLAR APPLICANTS--------------------------------//

//-----------------------------------NOTIFICATIONS--------------------------------//
function confirmDeleteNotification(event) {
    event.preventDefault(); // Prevent form submission immediately

    Swal.fire({
        title: 'Are you sure?',
        text: 'You are about to delete this notification. This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: false,
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit the form after confirmation
            event.target.closest('form').submit();
        } else {
            // Action canceled
            Swal.fire({
                icon: 'info',
                title: 'Action Canceled',
                text: 'The notification was not deleted.',
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Get the dropdown elements
    const groupRecipients = document.getElementById('group_recipients');
    const individualRecipients = document.getElementById('individual_recipients');

    // Disable the group recipient dropdown when an individual recipient is selected
    individualRecipients.addEventListener('change', function() {
        if (this.value) {
            groupRecipients.disabled = true; // Disable group recipient dropdown
        } else {
            groupRecipients.disabled = false; // Enable group recipient dropdown if no individual recipient is selected
        }
    });

    // Disable the individual recipient dropdown when a group recipient is selected
    groupRecipients.addEventListener('change', function() {
        if (this.value) {
            individualRecipients.disabled = true; // Disable individual recipient dropdown
        } else {
            individualRecipients.disabled = false; // Enable individual recipient dropdown if no group recipient is selected
        }
    });
});


function showEditNotificationModal(notification_id) {
    document.getElementById('editNotificationModal_' + notification_id).style.display = 'block';
}

function closeEditNotificationModal(notification_id) {
    document.getElementById('editNotificationModal_' + notification_id).style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.querySelector('.notificationModal');
    if (event.target === modal) {
        closeEditNotificationModal(event.target.id.split('_')[1]);
    }
}

document.onkeydown = function(event) {
    if (event.key === "Escape") {
        const modal = document.querySelector('.notificationModal[style="display: block;"]');
        if (modal) {
            closeEditNotificationModal(modal.id.split('_')[1]);
        }
    }
};




//-----------------------------------END NOTIFICATIONS--------------------------------//


//-----------------------------------REPORTS--------------------------------//

function openEditScoreModal(applicant_id) {
    // Set the applicant_id in the hidden input field in the form inside the modal
    document.getElementById('applicant_id').value = applicant_id;

    // Display the modal
    document.getElementById('editScoreModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('editScoreModal').style.display = 'none';
}

//-----------------------------------END OF REPORTS--------------------------------//

