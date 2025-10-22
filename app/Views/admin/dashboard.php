<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Admin Dashboard') ?></title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.15.4/css/all.css">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .main-content {
            margin-left: 260px;
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        .cards-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            width: 100%;
            max-width: 1200px;
        }

        .card-link-wrapper {
            text-decoration: none !important;
            color: inherit !important;
            display: block;
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
            transition: color 0.2s ease;
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

        .card-link-wrapper:hover .card {
            background: #e2e2e2;
            transform: translateY(-4px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.12);
        }

        .card-link-wrapper:hover .card i {
            color: #3550A0;
        }
    </style>
</head>

<body>
<div class="d-flex">
    <!-- Sidebar -->
    <?= $this->include('admin/sidebar') ?>

    <div class="main-content">
        <div class="cards-wrapper">
            
            <!-- Total Files -->
            <a href="<?= site_url('admin/files') ?>" class="card-link-wrapper">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <i class="fas fa-file-alt fa-2x text-success mb-2"></i>
                        <h5>Total Files</h5>
                        <h2><?= esc($totalFiles ?? 0) ?></h2>
                    </div>
                </div>
            </a>

            <!-- Pending Requests -->
            <a href="<?= site_url('admin/manage_request') ?>" class="card-link-wrapper">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <i class="fas fa-tasks fa-2x text-warning mb-2"></i>
                        <h5>Pending Requests</h5>
                        <h2><?= esc($pendingRequests ?? 0) ?></h2>
                    </div>
                </div>
            </a>

            <!-- New Uploaded Files -->
            <a href="<?= site_url('admin/manage-uploads') ?>" class="card-link-wrapper">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <i class="fas fa-upload fa-2x text-info mb-2"></i>
                        <h5>New Uploaded Files</h5>
                        <h2><?= esc($newUploadedFiles ?? 0) ?></h2>
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
