

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