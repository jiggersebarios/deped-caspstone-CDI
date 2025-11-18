<?php
$uri = service('uri');
$session = session();
$userName = $session->get('username');
$userRole = $session->get('role') ?? 'user';

// Get current segments after base URL
$currentSegment1 = $uri->getSegment(2); // e.g., 'dashboard', 'files'
$currentSegment2 = $uri->getSegment(2); // e.g., 'sharedfiles', 'manage-uploads'
?>

<div class="sidebar">
    <img src="/deped/public/uploads/pics/deped-ozamiz-2.png" alt="Logo" class="img-fluid">
    <h5 class="hello"><?= esc($userName) ?></h5>
    
<?php if ($userRole === 'superadmin') : ?>
    <a href="<?= site_url('superadmin/dashboard') ?>" class="nav-link <?= ($currentSegment1 === 'dashboard') ? 'active' : '' ?>">
        <i class="fas fa-tachometer-alt"></i> <span class="nav-link-text">DASHBOARD</span>
    </a>
    
    <a href="<?= site_url('superadmin/files') ?>" class="nav-link <?= ($currentSegment1 === 'files') ? 'active' : '' ?>">
        <i class="fas fa-folder"></i> <span class="nav-link-text">FILES</span>
    </a>

<a href="<?= site_url($userRole . '/sharedfiles') ?>" 
   class="nav-link <?= ($currentSegment2 === 'sharedfiles') ? 'active' : '' ?>">
    <i class="fas fa-share-alt"></i>
    <span class="nav-link-text">SHARED FILES</span>
    <span id="badge-shared" class="badge bg-danger notification-badge" style="display:none;"></span>
</a>

            

            <a href="<?= site_url('superadmin/manage-uploads') ?>" class="nav-link <?= ($currentSegment2 === 'manage-uploads') ? 'active' : '' ?>">
                <i class="fas fa-upload"></i>
                <span class="nav-link-text">MANAGE UPLOADS</span>
                <span id="badge-uploads" class="badge bg-danger notification-badge" style="display:none;"></span>
            </a>

            <a href="<?= site_url('superadmin/manage_request') ?>" class="nav-link <?= ($currentSegment2 === 'manage_request') ? 'active' : '' ?>">
                <i class="fas fa-tasks"></i>
                <span class="nav-link-text">REQUESTS</span>
                <span id="badge-requests" class="badge bg-warning text-dark notification-badge" style="display:none;"></span>
            </a>


<a href="<?= site_url($userRole . '/category') ?>" class="nav-link <?= ($currentSegment2 === 'category') ? 'active' : '' ?>">
    <i class="fas fa-tags"></i> <span class="nav-link-text">CATEGORIES</span>
</a>



        <a href="<?= site_url('superadmin/manage_users') ?>" class="nav-link <?= ($currentSegment1 === 'manage_users') ? 'active' : '' ?>">
        <i class="fas fa-users"></i> <span class="nav-link-text">MANAGE USERS</span>
    </a>

        <a href="<?= site_url('superadmin/globalconfig') ?>" class="nav-link <?= ($currentSegment1 === 'globalconfig') ? 'active' : '' ?>">
        <i class="fas fa-cogs"></i> <span class="nav-link-text">GLOBAL CONFIG</span>
    </a>


<?php endif; ?>


<a href="<?= site_url('logout') ?>" class="logout-btn">
    <i class="fas fa-sign-out-alt"></i> <span class="nav-link-text">Logout</span>
</a>


