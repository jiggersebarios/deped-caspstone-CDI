<?php
$session = session();
$userName = $session->get('username');
$userRole = $session->get('role') ?? 'user';

// Get current URL segment to highlight active links
$uri = service('uri');
$currentPath = $uri->getPath();
?>

<div class="sidebar">
    <img src="/cdi/deped/public/uploads/pics/deped-ozamiz-2.png" alt="Logo" class="img-fluid">
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
 <a href="<?= site_url('admin/files') ?>" class="nav-link">
        <i class="fas fa-folder"></i> FILES
    </a>
            <a href="<?= site_url('user/profile') ?>" class="nav-link <?= ($currentPath === 'user/profile') ? 'active' : '' ?>">
                <i class="fas fa-user"></i> PROFILE
            </a>
        <?php endif; ?>
    </nav>

<a href="<?= site_url('logout') ?>" class="logout-btn">
    <i class="fas fa-sign-out-alt"></i> Logout
</a>

</div>

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
</style>
