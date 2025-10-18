<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Files - HR Archiving System</title>

    <!-- Font Awesome Pro v5.15.4 -->
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.15.4/css/all.css">
    <!-- Bootstrap 4 -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

<!-- Sidebar -->
<?php
$role = $role ?? session()->get('role') ?? 'user';

if ($role === 'superadmin') {
    echo view('superadmin/sidebar');
} elseif ($role === 'admin') {
    echo view('admin/sidebar');
} else {
    echo view('user/sidebar');
}
?>


<div class="main-container">

<!-- Search -->
<?php if (!isset($parentFolder) || (isset($depth) && $depth < 3)): ?>
    <?php if (!isset($parentFolder)): ?>
        <form action="<?= base_url($role . '/files') ?>" method="get" class="form-inline mb-3">
            <input type="text" name="search" class="form-control mr-2" placeholder="Search folders..." value="<?= esc($search ?? '') ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    <?php else: ?>
        <form action="<?= site_url($role . '/files/view/' . $parentFolder['id']) ?>" method="get" class="form-inline mb-3">
            <input type="text" name="search" class="form-control mr-2" placeholder="Search subfolders..." value="<?= esc($search ?? '') ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    <?php endif; ?>
<?php endif; ?>


  <!-- Folder / Subfolder Buttons -->
<?php if (!isset($parentFolder)): ?>
    <div class="button-container">
        <?php if ($role === 'superadmin' || ($role === 'admin' && $canAddFolder)): ?>
            <button class="btn btn-primary mb-2" data-toggle="modal" data-target="#addFolderModal">
                <i class="fa fa-plus me-2"></i> ADD FOLDER
            </button>
        <?php endif; ?>

        <?php if ($role === 'superadmin' || ($role === 'admin' && $canDeleteFolder)): ?>
            <button class="btn btn-danger mb-2  " data-toggle="modal" data-target="#deleteFolderModal">
                <i class="fa fa-trash me-2"></i> DELETE FOLDER
            </button>
        <?php endif; ?>
    </div>

<?php elseif (isset($depth) && $depth < 3): ?>
    <div class="button-container">
        <?php if ($role === 'superadmin' || ($role === 'admin' && $canAddSubfolder)): ?>
            <button class="btn btn-warning d-flex align-items-center mb-2" data-toggle="modal" data-target="#addSubFolderModal">
                <i class="fa fa-plus me-2"></i> ADD SUBFOLDER
            </button>
        <?php endif; ?>

        <?php if ($role === 'superadmin' || ($role === 'admin' && $canDeleteSubfolder)): ?>
            <button class="btn btn-danger d-flex align-items-center mb-2" data-toggle="modal" data-target="#deleteSubFolderModal">
                <i class="fa fa-trash me-2"></i> DELETE SUBFOLDER
            </button>
        <?php endif; ?>
    </div>
<?php endif; ?>


    <!-- Flash Messages -->
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <!-- Breadcrumb -->
    <?php if (isset($breadcrumb) && !empty($breadcrumb)): ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url($role . '/files') ?>">FILES</a></li>
                <?php foreach ($breadcrumb as $index => $crumb): ?>
                    <?php if ($index === array_key_last($breadcrumb)): ?>
                        <li class="breadcrumb-item active"><?= esc($crumb['folder_name']) ?></li>
                    <?php else: ?>
                        <li class="breadcrumb-item">
                            <a href="<?= site_url($role . '/files/view/' . $crumb['id']) ?>"><?= esc($crumb['folder_name']) ?></a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ol>
        </nav>
    <?php endif; ?>

    <!-- Folder List -->
    <div class="folder-container">
        <?php if (!empty($folders)): ?>
            <?php foreach ($folders as $folder): ?>
                <a href="<?= site_url($role . '/files/view/' . $folder['id']) ?>" class="folder">
                    <i class="fad fa-folder-open"></i>
                    <div class="folder-name"><?= esc($folder['folder_name']) ?></div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <?php if (!isset($depth) || $depth < 3): ?>
                <div class="alert alert-info">
                    <?= $parentFolder ?? false ? 'No subfolders inside "' . esc($parentFolder['folder_name']) . '".' : 'No folders available.' ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

