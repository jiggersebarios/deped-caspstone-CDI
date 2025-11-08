

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= esc($title ?? 'Shared Files') ?></title>

  <!-- Bootstrap & Font Awesome -->
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
    .shared-table {
      background: white;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .shared-table th {
      background: #2C2C2C;
      color: white;
      text-transform: uppercase;
      font-size: 14px;
    }
    .shared-table td {
      font-size: 14px;
      vertical-align: middle;
    }
    .btn-action {
      margin-right: 6px;
    }
    .modal-header {
      background: linear-gradient(135deg, #3550A0, #2a3d7d);
      color: white;
    }
    .nav-tabs .nav-link.active {
      background-color: #3550A0;
      color: white !important;
      border: none;
    }
    .nav-tabs .nav-link {
      color: #3550A0;
      font-weight: 500;
      border: none;
    }
    .file-list-table th {
      background: #f1f1f1;
      color: #333;
    }
    .search-bar {
      margin-bottom: 15px;
    }
  </style>
</head>
<body>

<?php 
  $role = session()->get('role');
  if ($role === 'superadmin') {
      echo $this->include('superadmin/sidebar');
  } elseif ($role === 'admin') {
      echo $this->include('admin/sidebar');
  } else {
      echo $this->include('user/sidebar');
  }
?>

<div class="content">
  <h2 class="page-header"><i class="fa-solid fa-share-nodes"></i> Shared Files</h2>

  <!-- Flash messages -->
  <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php elseif (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
  <?php elseif (session()->getFlashdata('info')): ?>
      <div class="alert alert-info"><?= session()->getFlashdata('info') ?></div>
  <?php endif; ?>

  <!-- Share File Button -->
  <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#shareFileModal">
      <i class="fa fa-share-alt"></i> Share a File
  </button>

  <!-- Tabs -->
  <ul class="nav nav-tabs" id="fileTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="shared-by-me-tab" data-bs-toggle="tab" data-bs-target="#shared-by-me" type="button" role="tab">
        <i class="fa fa-user-shield"></i> Files You Shared
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="shared-with-me-tab" data-bs-toggle="tab" data-bs-target="#shared-with-me" type="button" role="tab">
        <i class="fa fa-users"></i> Files Shared With You
      </button>
    </li>
  </ul>

  <div class="tab-content" id="fileTabsContent">
    <!-- Shared by me -->
    <div class="tab-pane fade show active" id="shared-by-me" role="tabpanel">
      <div class="card mt-3">
        <div class="card-body p-0">
          <table class="table table-bordered shared-table mb-0">
            <thead>
              <tr>
                
                <th>File Name</th>
                <th>Category</th>
                <th>Uploader</th>
                <th>Shared With</th>
                <th>Shared Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($sharedFiles)): ?>
                <?php foreach ($sharedFiles as $index => $file): ?>
                  <tr>
                 
                    <td><?= esc($file['file_name']) ?></td>
                    <td><?= esc($file['category_name'] ?? '-') ?></td>
                    <td><?= esc($file['uploader_name'] ?? '-') ?></td>
                    <td><?= esc($file['shared_to_name'] ?? '-') ?></td>
                    <td><?= esc($file['created_at']) ?></td>
                    <td>
                      <a href="<?= site_url(session()->get('role') . '/sharedfiles/unshare/'.$file['id']) ?>" class="btn btn-danger btn-sm btn-action" onclick="return confirm('Unshare this file?')">
    <i class="fa fa-share"></i>
</a>

                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="7" class="text-center text-muted">You haven’t shared any files yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Shared with me -->
    <div class="tab-pane fade" id="shared-with-me" role="tabpanel">
      <div class="card mt-3">
        <div class="card-body p-0">
          <table class="table table-bordered shared-table mb-0">
            <thead>
              <tr>
              
                <th>File Name</th>
                <th>Category</th>
                <th>Shared By</th>
                <th>Shared Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($sharedWithMe)): ?>
                <?php foreach ($sharedWithMe as $index => $file): ?>
                  <tr>
                   
                    <td><?= esc($file['file_name']) ?></td>
                    <td><?= esc($file['category_name'] ?? '-') ?></td>
                     <td><?= esc($file['shared_to_name'] ?? '-') ?></td>
                    <td><?= esc($file['created_at']) ?></td>
                    <td>
                      <a href="<?= base_url('sharedfiles/download/' . $file['id']) ?>" class="btn btn-sm btn-primary">
                        <i class="fa fa-download"></i> Download
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="6" class="text-center text-muted">No files have been shared with you yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Share File Modal -->
<div class="modal fade" id="shareFileModal" tabindex="-1" aria-labelledby="shareFileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="shareFileModalLabel"><i class="fa fa-share-alt"></i> Share a File</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table table-striped file-list-table">
          <thead>
            <tr>
              <th>File Name</th>
              <th>Uploader</th>
              <th>Folder</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($allFiles)): ?>
              <?php foreach ($allFiles as $file): ?>
                <tr>
                  <td><?= esc($file['file_name']) ?></td>
                  <td><?= esc($file['uploader_name']) ?></td>
                  <td><?= esc($file['folder_name']) ?></td>
                  <td>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" 
                            data-bs-target="#selectUserModal" 
                            data-file-id="<?= $file['id'] ?>">
                      <i class="fa fa-share-alt"></i> Share
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="4" class="text-center text-muted">No files available to share.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- Select Users Modal -->
<div class="modal fade" id="selectUserModal" tabindex="-1" aria-labelledby="selectUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
   <form id="shareForm" method="post" action="<?= site_url(session()->get('role') . '/sharedfiles/share') ?>">


        <div class="modal-header">
          <h5 class="modal-title" id="selectUserModalLabel"><i class="fa fa-users"></i> Select Users to Share</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="file_id" id="modalFileId">
          <?php if (!empty($users)): ?>
            <div class="form-group">
              <?php foreach ($users as $user): ?>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" name="target_users[]" value="<?= esc($user['id']) ?>" id="user<?= esc($user['id']) ?>">
                  <label class="form-check-label" for="user<?= esc($user['id']) ?>">
                    <?= esc($user['username']) ?> (<?= esc($user['role']) ?>)
                  </label>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-muted">No users available.</p>
          <?php endif; ?>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Share File</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>



<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const selectUserModal = document.getElementById('selectUserModal');
  selectUserModal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    const fileId = button.getAttribute('data-file-id');
    document.getElementById('modalFileId').value = fileId;
  });
</script>

</body>
</html>
