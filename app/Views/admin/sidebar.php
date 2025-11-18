<?php
$uri = service('uri');
$currentSegment2 = $uri->getSegment(2); // e.g., 'files', 'sharedfiles', etc.

$session = session();
$userName = $session->get('username');
$userRole = $session->get('role') ?? 'user';
?>

<div class="sidebar">
    <img src="/deped/public/uploads/pics/deped-ozamiz-2.png" alt="Logo" class="img-fluid">
    <h5 class="hello"><?= esc($userName) ?></h5>
    
    <nav class="nav flex-column">
        <?php if ($userRole === 'admin') : ?>
            <!-- Admin-specific links -->

                <a href="<?= site_url('admin/dashboard') ?>" class="nav-link <?= ($currentSegment2 === 'dashboard') ? 'active' : '' ?>">
        <i class="fas fa-tachometer-alt"></i> <span class="nav-link-text">DASHBOARD</span>
    </a>
          
            <a href="<?= site_url('admin/files') ?>" class="nav-link <?= ($currentSegment2 === 'files') ? 'active' : '' ?>">
                <i class="fas fa-folder"></i> <span class="nav-link-text">FILES</span>
            </a>

            <a href="<?= site_url($userRole . '/sharedfiles') ?>" 
               class="nav-link <?= ($currentSegment2 === 'sharedfiles') ? 'active' : '' ?>">
                <i class="fas fa-share-alt"></i> <span class="nav-link-text">SHARED FILES</span>
            </a>

            <a href="<?= site_url('admin/manage-uploads') ?>" class="nav-link <?= ($currentSegment2 === 'manage-uploads') ? 'active' : '' ?>">
                <i class="fas fa-upload"></i>
                <span class="nav-link-text">MANAGE UPLOADS</span>
                <span id="badge-uploads" class="badge bg-danger notification-badge" style="display:none;"></span>
            </a>

            <a href="<?= site_url('admin/manage_request') ?>" class="nav-link <?= ($currentSegment2 === 'manage_request') ? 'active' : '' ?>">
                <i class="fas fa-tasks"></i>
                <span class="nav-link-text">REQUESTS</span>
                <span id="badge-requests" class="badge bg-warning text-dark notification-badge" style="display:none;"></span>
            </a>

<a href="<?= site_url($userRole . '/category') ?>" class="nav-link <?= ($currentSegment2 === 'category') ? 'active' : '' ?>">
    <i class="fas fa-tags"></i> <span class="nav-link-text">CATEGORIES</span>
</a>

<a href="<?= site_url($userRole . '/manual') ?>" class="nav-link <?= ($currentSegment2 === 'manual') ? 'active' : '' ?>">
    <i class="fas fa-file"></i> 
    <span class="nav-link-text">MANUAL GUIDE</span>
</a>




        <?php else : ?>
            <!-- User-specific links -->
            <a href="<?= site_url('dashboard') ?>" class="nav-link">
                <i class="fas fa-tachometer-alt"></i> <span class="nav-link-text">DASHBOARD</span>
            </a>
            <a href="<?= site_url('files') ?>" class="nav-link">
                <i class="fas fa-folder"></i> <span class="nav-link-text">MANAGE FILES</span>
            </a>
        <?php endif; ?>
    </nav>

    <a href="<?= site_url('logout') ?>" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i> <span class="nav-link-text">Logout</span>
    </a>
</div>
<script>
function fetchNotifications() {
    fetch("<?= site_url($userRole . '/get-notifications') ?>")
        .then(res => res.json())
        .then(data => {
            const uploadBadge = document.getElementById("badge-uploads");
            const requestBadge = document.getElementById("badge-requests");

            if (data.newUploadedFiles > 0) {
                uploadBadge.textContent = data.newUploadedFiles;
                uploadBadge.style.display = "inline-block";
            } else {
                uploadBadge.style.display = "none";
            }

            if (data.pendingRequests > 0) {
                requestBadge.textContent = data.pendingRequests;
                requestBadge.style.display = "inline-block";
            } else {
                requestBadge.style.display = "none";
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
    font-size: 13px;
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
    margin: 12px 0;
    text-decoration: none;
    padding: 8px 0;
    transition: all 0.3s ease;
    font-weight: 500;
    font-size: 15px;
    display: flex;
    align-items: center;
    gap: 12px; 
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

    /* This class should be on the main content area of pages using this sidebar */
    .main-content {
        margin-left: 80px !important; /* Adjust content margin to new sidebar width */
        transition: margin-left 0.3s ease-in-out;
    }

    
}

/* Styles for shorter screens */
@media (max-height: 700px) {
    .sidebar {
        overflow-y: auto; /* Enable vertical scrolling */
        padding-top: 15px;
        padding-bottom: 15px;
    }
    .sidebar img {
        width: 40%; /* Shrink logo */
        margin-bottom: 20px;
    }
    .sidebar .hello {
        font-size: 14px; /* Shrink text */
        margin-bottom: 20px;
    }
    .sidebar .nav-link {
        font-size: 14px;
        margin: 8px 0;
        padding: 6px 0;
        gap: 10px;
    }
    .sidebar .nav-link i {
        font-size: 18px; /* Shrink icons */
    }
    .sidebar .logout-btn {
        margin-top: 25px; /* Add some space before it */
    }

    /* When sidebar is collapsed on short screens */
    .sidebar .logout-btn {
    
        height: 45px;
        font-size: 18px;
    }
}


</style>