<!-- Upload + Files Table (depth 3 only) -->
<?php if (isset($depth) && $depth === 3): ?>
    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">
            <i class="fa fa-upload mr-2"></i> Upload File
        </button>
    </div>

    <!-- 🧭 Tabs -->
    <ul class="nav nav-tabs" id="fileTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="active-tab" data-toggle="tab" href="#active" role="tab">Active Files</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="archived-tab" data-toggle="tab" href="#archived" role="tab">Archived Files</a>
        </li>
    </ul>

    <div class="tab-content mt-3" id="fileTabsContent">

        <!-- 🟢 ACTIVE FILES TAB -->
        <div class="tab-pane fade show active" id="active" role="tabpanel">
            <?php if (!empty($activeFiles)): ?>
                <h5>📂 Active Files in <?= esc($parentFolder['folder_name']) ?></h5>
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>File Name</th>
                            <th>Category</th>
                            <th>Uploaded By</th>
                            <th>Date Uploaded</th>
                            <th>Date Archived</th>
                            <th>Date Expired</th>
                            <th>Status</th>
                            <th style="width: 250px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activeFiles as $file): ?>
                            <tr>
                                <td><?= $file['id'] ?></td>
                                <td><?= esc($file['file_name']) ?></td>
                                <td><?= esc($file['category_name'] ?? 'Uncategorized') ?></td>
                                <td><?= esc($file['uploader_name'] ?? 'Unknown') ?></td>

                                <td><?= $file['uploaded_at'] ? date('Y-m-d H:i', strtotime($file['uploaded_at'])) : '—' ?></td>
                                <td><?= $file['archived_at'] ? date('Y-m-d H:i', strtotime($file['archived_at'])) : '—' ?></td>
                                <td><?= $file['expired_at'] ? date('Y-m-d H:i', strtotime($file['expired_at'])) : '—' ?></td>

                                <td>
                                    <?php
                                        $status = strtolower($file['status'] ?? 'pending');
                                        switch ($status) {
                                            case 'pending':
                                                $badge = 'badge-warning';
                                                break;
                                            case 'active':
                                                $badge = 'badge-success';
                                                break;
                                            case 'archived':
                                                $badge = 'badge-secondary';
                                                break;
                                            case 'expired':
                                                $badge = 'badge-danger';
                                                break;
                                            default:
                                                $badge = 'badge-light';
                                        }
                                    ?>
                                    <span class="badge <?= $badge ?>"><?= ucfirst($status) ?></span>
                                </td>

                                <td>
                                    <a href="<?= site_url($role . '/files/viewFile/' . $file['id']) ?>" target="_blank" class="btn btn-sm btn-info">View</a>
                                    <a href="<?= site_url($role . '/files/download/' . $file['id']) ?>" class="btn btn-sm btn-success">Download</a>
                                    <form action="<?= site_url($role . '/files/deleteFile/' . $file['id']) ?>" method="post" style="display:inline;">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this file?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted">No active files found in this folder.</p>
            <?php endif; ?>
        </div>

        <!-- 🗄️ ARCHIVED FILES TAB -->
        <div class="tab-pane fade" id="archived" role="tabpanel">
            <?php if (!empty($archivedFiles)): ?>
                <h5>🗄️ Archived Files in <?= esc($parentFolder['folder_name']) ?></h5>
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>File Name</th>
                            <th>Category</th>
                            <th>Uploaded By</th>
                            <th>Date Uploaded</th>
                            <th>Date Archived</th>
                            <th>Date Expired</th>
                            <th>Status</th>
                            <th style="width: 200px;">Actions</th>
                        </tr>
                    </thead>
<tbody>
    <?php foreach ($archivedFiles as $file): ?>
        <tr>
            <td><?= $file['id'] ?></td>
            <td><?= esc($file['file_name']) ?></td>
            <td><?= esc($file['category_name'] ?? 'Uncategorized') ?></td>
            <td><?= esc($file['uploader_name'] ?? 'Unknown') ?></td>

            <td><?= $file['uploaded_at'] ? date('Y-m-d H:i', strtotime($file['uploaded_at'])) : '—' ?></td>
            <td><?= $file['archived_at'] ? date('Y-m-d H:i', strtotime($file['archived_at'])) : '—' ?></td>
            <td><?= $file['expired_at'] ? date('Y-m-d H:i', strtotime($file['expired_at'])) : '—' ?></td>

            <td>
                <?php
                    $status = strtolower($file['status'] ?? 'archived');
                    $badge = match ($status) {
                        'archived' => 'badge-secondary',
                        'expired'  => 'badge-danger',
                        default    => 'badge-light',
                    };
                ?>
                <span class="badge <?= $badge ?>"><?= ucfirst($status) ?></span>
            </td>

            <td>
                <a href="<?= site_url($role . '/files/viewFile/' . $file['id']) ?>" target="_blank" class="btn btn-sm btn-info">View</a>
                <a href="<?= site_url($role . '/files/download/' . $file['id']) ?>" class="btn btn-sm btn-success">Download</a>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>

                </table>
            <?php else: ?>
                <p class="text-muted">No archived files found in this folder.</p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>




<!-- Modals -->

<!-- Add Folder Modal -->
<?php if ($role === 'superadmin' || ($role === 'admin' && $canAddFolder)): ?>
<div class="modal fade" id="addFolderModal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <form method="post" action="<?= site_url($role . '/files/add') ?>">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Add Folder</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
        <div class="modal-body">
          <input type="text" name="folder_name" class="form-control" placeholder="Folder Name" required>
        </div>
        <div class="modal-footer"><button type="submit" class="btn btn-primary">Add</button></div>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<!-- Add Subfolder Modal -->
