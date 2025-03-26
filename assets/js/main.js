(function() {
  "use strict";

  /**
   * Apply .scrolled class to the body as the page is scrolled down
   */
  function toggleScrolled() {
    const selectBody = document.querySelector('body');
    const selectHeader = document.querySelector('#header');
    if (!selectHeader.classList.contains('scroll-up-sticky') && !selectHeader.classList.contains('sticky-top') && !selectHeader.classList.contains('fixed-top')) return;
    window.scrollY > 100 ? selectBody.classList.add('scrolled') : selectBody.classList.remove('scrolled');
  }

  document.addEventListener('scroll', toggleScrolled);
  window.addEventListener('load', toggleScrolled);

  /**
   * Mobile nav toggle
   */
  const mobileNavToggleBtn = document.querySelector('.mobile-nav-toggle');

  function mobileNavToogle() {
    document.querySelector('body').classList.toggle('mobile-nav-active');
    mobileNavToggleBtn.classList.toggle('bi-list');
    mobileNavToggleBtn.classList.toggle('bi-x');
  }
  if (mobileNavToggleBtn) {
    mobileNavToggleBtn.addEventListener('click', mobileNavToogle);
  }

  /**
   * Hide mobile nav on same-page/hash links
   */
  document.querySelectorAll('#navmenu a').forEach(navmenu => {
    navmenu.addEventListener('click', () => {
      if (document.querySelector('.mobile-nav-active')) {
        mobileNavToogle();
      }
    });

  });

  /**
   * Toggle mobile nav dropdowns
   */
  document.querySelectorAll('.navmenu .toggle-dropdown').forEach(navmenu => {
    navmenu.addEventListener('click', function(e) {
      e.preventDefault();
      this.parentNode.classList.toggle('active');
      this.parentNode.nextElementSibling.classList.toggle('dropdown-active');
      e.stopImmediatePropagation();
    });
  });

  /**
   * Preloader
   */
  const preloader = document.querySelector('#preloader');
  if (preloader) {
    window.addEventListener('load', () => {
      preloader.remove();
    });
  }

  /**
   * Scroll top button
   */
  let scrollTop = document.querySelector('.scroll-top');

  function toggleScrollTop() {
    if (scrollTop) {
      window.scrollY > 100 ? scrollTop.classList.add('active') : scrollTop.classList.remove('active');
    }
  }
  scrollTop.addEventListener('click', (e) => {
    e.preventDefault();
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });

  window.addEventListener('load', toggleScrollTop);
  document.addEventListener('scroll', toggleScrollTop);

  /**
   * Animation on scroll function and init
   */
  function aosInit() {
    AOS.init({
      duration: 600,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    });
  }
  window.addEventListener('load', aosInit);

  /**
   * Init typed.js
   */
  const selectTyped = document.querySelector('.typed');
  if (selectTyped) {
    let typed_strings = selectTyped.getAttribute('data-typed-items');
    typed_strings = typed_strings.split(',');
    new Typed('.typed', {
      strings: typed_strings,
      loop: true,
      typeSpeed: 100,
      backSpeed: 50,
      backDelay: 2000
    });
  }

  /**
   * Initiate glightbox
   */
  const glightbox = GLightbox({
    selector: '.glightbox'
  });

  /**
   * Init isotope layout and filters
   */
  document.querySelectorAll('.isotope-layout').forEach(function(isotopeItem) {
    let layout = isotopeItem.getAttribute('data-layout') ?? 'masonry';
    let filter = isotopeItem.getAttribute('data-default-filter') ?? '*';
    let sort = isotopeItem.getAttribute('data-sort') ?? 'original-order';

    let initIsotope;
    imagesLoaded(isotopeItem.querySelector('.isotope-container'), function() {
      initIsotope = new Isotope(isotopeItem.querySelector('.isotope-container'), {
        itemSelector: '.isotope-item',
        layoutMode: layout,
        filter: filter,
        sortBy: sort
      });
    });

    isotopeItem.querySelectorAll('.isotope-filters li').forEach(function(filters) {
      filters.addEventListener('click', function() {
        isotopeItem.querySelector('.isotope-filters .filter-active').classList.remove('filter-active');
        this.classList.add('filter-active');
        initIsotope.arrange({
          filter: this.getAttribute('data-filter')
        });
        if (typeof aosInit === 'function') {
          aosInit();
        }
      }, false);
    });

  });

  /**
   * Init swiper sliders
   */
  function initSwiper() {
    document.querySelectorAll(".init-swiper").forEach(function(swiperElement) {
      let config = JSON.parse(
        swiperElement.querySelector(".swiper-config").innerHTML.trim()
      );

      if (swiperElement.classList.contains("swiper-tab")) {
        initSwiperWithCustomPagination(swiperElement, config);
      } else {
        new Swiper(swiperElement, config);
      }
    });
  }

  window.addEventListener("load", initSwiper);

  /**
   * Correct scrolling position upon page load for URLs containing hash links.
   */
  window.addEventListener('load', function(e) {
    if (window.location.hash) {
      if (document.querySelector(window.location.hash)) {
        setTimeout(() => {
          let section = document.querySelector(window.location.hash);
          let scrollMarginTop = getComputedStyle(section).scrollMarginTop;
          window.scrollTo({
            top: section.offsetTop - parseInt(scrollMarginTop),
            behavior: 'smooth'
          });
        }, 100);
      }
    }
  });

  /**
   * Navmenu Scrollspy
   */
  let navmenulinks = document.querySelectorAll('.navmenu a');

  function navmenuScrollspy() {
    navmenulinks.forEach(navmenulink => {
      if (!navmenulink.hash) return;
      let section = document.querySelector(navmenulink.hash);
      if (!section) return;
      let position = window.scrollY + 200;
      if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
        document.querySelectorAll('.navmenu a.active').forEach(link => link.classList.remove('active'));
        navmenulink.classList.add('active');
      } else {
        navmenulink.classList.remove('active');
      }
    })
  }
  window.addEventListener('load', navmenuScrollspy);
  document.addEventListener('scroll', navmenuScrollspy);

})();

