<?php
$session = session();
$userName = $session->get('username');
$userRole = $session->get('role') ?? 'user';
?>

<div class="sidebar">
    <img src="/cdi/deped/public/uploads/pics/deped-ozamiz-2.png" alt="Logo" class="img-fluid">
    <h5 class="hello"><?= esc($userName) ?></h5>
    
<?php if ($userRole === 'superadmin') : ?>
    <a href="<?= site_url('superadmin/dashboard') ?>" class="nav-link">
        <i class="fas fa-tachometer-alt"></i> DASHBOARD
    </a>
    
    <a href="<?= site_url('admin/files') ?>" class="nav-link">
        <i class="fas fa-folder"></i> FILES
    </a>

    <a href="<?= site_url('request') ?>" class="nav-link">
        <i class="fas fa-upload"></i> MANAGE UPLOADS
    </a>

    <a href="<?= site_url('managereq') ?>" class="nav-link">
        <i class="fas fa-tasks"></i> MANAGE REQUEST
    </a>

    <a href="<?= site_url('superadmin/manage_users') ?>" class="nav-link">
        <i class="fas fa-users"></i> MANAGE USERS
    </a>

<a href="<?= site_url('superadmin/category') ?>" class="nav-link">
    <i class="fas fa-tags"></i> CATEGORIES
</a>

    <a href="<?= site_url('superadmin/globalconfig') ?>" class="nav-link">
        <i class="fas fa-cogs"></i> GLOBAL CONFIG
    </a>
<?php else : ?>
    <!-- Regular user links -->
    <a href="<?= site_url('dashboard') ?>" class="nav-link">
        <i class="fas fa-tachometer-alt"></i> DASHBOARD
    </a>
    <a href="<?= site_url('files') ?>" class="nav-link">
        <i class="fas fa-folder"></i> MANAGE FILES
    </a>
<?php endif; ?>


<a href="<?= site_url('logout') ?>" class="logout-btn">
    <i class="fas fa-sign-out-alt"></i> Logout
</a>


</div>

<style>
   .sidebar {
    background-color: #2C2C2C;
    color: white;
    top: 0;
    padding: 20px 15px;
    height: 100vh;
    width: 220px; /* slightly slimmer for better proportions */
    display: flex;
    flex-direction: column;
    align-items: center;
    position: fixed;
    font-size: 15px; /* consistent baseline font */
}

.sidebar img {
    margin-bottom: 25px;
    width: 60%; /* more presence */
    height: auto;
}

.sidebar .hello {
    text-align: center;
    margin-bottom: 40px;
    font-size: 15px;
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
    width: 100%; /* links stretch nicely */
}

.sidebar .nav-link i {
    font-size:20px; /* icons scale with text */
    min-width: 20px;
    text-align: center;
}

.nav-link:hover {
    color: #ECB439;
}

.nav-link.active {
    color: #ECB439;
    font-weight: 600;
}

.sidebar .logout-btn {
    margin-top: 100px;
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
    text-decoration: none; /* remove underline */
    border-radius: 4px; /* optional smoother look */
    font-weight: 500;
    transition: background-color 0.3s ease;
}

.sidebar .logout-btn:hover {
    background-color: #6b1d1d;
    color: white;
}

.sidebar a {
    font-weight: normal !important;
    color: #ffffffff !important;
    text-decoration: none;
}

/* Keep hover consistent */
.sidebar a:hover {
    color: #3550A0 !important;
    text-decoration: none;
}

/* Active sidebar link */
.sidebar a.active {
    font-weight: normal !important; /* prevent bold */
    color: #3550A0 !important;
    background: #f0f2f8; /* optional highlight */
    border-radius: 5px;
}

</style>