<?php if ($role === 'superadmin' || ($role === 'admin' && $canAddSubfolder)): ?>
<div class="modal fade" id="addSubFolderModal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <form method="post" action="<?= site_url($role . '/files/addSubfolder/' . ($parentFolder['id'] ?? 0)) ?>">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Add Subfolder</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
        <div class="modal-body">
          <input type="text" name="folder_name" class="form-control" placeholder="Subfolder Name" required>
        </div>
        <div class="modal-footer"><button type="submit" class="btn btn-primary">Add</button></div>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<!-- Delete Folder Modal -->

<?php if ($role === 'superadmin' || ($role === 'admin' && $canDeleteFolder)): ?>
<div class="modal fade" id="deleteFolderModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= site_url($role.'/files/delete') ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Folder</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <?php if (!empty($folders)): ?>
                        <ul class="list-group">
                            <?php foreach ($folders as $folder): ?>
                                <?php if (empty($folder['parent_folder_id'])): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?= esc($folder['folder_name']) ?>
                                    <button type="submit" name="delete_folder_id" value="<?= $folder['id'] ?>" class="btn btn-danger btn-sm">
                                        Delete
                                    </button>
                                </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="alert alert-info">No folders found.</div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="parent_folder_id" value="">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>




<!-- Delete Subfolder Modal -->
<?php if ($role === 'superadmin' || ($role === 'admin' && $canDeleteSubfolder)): ?>
<div class="modal fade" id="deleteSubFolderModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= site_url($role.'/files/deleteSubfolder') ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Subfolder in "<?= esc($parentFolder['folder_name'] ?? '') ?>"</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <?php if (!empty($folders)): ?>
                        <ul class="list-group">
                            <?php foreach ($folders as $folder): ?>
                                <?php if ($folder['parent_folder_id'] == ($parentFolder['id'] ?? 0)): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?= esc($folder['folder_name']) ?>
                                    <button type="submit" name="delete_folder_id" value="<?= $folder['id'] ?>" class="btn btn-danger btn-sm">
                                        Delete
                                    </button>
                                </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="alert alert-info">
                            No subfolders inside "<?= esc($parentFolder['folder_name'] ?? '') ?>".
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="parent_folder_id" value="<?= $parentFolder['id'] ?? '' ?>">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>


<!-- Upload File Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <form method="post" action="<?= site_url($role . '/files/upload/' . ($parentFolder['id'] ?? 0)) ?>" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Upload File</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
        <div class="modal-body">
          <div class="form-group">
            <label>Select File</label>
            <input type="file" name="upload_file" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Category</label>
            <select name="category_id" class="form-control" required>
              <option value="">-- Select Category --</option>
              <?php foreach ($categories ?? [] as $cat): ?>
                  <option value="<?= $cat['id'] ?>"><?= esc($cat['category_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer"><button type="submit" class="btn btn-primary">Upload</button></div>
      </div>
    </form>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
        <style>
.modal-content { border-radius: 8px; }
        .main-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            padding: 20px;
            margin-left: 280px;
            width: calc(100% - 280px);
            min-height: 100vh;
            background: #fff;
            overflow-y: auto;
        }
        .page-title {
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: bold;
            color: #3550A0;
        }
        .add-folder-btn {
            background-color: #3550A0;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
        }
        .add-folder-btn i { margin-right: 10px; }
        .add-subfolder-btn i { margin-right: 10px; }
        .add-subfolder-btn {
            background-color: #f0ad4e; /* Bootstrap warning yellow */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
        }
        .add-subfolder-btn:hover { background-color: #ec971f; }
        .add-folder-btn:hover { background-color: #4A68C0; }
        .folder-container {
            display: flex;
            flex-wrap: wrap;
            gap: 35px;
            width: 100%;
        }
        .folder {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #f9fafc;
            border: 1px solid #e1e5ee;
            border-radius: 6px;
            width: 200px;
            text-align: center;
            padding: 30px 30px;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-decoration: none;
        }
        .folder:hover {
            background: #e2e2e2;
            transform: translateY(-4px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.12);
        }
        .folder i {
            font-size: 60px;
            color: #ECB439;
        }
        .folder:hover i { color: #3550A0; }
        .folder-name {
            margin-top: 12px;
            font-size: 15px;
            font-weight: 600;
            color: #2f3e64;
            word-break: break-word;
        }
        .button-container { display: flex; gap: 20px; margin-bottom: 20px; }
        .delete-btn { background-color: #D9534F; }
        .delete-btn:hover { background-color: #B52D2D; }
        </style>

</html>
