<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Uploads - HR Archiving System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
         <!-- Font Awesome Pro v5.15.4 -->
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.15.4/css/all.css">
</head>
<body>

<?php
$role = $role ?? session()->get('role') ?? 'admin';
echo view($role . '/sidebar');
?>

<div class="container mt-4">
    <h2 class="mb-4">Manage Uploads</h2>
    <p class="text-muted">Review and approve or reject pending file uploads.</p>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php elseif (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if (!empty($pendingFiles)): ?>
        <table class="table table-bordered table-striped shadow-sm">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>File Name</th>
                    <th>Category</th>
                    <th>Uploaded By</th>
                    <th>Date Uploaded</th>
                    <th>Status</th>
                    <th style="width: 220px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingFiles as $file): ?>
                    <tr>
                        <td><?= $file['id'] ?></td>
                        <td><?= esc($file['file_name']) ?></td>
                        <td><?= esc($file['category_name'] ?? 'Uncategorized') ?></td>
                        <td><?= esc($file['uploader_name'] ?? 'Unknown') ?></td>
                        <td><?= $file['uploaded_at'] ? date('Y-m-d h:i A', strtotime($file['uploaded_at'])) : '—' ?></td>
                        <td><span class="badge badge-warning">Pending</span></td>

                        <td>
                            <a href="<?= site_url($role . '/files/viewFile/' . $file['id']) ?>" 
                               target="_blank" class="btn btn-sm btn-info">View</a>
                            <a href="<?= site_url('manage-uploads/accept/' . $file['id']) ?>" 
                               class="btn btn-sm btn-success"
                               onclick="return confirm('Accept this file upload?')">Accept</a>
                            <a href="<?= site_url('manage-uploads/reject/' . $file['id']) ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Reject this file upload?')">Reject</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No pending file uploads found.</div>
    <?php endif; ?>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
