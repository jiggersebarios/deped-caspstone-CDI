<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Shared Files') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f9f9f9;
            margin-left: 240px; /* sidebar width */
            padding: 30px;
        }
        .section-title {
            margin-top: 40px;
            margin-bottom: 20px;
            font-weight: bold;
            color: #333;
        }
        .table th {
            background-color: #2c2c2c;
            color: white;
        }
        .alert {
            margin-bottom: 20px;
        }
        .btn-sm {
            padding: 3px 8px;
        }
    </style>
</head>
<body>

    <div class="container-fluid">

        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php elseif (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php elseif (session()->getFlashdata('info')): ?>
            <div class="alert alert-info"><?= session()->getFlashdata('info') ?></div>
        <?php endif; ?>

        <!-- Page Title -->
        <h2 class="mb-4">Shared Files</h2>

        <!-- Shared Files Table -->
        <?php if (!empty($sharedFiles)): ?>
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>File Name</th>
                        <th>Shared By</th>
                        <th>Date Shared</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($sharedFiles as $file): ?>
                    <tr>
                        <td><?= esc($file['id']) ?></td>
                        <td><?= esc($file['file_name']) ?></td>
                        <td><?= esc($file['shared_by_name']) ?></td>
                        <td><?= esc($file['created_at']) ?></td>
                        <td>
                            <a href="<?= base_url('sharedfiles/download/' . $file['id']) ?>" class="btn btn-success btn-sm">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No shared files available.</p>
        <?php endif; ?>
    </div>

    <script src="https://kit.fontawesome.com/a2e0bf0f3d.js" crossorigin="anonymous"></script>
</body>
</html>
