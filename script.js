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

/* ==============================
   CATEGORY ENTRANCE ANIMATION
   ============================== */

const categoriesGrid = document.querySelector(".categories-grid");

if (categoriesGrid) {

    const cards = categoriesGrid.querySelectorAll(".category-card");

    // Prepare cards for animation
    categoriesGrid.classList.add("animate-ready");

    const observer = new IntersectionObserver(
        (entries, observer) => {

            entries.forEach(entry => {

                if (entry.isIntersecting) {

                    cards.forEach((card, index) => {

                        setTimeout(() => {
                            card.classList.add("show");
                        }, index * 250);

                    });

                    observer.unobserve(entry.target);
                }

            });

        },
        {
            threshold: 0.15
        }
    );

    observer.observe(categoriesGrid);
}