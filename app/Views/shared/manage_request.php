<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Manage File Requests') ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: #f8f9fa;
        }
        .content {
            margin-left: 220px;
            padding: 25px;
            min-height: 100vh;
        }
        .page-header {
            color: #3550A0;
            font-weight: bold;
            margin-bottom: 25px;
        }
        .requests-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
        .requests-table th {
            background: #2C2C2C;
            color: white;
            text-transform: uppercase;
            font-size: 14px;
        }
        .requests-table td {
            font-size: 14px;
            vertical-align: middle;
        }
        .btn-action {
            margin-right: 6px;
        }
        .modal-header {
            background: #3550A0;
            color: white;
        }
        .modal-footer .btn-primary {
            background: #3550A0;
            border: none;
        }
        .modal-footer .btn-primary:hover {
            background: #2a3d7d;
        }
    </style>
</head>
<body>

    <!-- Sidebar (auto-load depending on user role) -->
    <?php 
        $role = session()->get('role');
        if ($role === 'superadmin') {
            echo $this->include('superadmin/sidebar');
        } elseif ($role === 'admin') {
            echo $this->include('admin/sidebar');
        }
    ?>

    <div class="content">
        <h2 class="page-header"><i class="fa-solid fa-folder-open"></i> Manage File Requests</h2>

        <!-- Alert Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php elseif (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <!-- View Downloaded Files Button -->
<button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#completedFilesModal">
    <i class="fa fa-download"></i> View Downloaded Files
</button>

<!-- Pending & Approved Requests Table -->
<table class="table table-bordered requests-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>File Name</th>
            <th>Requested By</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Requested At</th>
            <th>Approved At</th>
            <th>Actions</th>
        </tr>
    </thead>
<tbody>
    <?php if (!empty($requests)): ?>
        <?php foreach ($requests as $req): ?>
            <tr>
                <td><?= esc($req['id']) ?></td>
                <td><?= esc($req['file_name'] ?? 'Unknown') ?></td>
                <td><?= esc($req['username'] ?? 'Unknown') ?></td>
                <td><?= esc($req['reason'] ?? '-') ?></td>
                <td>
                    <?php if ($req['status'] == 'pending'): ?>
                        <span class="badge bg-warning text-dark">Pending</span>
                    <?php elseif ($req['status'] == 'approved'): ?>
                        <span class="badge bg-success">Approved</span>
                    <?php elseif ($req['status'] == 'denied'): ?>
                        <span class="badge bg-danger">Denied</span>
                    <?php endif; ?>
                </td>
                <td><?= esc($req['requested_at']) ?></td>
                <td><?= esc($req['approved_at'] ?? '—') ?></td>
                <td>
                    <?php if ($req['status'] == 'pending'): ?>
                        <!-- Approve / Deny Buttons -->
                        <a href="<?= site_url($role.'/manage_request/approve/'.$req['id']) ?>" 
                           class="btn btn-success btn-sm btn-action"
                           onclick="return confirm('Approve this request?')">
                           <i class="fa fa-check"></i>
                        </a>
                        <a href="<?= site_url($role.'/manage_request/deny/'.$req['id']) ?>" 
                           class="btn btn-danger btn-sm btn-action"
                           onclick="return confirm('Deny this request?')">
                           <i class="fa fa-times"></i>
                        </a>

<?php elseif ($req['status'] == 'approved' && isset($req['user_id']) && $req['user_id'] == session()->get('id')): ?>
    <!-- Download Button (only for requester) -->
    <a href="<?= site_url($role.'/manage_request/directDownload/'.$req['id']) ?>" 
       class="btn btn-primary btn-sm btn-action">
       <i class="fa fa-download"></i> Download
    </a>
<?php endif; ?>

                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="8" class="text-center text-muted">No pending or approved requests.</td>
        </tr>
    <?php endif; ?>
</tbody>

</table>


    </div>

<div class="modal fade" id="completedFilesModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"><i class="fa fa-check-circle"></i> Completed / Downloaded Files</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th>#</th>
              <th>File Name</th>
              <th>Requested By</th>
              <th>Reason</th>
              <th>Requested At</th>
              <th>Downloaded At</th>
            </tr>
          </thead>
          <tbody>
              <?php if (!empty($completedFiles)): ?>
                  <?php foreach ($completedFiles as $index => $file): ?>
                  <tr>
                      <td><?= $index + 1 ?></td>
                      <td><?= esc($file['file_name']) ?></td>
                      <td><?= esc($file['username'] ?? 'Unknown') ?></td>
                      <td><?= esc($file['reason']) ?></td>
                      <td><?= esc($file['requested_at']) ?></td>
                      <td><?= esc($file['downloaded_at']) ?></td>
                  </tr>
                  <?php endforeach; ?>
              <?php else: ?>
                  <tr>
                      <td colspan="6" class="text-center text-muted">No completed files yet.</td>
                  </tr>
              <?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
