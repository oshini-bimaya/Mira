const signupForm = document.getElementById("signupForm");
const password = document.getElementById("password");
const confirmPassword = document.getElementById("confirmPassword");
const showPassword = document.getElementById("showPassword");
const signupStatus = document.getElementById("signupStatus");

// Show or hide both passwords
showPassword.addEventListener("click", function () {
    const show = password.type === "password";

    password.type = show ? "text" : "password";
    confirmPassword.type = show ? "text" : "password";

    showPassword.textContent = show ? "Hide" : "Show";
    showPassword.setAttribute("aria-pressed", String(show));
});

// Check whether the passwords match
function checkPasswords() {
    if (password.value !== confirmPassword.value) {
        confirmPassword.setCustomValidity("Passwords do not match.");
    } else {
        confirmPassword.setCustomValidity("");
    }

    signupStatus.textContent = "";
}

password.addEventListener("input", checkPasswords);
confirmPassword.addEventListener("input", checkPasswords);

// Demo signup submission
signupForm.addEventListener("submit", function (event) {
    event.preventDefault();

    signupStatus.textContent =
        "Demo only: connect this form to your backend to create an account.";
});