//--------Modal functionality------------------//
        const modal = document.getElementById("formModal");
        const openModalBtn = document.getElementById("openModal");
        const closeModalBtn = document.querySelector(".close");

        openModalBtn.onclick = function () {
            modal.style.display = "block";
        };

        closeModalBtn.onclick = function () {
            modal.style.display = "none";
        };

        window.onclick = function (event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };
        
//------Application form functionality-----------

//----------- First Form -----------

function validateForm() {
  const firstname = document.getElementById('firstname').value.trim();
  const lastname = document.getElementById('lastname').value.trim();
  const gender = document.getElementById('gender').value;
  const birthdate = document.getElementById('birthdate').value;
  const contactNumber = document.getElementById('number').value.trim();
  const streetNumber = document.getElementById('street-number').value.trim();

  const namePattern = /^[a-zA-Z\s'-]+$/;
  const contactNumberPattern = /^[0-9]{11}$/;

  if (!firstname || !lastname || !birthdate || !contactNumber || !streetNumber) {
    showPopup("Please fill out all required fields.");
    return false;
  }

  if (!namePattern.test(firstname) || !namePattern.test(lastname)) {
    showPopup("Name can only contain letters, spaces, hyphens, and apostrophes.");
    return false;
  }

  if (gender === "") {
    showPopup("Please select a gender.");
    return false;
  }

  const today = new Date();
  const birthDateObj = new Date(birthdate);
  let age = today.getFullYear() - birthDateObj.getFullYear(); // Changed to let
  const monthDifference = today.getMonth() - birthDateObj.getMonth();

  if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthDateObj.getDate())) {
    age--; // No error now
  }

  if (age < 17) {
    showPopup("You must be at least 17 years old.");
    return false;
  }

  if (!contactNumberPattern.test(contactNumber)) {
    showPopup("Please enter a valid contact number (11 digits).");
    return false;
  }

  showNextForm(2);
  return true;
}


function showPopup(message) {
  const popup = document.getElementById('popup');
  document.getElementById('popup-message').textContent = message;
  popup.style.display = 'block';
}

function closePopup() {
  document.getElementById('popup').style.display = 'none';
}

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

function setBirthdateLimit() {
  const today = new Date();
  const maxDate = new Date(today.getFullYear() - 17, today.getMonth(), today.getDate());
  const formattedMaxDate = maxDate.toISOString().split('T')[0];
  document.getElementById('birthdate').setAttribute('max', formattedMaxDate); // Limit for the first form

   // Optional: Show a message if the user selects an invalid date
   birthdateInput.addEventListener('change', function () {
    const selectedDate = new Date(this.value);
    if (selectedDate > maxDate) {
        alert('You must be at least 17 years old!');
        this.value = ''; // Clear the invalid value
    }
});
}

