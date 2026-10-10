<?php

session_start();

// Prevent users from opening this page without logging in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}

$userName = $_SESSION["full_name"];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MIRA | Home</title>

    <link rel="stylesheet" href="style.css">
    <script src="theme.js" defer></script>
</head>

<body class="user-home-page">

    <!-- =========================
         LIQUID GLASS NAVBAR
    ========================== -->

    <header class="user-navbar">

        <!-- Logo -->
        <a href="user-home.php" class="user-nav-logo">
            <img src="./Assests/Logo.jpeg" alt="MIRA Logo">
        </a>


        <!-- Center Navigation -->
        <nav class="user-nav-links">

            <a href="user-home.php" class="active">
                Home
            </a>

            <a href="categories.php">
                 Categories
            </a>

            <a href="gallery.php">
                Gallery
            </a>

            <a href="artists.php">
                Artists
            </a>

            <a href="events.php">
                Exhibitions
            </a>

        </nav>


        <!-- Right Side -->
        <div class="user-nav-actions">

            <!-- Upload Artwork -->
            <a href="upload-artwork.php" class="upload-nav-button">
                + Upload
            </a>


            <!-- Notification -->
            <button class="notification-button"
                    type="button"
                    aria-label="Notifications">

                <span>♡</span>

                <span class="notification-dot"></span>

            </button>


            <!-- Profile -->
            <div class="profile-menu">

                <button class="profile-button"
                        id="profileButton"
                        type="button">

                    <div class="profile-avatar">
                        <?php echo strtoupper(substr($userName, 0, 1)); ?>
                    </div>

                    <span class="profile-name">
                        <?php echo htmlspecialchars($userName); ?>
                    </span>

                    <span class="profile-arrow">
                        ▾
                    </span>

                </button>


                <!-- Dropdown -->
                <div class="profile-dropdown"
                     id="profileDropdown">

                    <a href="profile.php">
                        My Profile
                    </a>

                    <a href="my-artworks.php">
                        My Artworks
                    </a>

                    <a href="submissions.php">
                        Submission History
                    </a>

                    <a href="my-events.php">
                        My Exhibitions
                    </a>

                    <a href="following.php">
                        Following
                    </a>

                    <div class="dropdown-line"></div>

                    <a href="logout.php"
                       class="logout-link">
                        Log Out
                    </a>

                </div>

            </div>

        </div>

    </header>


    <!-- =========================
         HERO
    ========================== -->

    <main class="mira-feed-page">

    <!-- Search bar -->
    <div class="mira-feed-search">

        <span class="search-icon">⌕</span>

        <input
            type="text"
            id="artSearch"
            placeholder="Search artworks..."
        >

    </div>


    <!-- Pinterest-style artwork grid -->
    <section class="mira-pin-grid">


        <!-- Artwork 1 -->
        <a href="artwork-view.php?id=1"
           class="mira-pin">

            <img
                src="./Assets/painting1.jpg"
                alt="Painting"
            >

        </a>


        <!-- Artwork 2 -->
        <a href="artwork-view.php?id=2"
           class="mira-pin">

            <img
                src="./Assets/sketching1.jpg"
                alt="Sketching"
            >

        </a>


        <!-- Artwork 3 -->
        <a href="artwork-view.php?id=3"
           class="mira-pin">

            <img
                src="./Assets/photography1.jpg"
                alt="Photography"
            >

        </a>


        <!-- Artwork 4 -->
        <a href="artwork-view.php?id=4"
           class="mira-pin">

            <img
                src="./Assets/handcraft1.jpg"
                alt="Handcraft"
            >

        </a>


        <!-- Artwork 5 -->
        <a href="artwork-view.php?id=5"
           class="mira-pin">

            <img
                src="./Assets/digiart1.jpg"
                alt="Digital Art"
            >

        </a>


        <!-- Artwork 6 -->
        <a href="artwork-view.php?id=6"
           class="mira-pin">

            <img
                src="./Assets/3dart.webp"
                alt="Sculpture"
            >

        </a>


        <!-- More sample images -->
        <a href="artwork-view.php?id=7"
           class="mira-pin">

            <img
                src="./Assets/art1.jpeg"
                alt="Artwork"
            >

        </a>


        <a href="artwork-view.php?id=8"
           class="mira-pin">

            <img
                src="./Assets/art2.jpeg"
                alt="Artwork"
            >

        </a>


        <a href="artwork-view.php?id=9"
           class="mira-pin">

            <img
                src="./Assets/art3.jpeg"
                alt="Artwork"
            >

        </a>


        <a href="artwork-view.php?id=10"
           class="mira-pin">

            <img
                src="./Assets/art4.jpeg"
                alt="Artwork"
            >

        </a>


        <a href="artwork-view.php?id=11"
           class="mira-pin">

            <img
                src="./Assets/art5.jpeg"
                alt="Artwork"
            >

        </a>


        <a href="artwork-view.php?id=12"
           class="mira-pin">

            <img
                src="./Assets/art6.jpeg"
                alt="Artwork"
            >

        </a>

    </section>

</main>


    <script>

        const profileButton =
            document.getElementById("profileButton");

        const profileDropdown =
            document.getElementById("profileDropdown");


        profileButton.addEventListener("click", function () {

            profileDropdown.classList.toggle("show-profile-menu");

        });


        document.addEventListener("click", function (event) {

            if (!event.target.closest(".profile-menu")) {

                profileDropdown.classList.remove(
                    "show-profile-menu"
                );

            }

        });

    </script>

</body>
</html>