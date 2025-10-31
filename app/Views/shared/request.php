<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Request for Archive Files') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .content { margin-left: 220px; padding: 25px; min-height: 100vh; }
        .page-header { color: #3550A0; font-weight: bold; margin-bottom: 25px; }
        .requests-table { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
        .requests-table th { background: #2C2C2C; color: white; text-transform: uppercase; font-size: 14px; }
        .requests-table td { font-size: 14px; vertical-align: middle; }
        .btn-action { margin-right: 6px; }
        .modal-header { background: #3550A0; color: white; }
        .modal-footer .btn-primary { background: #3550A0; border: none; }
        .modal-footer .btn-primary:hover { background: #2a3d7d; }
    </style>
</head>
<body>

<?= $this->include('user/sidebar') ?>

<div class="content">
    <h2 class="page-header"><i class="fa-solid fa-file-circle-plus"></i> Request for Archive Files</h2>

    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#completedFilesModal">
        <i class="fa fa-download"></i> View Downloaded Files
    </button>

    <!-- Main Table: Not Yet Downloaded -->
    <table class="table table-bordered requests-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>File Name</th>
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
                    <td><?= esc($req['reason']) ?></td>
                    <td>
                        <?php if ($req['status'] == 'pending'): ?>
                            <span class="badge bg-warning text-dark">Pending</span>
                        <?php elseif ($req['status'] == 'approved'): ?>
                            <span class="badge bg-success">Approved</span>
                        <?php elseif ($req['status'] == 'denied'): ?>
                            <span class="badge bg-danger">Denied</span>
                        <?php else: ?>
                            <span class="badge bg-secondary"><?= ucfirst($req['status']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($req['requested_at']) ?></td>
                    <td><?= esc($req['approved_at'] ?? '—') ?></td>
                    <td>
                        <?php if ($req['status'] == 'approved' && !empty($req['download_token'])): ?>
                            <a href="<?= site_url('request/download/'.$req['download_token']) ?>" 
                               class="btn btn-success btn-sm btn-action"
                               onclick="return confirm('Download this file?');">
                               <i class="fa fa-download"></i> Download
                            </a>
                        <?php else: ?>
                            <span class="text-muted">No action</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center text-muted">No requests yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- New Request Modal -->
<div class="modal fade" id="addRequestModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url('request/submit') ?>" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-file-circle-plus"></i> New File Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="file_id" class="form-label">Select File</label>
                    <select name="file_id" id="file_id" class="form-select" required>
                        <option value="" disabled selected>Choose file...</option>
                        <?php foreach ($files as $file): ?>
                            <option value="<?= esc($file['id']) ?>"><?= esc($file['file_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="reason" class="form-label">Reason for Request</label>
                    <textarea name="reason" id="reason" class="form-control" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Submit Request</button>
            </div>
        </form>
    </div>
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
              <th>ID</th>
              <th>File Name</th>
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
                      <td><?= esc($file['reason']) ?></td>
                      <td><?= esc($file['requested_at']) ?></td>
                      <td><?= esc($file['downloaded_at']) ?></td>
                  </tr>
                  <?php endforeach; ?>
              <?php else: ?>
                  <tr>
                      <td colspan="5" class="text-center text-muted">No completed files yet.</td>
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


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('a.btn-action[href*="request/download"]').forEach(btn => {
        btn.addEventListener('click', function() {
            setTimeout(() => location.reload(), 2000);
        });
    });
});
</script>
</body>
</html>