// Call this function when the page loads
window.onload = function() {
  setBirthdateLimit();
  setParentBirthdateLimit(); // Call the function to set parent birthdate limits
};



//----------- Second Form ----------- 
function setParentBirthdateLimit() {
  const today = new Date();
  const maxDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate()); // Parents must be at least 18
  const formattedMaxDate = maxDate.toISOString().split('T')[0];
  document.getElementById('mother-birthdate').setAttribute('max', formattedMaxDate);
  document.getElementById('father-birthdate').setAttribute('max', formattedMaxDate);
}

function validateForm2() {
  const motherFirstname = document.getElementById('mother-firstname').value.trim();
  const motherLastname = document.getElementById('mother-lastname').value.trim();
  const motherContactNumber = document.getElementById('mother-contact').value.trim();
  const motherBirthdate = document.getElementById('mother-birthdate').value;

  const fatherFirstname = document.getElementById('father-firstname').value.trim();
  const fatherLastname = document.getElementById('father-lastname').value.trim();
  const fatherContactNumber = document.getElementById('father-contact').value.trim();
  const fatherBirthdate = document.getElementById('father-birthdate').value;

  const namePattern = /^[a-zA-Z\s'-]+$/;
  const contactNumberPattern = /^[0-9]{11}$/;

  // Check if all required fields are filled for both parents
  if (!motherFirstname || !motherLastname || !motherContactNumber || !motherBirthdate ||
      !fatherFirstname || !fatherLastname || !fatherContactNumber || !fatherBirthdate) {
      showPopup("Please fill out all required fields for both parents.");
      return false;
  }

  // Validate name fields for both parents
  if (!namePattern.test(motherFirstname) || !namePattern.test(motherLastname) ||
      !namePattern.test(fatherFirstname) || !namePattern.test(fatherLastname)) {
      showPopup("Names can only contain letters, spaces, hyphens, and apostrophes.");
      return false;
  }

  // Validate birthdates for both parents
  const today = new Date();
  const motherBirthDateObj = new Date(motherBirthdate);
  const fatherBirthDateObj = new Date(fatherBirthdate);

  let motherAge = today.getFullYear() - motherBirthDateObj.getFullYear(); // Changed to let
  let fatherAge = today.getFullYear() - fatherBirthDateObj.getFullYear(); // Changed to let

  let monthDifferenceMother = today.getMonth() - motherBirthDateObj.getMonth(); // Changed to let
  let monthDifferenceFather = today.getMonth() - fatherBirthDateObj.getMonth(); // Changed to let

  if (monthDifferenceMother < 0 || (monthDifferenceMother === 0 && today.getDate() < motherBirthDateObj.getDate())) {
      motherAge--;
  }
  if (monthDifferenceFather < 0 || (monthDifferenceFather === 0 && today.getDate() < fatherBirthDateObj.getDate())) {
      fatherAge--;
  }

  if (motherAge < 18) {
      showPopup("Mother must be at least 18 years old.");
      return false;
  }

  if (fatherAge < 18) {
      showPopup("Father must be at least 18 years old.");
      return false;
  }

  // Validate contact numbers for both parents
  if (!contactNumberPattern.test(motherContactNumber)) {
      showPopup("Please enter a valid contact number for the mother (11 digits).");
      return false;
  }

  if (!contactNumberPattern.test(fatherContactNumber)) {
      showPopup("Please enter a valid contact number for the father (11 digits).");
      return false;
  }

  // If all validations pass, proceed to the next form
  showNextForm(3); // Adjust to show the next form number as needed
  return true;
}



//----------- Third Form -----------

function validateForm3() {
  const schoolName = document.getElementById('school-name').value.trim();
  const currentSchoolLevel = document.getElementById('current-school-level').value;
  const gradeLevel = document.getElementById('grade-level').value;
  const certificate = document.getElementById('certificate').files.length;
  const schoolId = document.getElementById('school-identification').files.length;

  const namePattern = /^[a-zA-Z\s'-]+$/; 

  if (!currentSchoolLevel) {
      showPopup("Please select the current school level.");
      return false;
  }
  if (!gradeLevel) {
      showPopup("Please select the current grade/year level.");
      return false;
  }
  if (!schoolName) {
      showPopup("Please enter the school name.");
      return false;
  }
  if (!namePattern.test(schoolName)) {
      showPopup("School name can only contain letters, spaces, apostrophes, and hyphens.");
      return false;
  }
  if (certificate === 0) {
      showPopup("Please upload the Certificate of Registration.");
      return false;
  }
  if (schoolId === 0) {
      showPopup("Please upload the School ID.");
      return false;
  }
  
  return true; 
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



// ------------- Announcement modal pop up ----------------------
// Show the modal when the page loads
window.onload = function() {
  const modal = document.getElementById("modal");
  const closeModalBtn = document.getElementById("close-announcement-modal");
  const dropdown = document.querySelector('.notification-dropdown'); // Reference to the notification dropdown
  
  // Hide the notification dropdown if it is visible
  dropdown.style.display = 'none';

  // Show the modal
  modal.style.display = "flex";

  // Close the modal when the "Okay" button is clicked
  closeModalBtn.addEventListener("click", function() {
    modal.style.display = "none";
  });
}

// ------------- Notification modal pop up ----------------------
// JavaScript to toggle notification dropdown on click
document.querySelector('.notification-toggle').addEventListener('click', function(event) {
  event.preventDefault(); // Prevent the default link behavior
  
  const dropdown = document.querySelector('.notification-dropdown');
  
  // If the modal is open, do not toggle the notification dropdown
  const modal = document.getElementById("modal");
  if (modal && modal.style.display === "flex") {
    return; // Do nothing if the modal is visible
  }

  // Toggle visibility of the dropdown
  dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
});

// Optional: Close the dropdown if the user clicks anywhere outside the notification
document.addEventListener('click', function(event) {
  const dropdown = document.querySelector('.notification-dropdown');
  const notificationToggle = document.querySelector('.notification-toggle');
  
  // If the click is outside the notification or dropdown, close it
  if (!notificationToggle.contains(event.target) && !dropdown.contains(event.target)) {
    dropdown.style.display = 'none';
  }
});

// Function to calculate and display relative time (e.g., "hrs ago", "days ago")
function timeAgo(timestamp) {
  const now = new Date();
  const date = new Date(timestamp);
  const seconds = Math.floor((now - date) / 1000);
  
  const intervals = {
    year: 31536000,
    month: 2592000,
    week: 604800,
    day: 86400,
    hour: 3600,
    minute: 60,
    second: 1
  };
  
  for (const [unit, value] of Object.entries(intervals)) {
    const interval = Math.floor(seconds / value);
    if (interval >= 1) {
      return `${interval} ${unit}${interval > 1 ? 's' : ''} ago`;
    }
  }
  
  return "just now";
}

// Update timestamps on the page
document.querySelectorAll('.notification-timestamp').forEach(element => {
  const timestamp = element.getAttribute('data-timestamp');
  const relativeTime = timeAgo(new Date(timestamp));
  element.textContent = relativeTime;
});



//------------------log out functionality------------------------------
// Toggle the visibility of the user menu when the profile icon is clicked
document.getElementById('user').addEventListener('click', function(event) {
  // Prevent event bubbling to avoid triggering the document click listener
  event.stopPropagation();

  var userMenu = document.getElementById('user_menu');
  // Toggle the display of the user menu
  if (userMenu.style.display === 'none' || userMenu.style.display === '') {
    userMenu.style.display = 'block';  // Show the menu
  } else {
    userMenu.style.display = 'none';  // Hide the menu
  }
});

// Close the dropdown if clicked outside of it
window.addEventListener('click', function(event) {
  var userMenu = document.getElementById('user_menu');
  var profileIcon = document.getElementById('user');

  // Hide the menu if the click is outside the profile icon or the menu
  if (event.target !== profileIcon && !profileIcon.contains(event.target)) {
    userMenu.style.display = 'none';
  }
});

// Add the redirection to login page when the "Log In" link is clicked
document.getElementById('user_logout').addEventListener('click', function() {
  window.location.href = 'login\login.html'; // Redirect to login page
});

// Add the redirection to sign up page when the "Sign Up" link is clicked
document.getElementById('user_signup').addEventListener('click', function() {
  window.location.href = 'login\signup.html'; // Redirect to sign up page
});

// Toggle the mobile menu when the icon is clicked
document.querySelector('.mobile-nav-toggle').addEventListener('click', function() {
  document.querySelector('.navmenu').classList.toggle('mobile-nav-active');
});


