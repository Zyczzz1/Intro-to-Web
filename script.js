document.addEventListener("DOMContentLoaded", function() {
    
    // 1. CHECK FOR LOGIN ERRORS (From PHP Redirects)
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');

    if (error === 'incorrect') {
        alert("Incorrect Password! Please try again.");
        // Clear the error from URL so it doesn't keep popping up on refresh
        window.history.replaceState(null, null, window.location.pathname);
    } 
    else if (error === 'notfound') {
        alert("Email not registered. Please Sign Up.");
        window.history.replaceState(null, null, window.location.pathname);
    }

    // 2. HOMEPAGE RESTRICTED LINKS (Optional UI logic)
    const restrictedLinks = document.querySelectorAll(".restricted-link");
    restrictedLinks.forEach(link => {
        link.addEventListener("click", function(event) {
            event.preventDefault(); 
            window.location.href = "login.html";
        });
    });

    console.log("System loaded.");
});