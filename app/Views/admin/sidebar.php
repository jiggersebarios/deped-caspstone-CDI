<?php
$session = session();
$userName = $session->get('username');
$userRole = $session->get('role') ?? 'user';
?>

<div class="sidebar">
    <img src="/cdi/deped/public/uploads/pics/deped-ozamiz-2.png" alt="Logo" class="img-fluid">
    <h5 class="hello">Welcome, <?= esc($userName) ?></h5>
    
    <nav class="nav flex-column">
        <?php if ($userRole === 'admin') : ?>
            <!-- Admin-specific links -->
            <a href="<?= site_url('admin/dashboard') ?>" class="nav-link">
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

        <?php else : ?>
            <!-- User-specific links -->
            <a href="<?= site_url('dashboard') ?>" class="nav-link">
                <i class="fas fa-tachometer-alt"></i> DASHBOARD
            </a>
            <a href="<?= site_url('files') ?>" class="nav-link">
                <i class="fas fa-folder"></i> MANAGE FILES
            </a>
        <?php endif; ?>
    </nav>
    
    <form method="post" action="<?= site_url('logout') ?>">
        <button type="submit" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </form>
</div>

<style>
   .sidebar {
    background-color: #3550A0;
    color: white;
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
    font-size: 24px;
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
    margin-top: 150px;
    background-color: red; color: white;
    width: 100%;
    text-align: center;
    padding: 10px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px; }

.logout-btn:hover {
    background-color: darkred;
}
</style>
