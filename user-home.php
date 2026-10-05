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

    <main class="user-main">

        <section class="user-welcome">

            <p class="welcome-small">
                MIRA CREATIVE COMMUNITY
            </p>

           <h1>
                Welcome back,
                <span><?php echo htmlspecialchars($userName); ?>.</span>
            </h1>

            <p class="welcome-description">
                Discover creativity from the NSBM community,
                share your work and connect with student artists.
            </p>


            <div class="welcome-buttons">

                <a href="upload-artwork.php"
                   class="welcome-upload">

                    + Upload Artwork

                </a>

                <a href="gallery.php"
                   class="welcome-gallery">

                    Explore Gallery →

                </a>

            </div>

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