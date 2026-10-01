const loginForm = document.getElementById("loginForm");
const passwordInput = document.getElementById("password");
const showPasswordButton = document.getElementById("showPassword");
const loginStatus = document.getElementById("loginStatus");

// Show or hide the password
showPasswordButton.addEventListener("click", function () {
    const showPassword = passwordInput.type === "password";

    passwordInput.type = showPassword ? "text" : "password";

    showPasswordButton.textContent = showPassword ? "Hide" : "Show";

    showPasswordButton.setAttribute(
        "aria-pressed",
        String(showPassword)
    );
});

// Demo login submission
loginForm.addEventListener("submit", function (event) {
    event.preventDefault();

    loginStatus.textContent =
        "Demo only: connect this form to your backend to log in.";
});

const categoryCards = document.querySelectorAll(".category-card");

const categoryObserver = new IntersectionObserver(
    (entries) => {

        entries.forEach((entry) => {

            if (entry.isIntersecting) {

                const cards = [...categoryCards];
                const index = cards.indexOf(entry.target);

                setTimeout(() => {
                    entry.target.classList.add("show-category");
                }, index * 150);

                categoryObserver.unobserve(entry.target);
            }

        });

    },
    {
        threshold: 0.2
    }
);

categoryCards.forEach((card) => {
    categoryObserver.observe(card);
});