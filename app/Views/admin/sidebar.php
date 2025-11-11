<?php
$uri = service('uri');
$currentSegment1 = $uri->getSegment(1); // e.g., 'admin', 'superadmin', or 'user'
$currentSegment2 = $uri->getSegment(2); // e.g., 'files', 'sharedfiles', etc.

$session = session();
$userName = $session->get('username');
$userRole = $session->get('role') ?? 'user';
?>
<?php
$session = session();
$userName = $session->get('username');
$userRole = $session->get('role') ?? 'user';
?>
<?php if (isset($config['allow_admin_to_access_category']) && $config['allow_admin_to_access_category'] == 1): ?>
<li>
    <a href="<?= site_url('admin/category') ?>">
        <i class="fas fa-tags"></i> <span>Categories</span>
    </a>
</li>
<?php endif; ?>

<div class="sidebar">
    <img src="/cdi/deped/public/uploads/pics/deped-ozamiz-2.png" alt="Logo" class="img-fluid">
    <h5 class="hello"><?= esc($userName) ?></h5>
    
    <nav class="nav flex-column">
        <?php if ($userRole === 'admin') : ?>
            <!-- Admin-specific links -->
            <a href="<?= site_url('admin/dashboard') ?>" class="nav-link">
                <i class="fas fa-tachometer-alt"></i> <span class="nav-link-text">DASHBOARD</span>
            </a>
            <a href="<?= site_url('admin/files') ?>" class="nav-link">
                <i class="fas fa-folder"></i> <span class="nav-link-text">FILES</span>
            </a>

            <a href="<?= site_url($userRole . '/sharedfiles') ?>" 
               class="nav-link <?= ($currentSegment2 === 'sharedfiles') ? 'active' : '' ?>">
                <i class="fas fa-share-alt"></i> <span class="nav-link-text">SHARED FILES</span>
            </a>
            
<a href="<?= site_url('admin/manage_uploads') ?>" 
   class="nav-link <?= ($currentSegment2 === 'manage_uploads') ? 'active' : '' ?>">
    <i class="fas fa-upload"></i> <span class="nav-link-text">MANAGE UPLOADS</span>
</a>

     
            <a href="<?= site_url('admin/manage_request') ?>" 
   class="nav-link <?= ($currentSegment1 === 'manage_request') ? 'active' : '' ?>">
    <i class="fas fa-tasks"></i> <span class="nav-link-text">REQUESTS</span>
</a>



<a href="<?= site_url($userRole . '/category') ?>" class="nav-link">
    <i class="fas fa-tags"></i> <span class="nav-link-text">CATEGORIES</span>
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

<style>
.sidebar {
    background-color: #2C2C2C;
    color: white;
    top: 0;
    padding: 20px 15px;
    height: 100vh;
    width: 220px;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: fixed;
    transition: width 0.3s ease-in-out;
    font-size: 15px;
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

    .sidebar img {
        width: 80%;
    }

    /* This class should be on the main content area of pages using this sidebar */
    .main-content {
        margin-left: 80px !important; /* Adjust content margin to new sidebar width */
        transition: margin-left 0.3s ease-in-out;
    }

    
}
</style>
