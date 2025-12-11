/* notifications_script.js */

// 1. Toggle the dropdown when the profile icon is clicked
function toggleDropdown() {
    var dropdown = document.getElementById("userDropdown");
    dropdown.classList.toggle("show");
}

// 2. Close the dropdown if the user clicks ANYWHERE outside of it
window.onclick = function(event) {
    // Check if the click target is NOT the user profile section
    if (!event.target.closest('.user-profile')) {
        var dropdowns = document.getElementsByClassName("dropdown-menu");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            // If it is open, close it
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}