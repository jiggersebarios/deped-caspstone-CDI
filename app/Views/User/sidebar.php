<?php
$session = session();
$userName = $session->get('username');
$userRole = $session->get('role') ?? 'user';

// Get current URL path to highlight active links
$uri = service('uri');
$currentPath = $uri->getPath();
?>
<?php
$uri = service('uri');
$currentSegment1 = $uri->getSegment(1); // e.g., 'admin' or 'superadmin'
$currentSegment2 = $uri->getSegment(2); // e.g., 'manage_uploads', 'files', etc.
?>
<div class="sidebar">
    <img src="/deped/public/uploads/pics/deped-ozamiz-2.png" alt="Logo" class="img-fluid">
    <h5 class="hello">Welcome, <?= esc($userName) ?></h5>

    <nav class="nav flex-column">
        <?php if ($userRole === 'admin') : ?>
            <!-- Admin-specific links -->
            <a href="<?= site_url('admin/dashboard') ?>" class="nav-link <?= ($currentPath === 'admin/dashboard') ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> DASHBOARD
            </a>
            <a href="<?= site_url('admin/files') ?>" class="nav-link <?= ($currentPath === 'admin/files') ? 'active' : '' ?>">
                <i class="fas fa-folder"></i> FILES
            </a>

        <?php else : ?>
            <!-- User-specific links -->
            <a href="<?= site_url('user/dashboard') ?>" class="nav-link <?= ($currentPath === 'user/dashboard') ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> DASHBOARD
            </a>
            <a href="<?= site_url('user/files') ?>" class="nav-link <?= ($currentPath === 'user/files') ? 'active' : '' ?>">
                <i class="fas fa-folder"></i> FILES
            </a>
            <a href="<?= site_url('user/request') ?>" class="nav-link">
                <i class="fas fa-tasks"></i>
                <span class="nav-link-text">REQUESTS</span>
                <span id="badge-requests" class="badge bg-success text-white notification-badge" style="display:none;"></span>

            </a>

<a href="<?= site_url($userRole . '/sharedfiles') ?>" 
   class="nav-link <?= ($currentSegment2 === 'sharedfiles') ? 'active' : '' ?>">
    <i class="fas fa-share-alt"></i>
    <span class="nav-link-text">SHARED FILES</span>
    <span id="badge-shared" class="badge bg-danger notification-badge" style="display:none;"></span>
</a>

<a href="<?= site_url(strtolower($userRole) . '/manual') ?>"
   class="nav-link <?= ($currentSegment2 === 'manual') ? 'active' : '' ?>">
    <i class="fas fa-file"></i> 
    <span class="nav-link-text">MANUAL GUIDE</span>
</a>



        <?php endif; ?>
    </nav>

    <a href="<?= site_url('logout') ?>" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</div>
<script>
function fetchNotifications() {
    fetch("<?= site_url($userRole . '/get-notifications') ?>")
        .then(res => res.json())
        .then(data => {
            const uploadBadge = document.getElementById("badge-uploads");
            const requestBadge = document.getElementById("badge-requests");
            const sharedBadge = document.getElementById("badge-shared");

            // New uploads (only if badge exists)
            if (uploadBadge) {
                if (data.newUploadedFiles > 0) {
                    uploadBadge.textContent = data.newUploadedFiles;
                    uploadBadge.style.display = "inline-block";
                } else {
                    uploadBadge.style.display = "none";
                }
            }

            // Pending requests
            if (data.pendingRequests > 0) {
                requestBadge.textContent = data.pendingRequests;
                requestBadge.style.display = "inline-block";
            } else {
                requestBadge.style.display = "none";
            }

            // Shared files
            if (data.sharedWithMe > 0) {
                sharedBadge.textContent = data.sharedWithMe;
                sharedBadge.style.display = "inline-block";
            } else {
                sharedBadge.style.display = "none";
            }
        });
}

fetchNotifications();
setInterval(fetchNotifications, 5000);
</script>

<style>
.sidebar {
    background-color: #3550A0;
    color: white;
    padding: 20px 15px;
    height: 100vh;
    width: 220px;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: fixed;
    font-size: 15px;
    top: 0;
    left: 0;
    z-index: 1000;
}

.sidebar img {
    margin-bottom: 25px;
    width: 65%;
    height: auto;
}

.sidebar .hello {
    text-align: center;
    margin-bottom: 40px;
    font-size: 20px;
    font-weight: 600;
}

.sidebar .nav-link {
    color: white;
    margin: 12px 0;
    text-decoration: none;
    padding: 8px 0;
    position: relative;
    transition: all 0.3s ease;
    font-weight: 500;
    font-size: 15px;
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
}

.sidebar .nav-link i {
    font-size: 18px;
    min-width: 20px;
    text-align: center;
}

.nav-link:hover,
.nav-link.active {
    color: #ECB439;
    font-weight: 600;
}

.sidebar .logout-btn {
    margin-top: 200px;
    background-color: #b23b3b;
    color: white;
    width: 70%;
    text-align: center;
    padding: 10px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border-radius: 4px;
}

.logout-btn:hover {
    background-color: #6b1d1d;
    text-decoration: none;
}

/* Styles for medium-height screens */
@media (max-height: 770px) {
    .sidebar {
        overflow-y: auto; /* Enable vertical scrolling */
        padding-top: 15px;
        padding-bottom: 15px;
    }
    .sidebar img {
        width: 55%; /* Shrink logo */
        margin-bottom: 20px;
    }
    .sidebar .hello {
        margin-bottom: 30px;
    }
    .sidebar .nav-link {
        margin: 8px 0;
        padding: 6px 0;
    }
    .sidebar .logout-btn {
        margin-top: 30px; /* Add some space before it */
    }
}

/* Styles for medium-short screens */
@media (max-height: 660px) {
    .sidebar {
        overflow-y: auto; /* Enable vertical scrolling */
        padding-top: 15px;
        padding-bottom: 15px;
    }
    .sidebar img {
        width: 50%; /* Shrink logo */
        margin-bottom: 20px;
    }
    .sidebar .hello {
        font-size: 14px;
        margin-bottom: 25px;
    }
    .sidebar .nav-link {
        margin: 6px 0;
        padding: 5px 0;
        font-size: 14px;
    }
    .sidebar .logout-btn {
        margin-top: 25px;
    }
}

/* Styles for slightly short screens */
@media (max-height: 552px) {
    .sidebar {
        overflow-y: auto; /* Enable vertical scrolling */
        padding-top: 15px;
        padding-bottom: 10px;
    }
    .sidebar img {
        width: 50%; /* Shrink logo */
        margin-bottom: 20px;
    }
    .sidebar .hello {
        font-size: 14px; /* Shrink text */
        margin-bottom: 20px;
    }
    .sidebar .nav-link {
        font-size: 14px; /* Corrected font size */
        margin: 6px 0;
        padding: 6px 0;
    }
    .sidebar .logout-btn {
        margin-top: 25px; /* Add some space before it */
    }
}

/* Styles for short screens (e.g., landscape mobile) */
@media (max-height: 540px) {
    .sidebar {
        overflow-y: auto; /* Enable vertical scrolling */
        padding-top: 10px;
        padding-bottom: 10px;
    }
    .sidebar img { width: 40%; margin-bottom: 15px; }
    .sidebar .hello { font-size: 13px; margin-bottom: 15px; }
    .sidebar .nav-link { font-size: 13px; margin: 4px 0; padding: 4px 0; gap: 8px; }
    .sidebar .nav-link i { font-size: 16px; }
    .sidebar .logout-btn { margin-top: 20px; }
}
</style>
