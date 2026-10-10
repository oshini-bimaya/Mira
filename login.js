(() => {

    const dbLoginForm = document.getElementById("loginForm");
    const dbPassword = document.getElementById("password");
    const dbShowPassword = document.getElementById("showPassword");
    const dbLoginStatus = document.getElementById("loginStatus");

    // Show / Hide password
    dbShowPassword.addEventListener("click", function () {

        const show = dbPassword.type === "password";

        dbPassword.type = show ? "text" : "password";
        dbShowPassword.textContent = show ? "Hide" : "Show";
    });


    // Database Login
    dbLoginForm.addEventListener("submit", async function (event) {

        event.preventDefault();

        dbLoginStatus.textContent = "Logging in...";

        const formData = new FormData(dbLoginForm);

        try {

            const response = await fetch("login.php", {
                method: "POST",
                body: formData
            });

            const data = await response.json();

            dbLoginStatus.textContent = data.message;

            if (data.success) {

    console.log("LOGIN SUCCESS");
    console.log("Redirecting to user-home.php");

    window.location.replace(
        "/Mira%20copy/user-home.php"
    );
}

        } catch (error) {

            console.error("Login error:", error);

            dbLoginStatus.textContent =
                "Unable to login. Please try again.";
        }

    });

})();