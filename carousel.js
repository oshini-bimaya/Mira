const stage = document.querySelector(".mira-stage");
const cards = document.querySelectorAll(".mira-art");
const motionButton = document.getElementById("motionButton");

const reducedMotion = window.matchMedia(
    "(prefers-reduced-motion: reduce)"
);

let paused = reducedMotion.matches;
let rotation = 0;
let previousTime = null;
let animationId = null;

function drawCards() {
    const mobile = stage.clientWidth <= 600;

    // Adapt the orbit to the available width
    const radiusX = Math.min(
        mobile ? 150 : 350,
        stage.clientWidth * 0.32
    );

    const radiusZ = mobile ? 65 : 140;
    const waveHeight = mobile ? 18 : 30;

    cards.forEach(function (card, index) {
        const angle =
            rotation + (index / cards.length) * Math.PI * 2;

        const depth = Math.cos(angle);

        const x = Math.sin(angle) * radiusX;
        const y = Math.sin(angle * 2) * waveHeight;
        const z = depth * radiusZ;

        const tilt = Math.sin(angle) * -28;

        card.style.transform = `
            translate3d(${x}px, ${y}px, ${z}px)
            rotateY(${tilt}deg)
        `;

        // Cards at the back appear slightly dimmer
        card.style.filter =
            `brightness(${0.82 + ((depth + 1) / 2) * 0.18})`;
    });
}

function animate(time) {
    if (previousTime !== null) {
        const elapsed = Math.min(time - previousTime, 50);

        // One full rotation every 24 seconds
        rotation += (elapsed / 24000) * Math.PI * 2;
    }

    previousTime = time;
    drawCards();

    animationId = requestAnimationFrame(animate);
}

function updatePlayback() {
    cancelAnimationFrame(animationId);
    previousTime = null;

    motionButton.textContent = paused
        ? "Play animation"
        : "Pause animation";

    if (!paused && !document.hidden) {
        animationId = requestAnimationFrame(animate);
    }
}

motionButton.addEventListener("click", function () {
    paused = !paused;
    updatePlayback();
});

reducedMotion.addEventListener("change", function (event) {
    paused = event.matches;
    updatePlayback();
});

document.addEventListener("visibilitychange", updatePlayback);
window.addEventListener("resize", drawCards);

drawCards();
updatePlayback();