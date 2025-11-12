<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>

    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    body {
        background-color: #f8f9fa;
        font-family: Arial, sans-serif;
    }
    .main-content {
        margin-left: 220px;
        padding: 20px;
    }
    .cards-wrapper {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }
    .card {
        border-radius: 12px;
        height: 180px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: #f9fafc;
        border: 1px solid #e1e5ee;
        text-align: center;
        cursor: pointer;
        padding: 20px;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }
    .card i {
        font-size: 40px;
        color: #ECB439;
        margin-bottom: 12px;
    }
    .card h5 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #2f3e64;
    }
    .card h2 {
        margin: 8px 0 0;
        font-size: 26px;
        font-weight: bold;
        color: #333;
    }
    .card:hover {
        background: #e2e2e2;
        transform: translateY(-4px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.12);
    }
    .recent-uploads {
        margin-top: 40px;
    }
    .recent-uploads h5 {
        font-weight: 600;
        color: #2f3e64;
        margin-bottom: 15px;
    }
    table th, table td {
        vertical-align: middle !important;
    }
    @media (max-width: 992px) {
        .main-content { margin-left: 80px; padding: 20px 15px; }
        .cards-wrapper { grid-template-columns: 1fr; gap: 15px; }
        .card { height: 150px; }
    }
</style>

<body>
<div class="d-flex">
    <!-- Sidebar -->
    <?= $this->include('user/sidebar') ?>

    <!-- Main Content -->
    <div class="main-content container-fluid">
        <!-- Welcome Header -->
        <div class="mb-4">
            <h3 class="font-weight-bold">Welcome, <?= esc($user['username'] ?? 'User') ?>!</h3>
            <p class="text-muted mb-1">
                Main Folder: <strong><?= esc($user['main_folder'] ?? 'No folder assigned') ?></strong>
            </p>
        </div>

        <!-- Cards Section -->
        <div class="cards-wrapper">
            <!-- Total Files Card -->
            <a href="<?= site_url('user/files') ?>" class="text-decoration-none text-dark">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <i class="fas fa-file-alt fa-2x text-success mb-2"></i>
                        <h5>Total Files</h5>
                        <h2><?= esc($totalFiles ?? 0) ?></h2>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
