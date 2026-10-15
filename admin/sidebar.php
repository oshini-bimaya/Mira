
<?php
// Shared admin sidebar.
// $activeAdminPage identifies the current section.

$activeAdminPage = $activeAdminPage ?? '';

$displayName = $adminName ?? ($_SESSION['full_name'] ?? 'Administrator');

$initialLetter = function_exists('mb_substr')
    ? mb_substr($displayName, 0, 1)
    : substr($displayName, 0, 1);
?>

<aside class="admin-sidebar">

    <!-- MIRA Logo -->
    <a class="admin-brand"
       href="dashboard.php"
       aria-label="MIRA Admin Dashboard">

        <span class="mira-admin-brand-logo">

            <img src="../Assets/mira-logo-light.jpeg"
                 alt="MIRA"
                 class="admin-brand-img mira-logo-for-light">

            <img src="../Assets/mira-logo-dark.png"
                 alt=""
                 aria-hidden="true"
                 class="admin-brand-img mira-logo-for-dark">

        </span>

        <span class="brand-copy">
            MIRA
            <small>ADMIN STUDIO</small>
        </span>

    </a>


    <!-- Admin Navigation -->
    <nav class="admin-nav" aria-label="Admin sections">

        <!-- Dashboard -->
        <a href="dashboard.php"
           <?= $activeAdminPage === 'dashboard'
               ? 'class="active" aria-current="page"'
               : '' ?>>
            ▦ &nbsp; Dashboard
        </a>

        <!-- Artworks -->
        <a href="artworks.php"
           <?= $activeAdminPage === 'artworks'
               ? 'class="active" aria-current="page"'
               : '' ?>>
            ▤ &nbsp; Artworks
        </a>

        <!-- Categories -->
        <a href="categories.php"
           <?= $activeAdminPage === 'categories'
               ? 'class="active" aria-current="page"'
               : '' ?>>
            ◈ &nbsp; Categories
        </a>

        <!-- Exhibitions -->
        <a href="events.php"
           <?= $activeAdminPage === 'events'
               ? 'class="active" aria-current="page"'
               : '' ?>>
            ▣ &nbsp; Exhibitions
        </a>

        <!-- Featured Art -->
        <a href="featured.php"
           <?= $activeAdminPage === 'featured'
               ? 'class="active" aria-current="page"'
               : '' ?>>
            ✧ &nbsp; Featured Art
        </a>

        <!-- Students -->
        <a href="students.php"
           <?= $activeAdminPage === 'students'
               ? 'class="active" aria-current="page"'
               : '' ?>>
            ♙ &nbsp; Students
        </a>

        <!-- Reports -->
        <a href="reports.php"
           <?= $activeAdminPage === 'reports'
               ? 'class="active" aria-current="page"'
               : '' ?>>
            ▥ &nbsp; Reports
        </a>

        <!-- Admin Accounts - NEW -->
        <a href="create-admin.php"
           <?= $activeAdminPage === 'create-admin'
               ? 'class="active" aria-current="page"'
               : '' ?>>
            ⚙ &nbsp; Admin Accounts
        </a>

        <a href="../user-home.php" target="_blank" rel="noopener noreferrer">↗ &nbsp; View Website</a>
    </nav>


    <!-- Admin Profile -->
    <div class="admin-user-box">

        <div class="admin-user">

            <span class="admin-avatar" aria-hidden="true">
                <?= htmlspecialchars(
                    strtoupper($initialLetter),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </span>

            <div>

                <strong>
                    <?= htmlspecialchars(
                        $displayName,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </strong>

                <span>Administrator</span>

            </div>

        </div>

        <!-- Logout -->
        <form method="post" action="logout.php">

            <button class="logout-button" type="submit">
                ↪ &nbsp; Log out
            </button>

        </form>

    </div>

</aside>

