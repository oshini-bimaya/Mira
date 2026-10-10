<?php

session_start();

// Prevent users from opening this page without logging in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}

// Never display the student homepage to an administrator.
// This also corrects accidental redirects caused by old links or cached scripts.
if (strtoupper(trim((string)($_SESSION["role"] ?? ""))) === "ADMIN") {
    header("Location: admin/dashboard.php", true, 302);
    exit();
}

$userName = $_SESSION["full_name"] ?? 'Artist';
require __DIR__ . '/db.php';
require __DIR__ . '/gallery-common.php';
$gallery = miraApprovedArtworks($conn, '', 0, 60);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MIRA | Home</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="gallery.css">
    <script src="theme.js" defer></script>
</head>

<body class="user-home-page">

    <!-- =========================
         LIQUID GLASS NAVBAR
    ========================== -->

    <header class="user-navbar">

        <!-- Logo -->
        <a href="user-home.php" class="user-nav-logo">
            <img src="./Assets/Logo.jpeg" alt="MIRA Logo">
        </a>


        <!-- Center Navigation -->
        <nav class="user-nav-links">

            <a href="user-home.php" class="active">
                Home
            </a>

            <a href="gallery.php">
                 Categories
            </a>

            <a href="gallery.php">
                Gallery
            </a>

            <a href="gallery.php">
                Artists
            </a>

            <a href="gallery.php">
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

                    <a href="my-artworks.php">
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


    <!-- Approved artworks only: data comes from MySQL -->
    <section class="mira-pin-grid" id="artGrid" aria-label="Approved student artworks">
    <?php foreach ($gallery as $art): ?>
      <a class="mira-pin mira-live-pin" href="artwork-view.php?id=<?= (int)$art['artwork_id'] ?>"
         data-search="<?= miraEsc(strtolower($art['title'].' '.$art['artist_name'].' '.$art['category_name'])) ?>">
        <img loading="lazy" src="<?= miraEsc(miraArtworkImage($art['image_path'])) ?>"
             alt="<?= miraEsc($art['title']) ?>">
        <span class="mira-pin-overlay"><strong><?= miraEsc($art['title']) ?></strong>
          <small><?= miraEsc($art['artist_name']) ?> · <?= miraEsc($art['category_name']) ?></small></span>
      </a>
    <?php endforeach; ?>
    </section>
    <p id="noResults" class="mira-empty" <?= $gallery ? 'hidden' : '' ?>>
      <?= $gallery ? 'No artworks match your search.' : 'No approved artworks yet. Once an administrator approves a submission, it will appear here.' ?>
    </p>

</main>


    <script src="gallery-search.js" defer></script>
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