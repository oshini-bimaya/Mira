

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

/* ==============================
   MIRA FLOWER OPENING ANIMATION
   ============================== */

(() => {
    "use strict";

    const root = document.documentElement;

    const reducedMotion = window.matchMedia(
        "(prefers-reduced-motion: reduce)"
    );

    if (
        !root.classList.contains("mira-intro-pending") ||
        reducedMotion.matches
    ) {
        root.classList.remove("mira-intro-pending");
        clearTimeout(window.miraIntroFallback);
        return;
    }

    // Flower petal shapes based on your photograph
    const petals = [
        "M163 322 C148 289 150 191 173 190 C198 191 196 280 184 321 Z",
        "M186 325 C187 282 220 190 244 200 C268 215 216 300 195 331 Z",
        "M198 332 C222 292 271 225 291 244 C315 269 243 321 207 344 Z",
        "M214 347 C253 316 321 287 328 313 C343 342 264 359 217 362 Z",
        "M217 367 C260 363 333 367 329 396 C322 424 253 396 212 381 Z",
        "M207 389 C244 409 308 437 294 455 C271 478 222 432 195 403 Z",
        "M191 404 C213 435 254 492 232 507 C203 523 185 449 180 412 Z",
        "M176 414 C193 457 196 522 178 529 C150 529 150 458 158 408 Z",
        "M153 403 C150 444 119 514 101 504 C80 490 119 424 140 392 Z",
        "M137 389 C108 423 62 475 42 456 C25 433 92 394 131 378 Z",
        "M130 373 C82 395 21 413 20 387 C21 360 86 355 130 355 Z",
        "M135 346 C94 351 15 341 20 316 C27 290 99 313 144 330 Z",
        "M145 329 C111 311 35 269 52 249 C72 229 121 277 154 320 Z",
        "M155 322 C128 284 86 216 105 206 C131 190 155 274 169 318 Z"
    ];

    // Your selected flower colors
    const colors = ["#ff8a3d", "#e04b7a", "#7654bb"];

    const overlay = document.createElement("section");

    overlay.className = "mira-opening";
    overlay.setAttribute("aria-label", "Welcome to MIRA");

    overlay.innerHTML = `
        <span class="mira-opening__label">
            NSBM ART & CRAFT CAFE
        </span>

        <div
            class="mira-opening__halo"
            aria-hidden="true">
        </div>

        <svg
            class="mira-opening__art"
            viewBox="0 184 352 352"
            aria-hidden="true">

            ${petals.map((shape, index) => `
                <g class="mira-opening__petal">
                    <path
                        d="${shape}"
                        fill="${colors[index % colors.length]}"
                        stroke="#7654bb"
                        stroke-width="2">
                    </path>
                </g>
            `).join("")}

            <g class="mira-opening__seed">
                <circle
                    cx="176"
                    cy="360"
                    r="44"
                    fill="#ff8a3d"
                    stroke="#7654bb"
                    stroke-width="3">
                </circle>

                <circle
                    cx="176"
                    cy="360"
                    r="35"
                    fill="none"
                    stroke="#e04b7a"
                    stroke-width="1">
                </circle>

                <circle
                    cx="176"
                    cy="360"
                    r="25"
                    fill="none"
                    stroke="#e04b7a"
                    stroke-width="1">
                </circle>
            </g>
        </svg>

        <div class="mira-opening__copy">
            <h2 class="mira-opening__title">MIRA</h2>

            <p class="mira-opening__tagline">
                Where student creativity comes to life
            </p>
        </div>

        <button
            class="mira-opening__skip"
            type="button">
            Skip intro ↗
        </button>
    `;

    document.body.appendChild(overlay);

    const siblings = [...document.body.children].filter(
        element => element !== overlay
    );

    const previousInert = siblings.map(
        element => element.inert
    );

    siblings.forEach(element => {
        element.inert = true;
    });

    const animations = [];

    let finished = false;
    let safetyTimer;

    function animate(element, frames, options) {
        const animation = element.animate(frames, {
            fill: "both",
            ...options
        });

        animations.push(animation);

        return animation;
    }

    function finish(focusHomepage = false) {
        if (finished) return;

        finished = true;

        clearTimeout(window.miraIntroFallback);
        clearTimeout(safetyTimer);

        animations.forEach(animation => {
            animation.cancel();
        });

        root.classList.remove(
            "mira-intro-pending",
            "mira-intro-running"
        );

        siblings.forEach((element, index) => {
            element.inert = previousInert[index];
        });

        overlay.remove();

        reducedMotion.removeEventListener(
            "change",
            handleMotionChange
        );

        window.removeEventListener(
            "pagehide",
            handlePageHide
        );

        if (focusHomepage) {
            document.querySelector(".header a")?.focus({
                preventScroll: true
            });
        }
    }

    function handleMotionChange() {
        if (reducedMotion.matches) {
            finish();
        }
    }

    function handlePageHide() {
        finish();
    }

    // Always restore access to the homepage.
    safetyTimer = setTimeout(() => {
        finish();
    }, 5800);

    reducedMotion.addEventListener(
        "change",
        handleMotionChange
    );

    window.addEventListener(
        "pagehide",
        handlePageHide
    );

    overlay.querySelector("button").addEventListener(
        "click",
        () => finish(true)
    );

    overlay.addEventListener("keydown", event => {
        if (event.key === "Escape") {
            finish(true);
        }
    });

    try {
        const flowerPetals = overlay.querySelectorAll(
            ".mira-opening__petal"
        );

        const flower = overlay.querySelector(
            ".mira-opening__art"
        );

        const centre = overlay.querySelector(
            ".mira-opening__seed"
        );

        const copy = overlay.querySelector(
            ".mira-opening__copy"
        );

        const halo = overlay.querySelector(
            ".mira-opening__halo"
        );

        // Background rings gently appear.
        animate(
            halo,
            [
                {
                    transform: "scale(0.7)",
                    opacity: 0
                },
                {
                    transform: "scale(1)",
                    opacity: 0.4
                }
            ],
            {
                duration: 1800,
                easing: "cubic-bezier(.2,.8,.2,1)"
            }
        );

        // Each petal blooms in sequence.
        flowerPetals.forEach((petal, index) => {
            animate(
                petal,
                [
                    {
                        transform: "rotate(-35deg) scale(0.06)",
                        opacity: 0
                    },
                    {
                        transform: "rotate(0deg) scale(1)",
                        opacity: 1
                    }
                ],
                {
                    delay: 100 + index * 48,
                    duration: 1050,
                    easing: "cubic-bezier(.16,1,.3,1)"
                }
            );
        });

        // Reveal the flower centre.
        animate(
            centre,
            [
                { transform: "scale(0)" },
                { transform: "scale(1)" }
            ],
            {
                duration: 1000,
                easing: "cubic-bezier(.16,1,.3,1)"
            }
        );

        // Reveal the MIRA title.
        animate(
            copy,
            [
                {
                    opacity: 0,
                    transform: "translateY(18px)"
                },
                {
                    opacity: 1,
                    transform: "translateY(0)"
                }
            ],
            {
                delay: 800,
                duration: 900,
                easing: "ease-out"
            }
        );

        // Expand the flower into the homepage transition.
        animate(
            flower,
            [
                {
                    transform: "rotate(0deg) scale(1)",
                    opacity: 1
                },
                {
                    transform: "rotate(35deg) scale(8)",
                    opacity: 0
                }
            ],
            {
                fill: "forwards",
                delay: 2400,
                duration: 1450,
                easing: "cubic-bezier(.65,0,.2,1)"
            }
        );

        animate(
            copy,
            [
                { opacity: 1 },
                {
                    opacity: 0,
                    transform: "translateY(-20px)"
                }
            ],
            {
                fill: "forwards",
                delay: 2450,
                duration: 450,
                easing: "ease-in"
            }
        );

        animate(
            halo,
            [
                { opacity: 0.4 },
                { opacity: 0 }
            ],
            {
                fill: "forwards",
                delay: 2450,
                duration: 500
            }
        );

        // Make the homepage visible behind the opening.
        root.classList.remove("mira-intro-pending");
        root.classList.add("mira-intro-running");

        const reveal = animate(
            overlay,
            [
                { opacity: 1 },
                { opacity: 0 }
            ],
            {
                fill: "forwards",
                delay: 3050,
                duration: 850,
                easing: "ease-in-out"
            }
        );

        reveal.finished
            .then(() => finish())
            .catch(() => {});
    } catch {
        finish();
    }
})();