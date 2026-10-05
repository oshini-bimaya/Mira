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

signupForm.addEventListener("submit", async function (event) {
    event.preventDefault();

    checkPasswords();

    if (!signupForm.checkValidity()) {
        signupForm.reportValidity();
        return;
    }

    signupStatus.textContent = "Creating account...";

    const formData = new FormData(signupForm);

    try {
        const response = await fetch("signup.php", {
            method: "POST",
            body: formData
        });

        const data = await response.json();

        signupStatus.textContent = data.message;

        if (data.success) {
            signupForm.reset();

            setTimeout(function () {
                window.location.href = "login.html";
            }, 1500);
        }

    } catch (error) {
        console.error("Signup error:", error);
        signupStatus.textContent = "Error: " + error.message;
    }
}); 