</div>
<script>
function fetchNotifications() {
    fetch("<?= site_url($userRole . '/get-notifications') ?>")
        .then(res => res.json())
        .then(data => {

            const uploadBadge  = document.getElementById("badge-uploads");
            const requestBadge = document.getElementById("badge-requests");
            const sharedBadge  = document.getElementById("badge-shared");

            // ==========================
            // NEW UPLOADED FILES
            // ==========================
            if (data.newUploadedFiles > 0) {
                uploadBadge.textContent = data.newUploadedFiles;
                uploadBadge.style.display = "inline-block";
            } else {
                uploadBadge.style.display = "none";
            }

            // ==========================
            // PENDING REQUESTS
            // ==========================
            if (data.pendingRequests > 0) {
                requestBadge.textContent = data.pendingRequests;
                requestBadge.style.display = "inline-block";
            } else {
                requestBadge.style.display = "none";
            }

            // ==========================
            // FILES SHARED WITH ME
            // ==========================
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
    background-color: #2C2C2C;
    color: white;
    top: 0;
    padding: 20px 15px;
    height: 100vh;
    width: 230px;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: fixed;
    transition: width 0.3s ease-in-out;
    font-size: 14px;
}


.sidebar img {
    margin-bottom: 25px;
    width: 60%;
    height: auto;
}

.sidebar .hello {
    text-align: center;
    margin-bottom: 40px;
    font-size: 15px;
}

.sidebar .nav-link {
    color: #ffffff;
    margin: 10px 0;
    text-decoration: none;
    padding: 8px 0;
    transition: all 0.3s ease;
    font-weight: 500;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px; 
    width: 100%;
}

.sidebar .nav-link i {
    font-size: 20px;
    min-width: 20px;
    text-align: center;
}

.sidebar .logout-btn {
    margin-top: auto;
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
    text-decoration: none;
    border-radius: 4px;
    font-weight: 500;
    transition: background-color 0.3s ease;
}

.sidebar .logout-btn:hover {
    background-color: #6b1d1d;
    color: white;
}

.sidebar a:hover {
    color: #ECB439;
    text-decoration: none;
}

.sidebar .nav-link.active {
    color: #ECB439 !important;
    font-weight: 600;
}

.sidebar .nav-link-text {
    white-space: nowrap; /* Prevents text from wrapping */
    overflow: hidden;    /* Hides overflow if text is too long */
    text-overflow: ellipsis; /* Adds '...' for long text */
}

.notification-badge {
    margin-left: auto; 
    align-self: center; 
    margin-right: 15px; /* Increased margin for better spacing */
}

/* Responsive Styles */
@media (max-width: 1220px) {
    .sidebar {
        width: 180px; /* A slightly smaller width */
    }
    .sidebar .nav-link {
        font-size: 14px; /* Slightly smaller font */
        gap: 10px;
    }
    .sidebar .nav-link i {
        font-size: 18px; /* Slightly smaller icon */
    }
    .sidebar .logout-btn {
        width: 80%;
        font-size: 14px;
    }
}

/* Responsive Styles */
@media (max-width: 1200px) {
    .sidebar {
        width: 80px; /* Shrink the sidebar */
        padding: 20px 10px;
    }

    .sidebar .hello,
    .sidebar .nav-link-text {
        display: none; /* Hide text */
    }

    .sidebar .nav-link {
        justify-content: center; /* Center the icon */
        padding: 10px 0;
    }

    .sidebar .logout-btn {
        width: 50px; /* Adjust button to be more square-like */
        height: 50px;
        font-size: 20px;
    }
    .notification-badge {
        position: absolute; /* Position relative to the icon */
        top: -5px;
        right: -8px;
    }

    .sidebar img {
        width: 80%;
    }
}

/* Responsive Styles for smaller tablets */
@media (max-width: 771px) {
    .sidebar {
        width: 60px; /* Make sidebar even more compact */
        padding: 15px 5px;
    }
    .sidebar .nav-link i {
        font-size: 18px; /* Slightly smaller icons */
    }
    .sidebar .logout-btn {
        width: 40px;
        height: 40px;
        font-size: 18px;
    }
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

/* Styles for medium-height screens */
@media (max-height: 666px) {
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
        margin: 7px 0;
        padding: 5px 0;
        font-size: 14px;
    }
    .sidebar .logout-btn {
        margin-top: 25px;
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
    .sidebar img {
        width: 40%; /* Shrink logo */
        margin-bottom: 15px;
    }
    .sidebar .hello {
        font-size: 13px; /* Shrink text */
        margin-bottom: 15px;
    }
    .sidebar .nav-link {
        font-size: 13px; /* Shrink nav links */
        margin: 4px 0;
        padding: 4px 0;
        gap: 8px;
    }
    .sidebar .nav-link i {
        font-size: 16px; /* Shrink icons */
    }
    .sidebar .logout-btn {
        margin-top: 20px; /* Add some space before it */
    }
}

</style>
