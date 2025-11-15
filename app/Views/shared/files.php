<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Files - HR Archiving System</title>


    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


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
<?php if (in_array($role, ['admin', 'superadmin'])): ?>
    <h3 class="page-header mb-4">
        <i class="fa-solid fa-folder-open"></i>
         Files Management
    </h3>
<?php endif; ?> 

<div class="top-bar d-flex align-items-center mb-3">

<!-- Page Header (Users only) -->
<?php if ($role === 'user'):
    // Ensure we have a username (fetch from session if not provided)
    $username = $username ?? session()->get('username') ?? session()->get('name') ?? 'User';
    $username = ucfirst(strtolower($username)); // Capitalize first letter only
?>
   <h2 class="page-header">
       <i class="fa-solid fa-folder-open"></i> 
       <?= esc($username) ?> Files Management
   </h2>
<?php endif; ?>
   <!-- Flash Messages -->
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

<!-- Top bar: Buttons + Search Bar -->
<?php if (!isset($parentFolder) || (isset($depth) && $depth < 3)): ?>
    <div class="top-bar d-flex justify-content-between align-items-center mb-3">

        <!-- Folder / Subfolder Buttons -->
        <div class="button-container">
            <?php if (!isset($parentFolder)): ?>
                
                <!-- Add Folder -->
                <?php if ($role === 'superadmin' || ($role === 'admin' && $canAddFolder)): ?>
                    <button class="add-folder-btn" data-toggle="modal" data-target="#addFolderModal">
                        <i class="fas fa-folder-plus"></i> Add Folder
                    </button>
                <?php endif; ?>

                <!-- Edit Main Folder -->
                <?php if ($role === 'superadmin' || ($role === 'admin' && $canEditFolder)): ?>
                    <button class="edit-subfolder-btn" data-toggle="modal" data-target="#editMainFolderModal">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Folder
                    </button>
                <?php endif; ?>

                <!-- Delete Folder -->
                <?php if ($role === 'superadmin' || ($role === 'admin' && $canDeleteFolder)): ?>
                    <button class="delete-folder-btn" data-toggle="modal" data-target="#deleteFolderModal">
                        <i class="fas fa-trash-alt"></i> Delete Folder
                    </button>
                <?php endif; ?>

            <?php else: ?>
                
                <!-- Add Subfolder -->
                <?php if ($role === 'superadmin' || ($role === 'admin' && $canAddSubfolder)): ?>
                    <button class="add-subfolder-btn" data-toggle="modal" data-target="#addSubFolderModal">
                        <i class="fa-solid fa-folder-tree"></i> Add Subfolder
                    </button>
                <?php endif; ?>

                <!-- Edit Subfolder -->
                <?php if ($role === 'superadmin' || ($role === 'admin' && $canEditSubfolder)): ?>
                    <button class="edit-subfolder-btn" data-toggle="modal" data-target="#editSubFolderModal">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Subfolder
                    </button>
                <?php endif; ?>

                <!-- Delete Subfolder -->
                <?php if ($role === 'superadmin' || ($role === 'admin' && $canDeleteSubfolder)): ?>
                    <button class="delete-btn" data-toggle="modal" data-target="#deleteSubFolderModal">
                        <i class="fa-solid fa-trash"></i> Delete Subfolder
                    </button>
                <?php endif; ?>

            <?php endif; ?>
        </div>

        <!-- Search folder Bar -->
        <div class="search-bar">
            <form action="<?= isset($parentFolder) ? site_url($role . '/files/view/' . $parentFolder['id']) : base_url($role . '/files') ?>" method="get">
                <div class="input-group">
                    <input type="text" name="search" class="form-control"
                        placeholder="<?= isset($parentFolder) ? 'Search subfolders...' : 'Search folders...' ?>"
                        value="<?= esc($search ?? '') ?>" aria-label="Search" aria-describedby="search-addon">
                    <button class="btn btn-outline-secondary" type="submit" id="search-addon">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>

    </div>
<?php endif; ?>

</div>

 

<?php if (!empty($breadcrumb) && $depth < 3): ?>
    <nav aria-label="breadcrumb" class="custom-breadcrumb" style="margin-left: 20px; margin-top: -40px; ">
        <ol class="breadcrumb mb-3">
            <?php foreach ($breadcrumb as $index => $crumb): ?>
                <?php 
                    $crumbId = $crumb['id'] ?? null;
                    $crumbName = $crumb['name'] ?? $crumb['folder_name'] ?? 'Unnamed';
                ?>
                <?php if ($index < count($breadcrumb) - 1 && $crumbId): ?>
                    <li class="breadcrumb-item">
                        <a href="<?= site_url($role . '/files/view/' . $crumbId) ?>">
                            <?= esc($crumbName) ?>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?= esc($crumbName) ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ol>
    </nav>
<?php endif; ?>

<!-- Upload + Files Table (depth 3 only) -->
<?php if (isset($depth) && $depth === 3): ?>
    <!-- Action Bar for Depth 3 -->
    <div class="top-bar d-flex justify-content-between align-items-center mb-3">
        <?php if ($enableUpload): ?>
            <!-- Upload button -->
            <button class="btn btn-primary" data-toggle="modal" data-target="#uploadModal" style="margin-left: 20px;">
                <i class="fa fa-upload mr-2"></i> Upload File
            </button>
        <?php else: ?>
            <!-- Spacer to keep search bar on the right -->
            <div></div>
        <?php endif; ?>

        <!-- File Search Bar -->
        <div class="search-bar">
            <form action="<?= current_url() ?>" method="get">
                <div class="input-group">
                    <input type="text" name="search" class="form-control"
                           placeholder="Search files..."
                           value="<?= esc($search ?? '') ?>"
                           aria-label="Search files" aria-describedby="search-addon">
                    <button class="btn btn-outline-secondary" type="submit" id="search-addon">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Breadcrumb -->
    <?php if (!empty($breadcrumb)): ?>
        <nav aria-label="breadcrumb" class="custom-breadcrumb">
            <ol class="breadcrumb mb-3">
                <?php foreach ($breadcrumb as $index => $crumb): ?>
                    <?php
                        $crumbId   = $crumb['id'] ?? null;
                        $crumbName = $crumb['name'] ?? $crumb['folder_name'] ?? 'Unnamed';
                    ?>
                    <?php if ($index < count($breadcrumb) - 1 && $crumbId !== null): ?>
                        <li class="breadcrumb-item">
                            <a href="<?= site_url($role . '/files/view/' . $crumbId) ?>">
                                <?= esc($crumbName) ?>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= esc($crumbName) ?>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ol>
        </nav>
    <?php endif; ?>

    <!-- Tabs -->
    <ul class="nav nav-tabs" id="fileTabs" role="tablist" style ="margin-left: 20px;">
        <li class="nav-item">
            <a class="nav-link active" id="active-tab" data-toggle="tab" href="#active" role="tab">Active Files</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="archived-tab" data-toggle="tab" href="#archived" role="tab">Archived Files</a>
        </li>
        <li class="nav-item d-flex align-items-center">
  <a class="nav-link" id="expired-tab" data-toggle="tab" href="#expired" role="tab">Expired Files</a>
  <!-- Button appears beside Expired Files -->
<button id="showDeletedBtn" class="btn btn-sm btn-info ml-2 d-none">
  <i class="fa fa-trash"></i> Deleted Logs
</button>
</li>


    </ul>

<?php else: ?>
    <!-- Folder List (only shown when not at depth 3) -->
    <div class="folder-container">
        <?php if (!empty($folders)): ?>
            <?php foreach ($folders as $folder): ?>
                <a href="<?= site_url($role . '/files/view/' . $folder['id']) ?>" class="folder">
                    <i class="fad fa-folder-open"></i>
                    <div class="folder-name"><?= esc($folder['folder_name']) ?></div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info" style="margin-left: 20px;">
                <?= $parentFolder ?? false ? 'No subfolders inside "' . esc($parentFolder['folder_name']) . '".' : 'No folders available.' ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if (isset($depth) && $depth === 3): ?>
    <div class="tab-content mt-3" id="fileTabsContent">

        <!-- 🟢 ACTIVE FILES TAB -->
        <div class="tab-pane fade show active" id="active" role="tabpanel " >
            <?php if (!empty($activeFiles)): ?>
                <h4 style ="margin-left: 20px;">📂 Active Files in <?= esc($parentFolder['folder_name']) ?> </h5>
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>File Name</th>
                            <th>Category</th>
                            <th>File Size</th>
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

                                <!-- File Size -->
                                <td>
                                    <?php 
                                        if (!empty($file['file_size'])) {
                                            $size = $file['file_size'] < 1048576 
                                                ? round($file['file_size'] / 1024, 2) . ' KB' 
                                                : round($file['file_size'] / 1048576, 2) . ' MB';
                                            echo $size;
                                        } else {
                                            echo '—';
                                        }
                                    ?>
                                </td>

                                <td><?= esc($file['uploader_name'] ?? 'Unknown') ?></td>
                                <td><?= $file['uploaded_at'] ? date('Y-m-d h:i A', strtotime($file['uploaded_at'])) : '—' ?></td>
                                <td><?= $file['archived_at'] ? date('Y-m-d h:i A', strtotime($file['archived_at'])) : '—' ?></td>
                                <td><?= $file['expired_at'] ? date('Y-m-d h:i A', strtotime($file['expired_at'])) : '—' ?></td>

                                <!-- Status Badge -->
                                <td>
                                    <?php
                                        $status = strtolower($file['status'] ?? 'pending');
                                        switch ($status) {
                                            case 'pending': $badge = 'badge-warning'; break;
                                            case 'active': $badge = 'badge-success'; break;
                                            case 'archived': $badge = 'badge-secondary'; break;
                                            case 'expired': $badge = 'badge-danger'; break;
                                            default: $badge = 'badge-light';
                                        }
                                    ?>
                                    <span class="badge <?= $badge ?>"><?= ucfirst($status) ?></span>
                                </td>

                                <!--  Actions -->
                                 <td class="text-center">
    <div class="btn-group btn-group-sm" role="group">
        <a href="<?= site_url($role . '/files/viewFile/' . $file['id']) ?>" 
           target="_blank" class="btn btn-info">
            <i class="fa fa-eye"></i> View
        </a>

        <a href="<?= site_url($role . '/files/download/' . $file['id']) ?>" 
           class="btn btn-success">
            <i class="fa fa-download"></i> Download
        </a>

        <?php if ($enableEdit): ?>
       <!-- Edit button code -->
        <button type="button" 
    class="btn btn-warning" 
    data-toggle="modal" 
    data-target="#renameModal"
    data-file-id="<?= $file['id'] ?>" 
    data-file-name="<?= esc($file['file_name']) ?>">
    <i class="fa fa-edit"></i> Edit
</button>
         <?php endif; ?>

         <?php if ($enableDelete): ?>
   <!-- Delete button code -->
            <form action="<?= site_url($role . '/files/deleteFile/' . $file['id']) ?>" 
              method="post" class="d-inline">
            <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this file?')">
                <i class="fa fa-trash"></i> Delete
            </button>
        </form>
    <?php endif; ?>


    </div>
</td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted">No active files found in this folder.</p>
            <?php endif; ?>
        </div>

        <!--📂 ARCHIVED FILES TAB -->
        <div  class="tab-pane fade" id="archived" role="tabpanel" >
            <?php if (!empty($archivedFiles)): ?>
                <h5 style ="margin-left: 20px;">🗄️ Archived & Expired Files in <?= esc($parentFolder['folder_name']) ?></h5>
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>File Name</th>
                            <th>Category</th>
                            <th>File Size</th>
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

                                <!-- File Size -->
                                <td>
                                    <?php 
                                        if (!empty($file['file_size'])) {
                                            $size = $file['file_size'] < 1048576 
                                                ? round($file['file_size'] / 1024, 2) . ' KB' 
                                                : round($file['file_size'] / 1048576, 2) . ' MB';
                                            echo $size;
                                        } else {
                                            echo '—';
                                        }
                                    ?>
                                </td>

                                <td><?= esc($file['uploader_name'] ?? 'Unknown') ?></td>
                                <td><?= $file['uploaded_at'] ? date('Y-m-d h:i A', strtotime($file['uploaded_at'])) : '—' ?></td>
                                <td><?= $file['archived_at'] ? date('Y-m-d h:i A', strtotime($file['archived_at'])) : '—' ?></td>
                                <td><?= $file['expired_at'] ? date('Y-m-d h:i A', strtotime($file['expired_at'])) : '—' ?></td>

                                <td>
                                    <?php if ($file['status'] === 'expired'): ?>
                                        <span class="badge badge-danger">Expired</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Archived</span>
                                    <?php endif; ?>
                                </td>
<td>
    <?php if ($role === 'superadmin'): ?>
        <a href="<?= site_url($role . '/files/viewFile/' . $file['id']) ?>" 
           target="_blank" class="btn btn-info btn-sm">
            <i class="fa fa-eye"></i> View
        </a>

        <a href="<?= site_url($role . '/files/restore/' . $file['id']) ?>" 
           class="btn btn-success btn-sm"
           onclick="return confirm('Are you sure you want to restore this file and reset its archive/expiry countdown?');">
            <i class="fa fa-undo"></i> Restore
        </a>

    <?php else: ?>
        <button class="btn btn-sm btn-info" 
            data-toggle="modal" 
            data-target="#requestModal"
            data-file-id="<?= $file['id'] ?>"
            data-file-name="<?= esc($file['file_name']) ?>">
            Request
        </button>
    <?php endif; ?>
</td>
                                
                                        

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted" style ="margin-left: 20px;">No archived or expired files found in this folder.</p>
            <?php endif; ?>
        </div>

                <!-- EXPIRED FILES TAB -->
        <div class="tab-pane fade" id="expired" role="tabpanel">
            <?php if (!empty($expiredFiles)): ?>
                <h5 style ="margin-left: 20px;">⚠️ Expired Files in <?= esc($parentFolder['folder_name']) ?></h5>
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>File Name</th>
                            <th>Category</th>
                            <th>File Size</th>
                            <th>Uploaded By</th>
                            <th>Date Uploaded</th>
                            <th>Date Archived</th>
                            <th>Date Expired</th>
                            <th>Status</th>
                            <th style="width: 200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($expiredFiles as $file): ?>
                            <tr>
                                <td><?= $file['id'] ?></td>
                                <td><?= esc($file['file_name']) ?></td>
                                <td><?= esc($file['category_name'] ?? 'Uncategorized') ?></td>
                                <td>
                                    <?php 
                                        if (!empty($file['file_size'])) {
                                            $size = $file['file_size'] < 1048576 
                                                ? round($file['file_size'] / 1024, 2) . ' KB' 
                                                : round($file['file_size'] / 1048576, 2) . ' MB';
                                            echo $size;
                                        } else {
                                            echo '—';
                                        }
                                    ?>
                                </td>
                                <td><?= esc($file['uploader_name'] ?? 'Unknown') ?></td>
                                <td><?= $file['uploaded_at'] ? date('Y-m-d h:i A', strtotime($file['uploaded_at'])) : '—' ?></td>
                                <td><?= $file['archived_at'] ? date('Y-m-d h:i A', strtotime($file['archived_at'])) : '—' ?></td>
                                <td><?= $file['expired_at'] ? date('Y-m-d h:i A', strtotime($file['expired_at'])) : '—' ?></td>
                                <td><span class="badge badge-danger">Expired</span></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">

                                        <?php if ($enableDelete): ?>
                                            <form action="<?= site_url($role . '/files/deleteFile/' . $file['id']) ?>" method="post" class="d-inline">
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this expired file permanently?')">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>
                                        </form>
                                        <?php endif; ?>

         <a href="<?= site_url($role . '/files/restore/' . $file['id']) ?>" 
           class="btn btn-success btn-sm"
           onclick="return confirm('Are you sure you want to restore this file and reset its archive/expiry countdown?');">
            <i class="fa fa-undo"></i> Restore
        </a>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted" style ="margin-left: 20px;">No expired files found in this folder.</p>
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

<!-- Edit Main Folder Modal -->
<?php if ($role === 'superadmin' || ($role === 'admin' && $canEditFolder)): ?>
<div class="modal fade" id="editMainFolderModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= site_url($role.'/files/renameMainFolder') ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Main Folder</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p>Select a main folder to rename:</p>
                    <?php if (!empty($folders)): ?>
                        <div class="form-group">
                            <label for="folder_id">Main Folder</label>
                            <select name="folder_id" class="form-control" required>
                                <option value="">-- Select a folder --</option>
                                <?php foreach ($folders as $folder): ?>
                                    <option value="<?= $folder['id'] ?>"><?= esc($folder['folder_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="new_name">New Name</label>
                            <input type="text" name="new_name" class="form-control" placeholder="Enter new folder name" required>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">No main folders to edit.</div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" <?= empty($folders) ? 'disabled' : '' ?>>Rename</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Edit Subfolder Modal -->
<?php if ($role === 'superadmin' || ($role === 'admin' && $canEditSubfolder)): ?>
<div class="modal fade" id="editSubFolderModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= site_url($role.'/files/renameSubfolder') ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Subfolder in "<?= esc($parentFolder['folder_name'] ?? '') ?>"</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p>Select a subfolder to rename:</p>
                    <?php if (!empty($folders)): ?>
                        <div class="form-group">
                            <label for="folder_id">Subfolder</label>
                            <select name="folder_id" class="form-control" required>
                                <option value="">-- Select a subfolder --</option>
                                <?php foreach ($folders as $folder): ?>
                                    <option value="<?= $folder['id'] ?>"><?= esc($folder['folder_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="new_name">New Name</label>
                            <input type="text" name="new_name" class="form-control" placeholder="Enter new subfolder name" required>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">No subfolders to edit.</div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="parent_folder_id" value="<?= $parentFolder['id'] ?? '' ?>">
                    <button type="submit" class="btn btn-primary" <?= empty($folders) ? 'disabled' : '' ?>>Rename</button>
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

<!-- 📄 Request File Modal -->
<div class="modal fade" id="requestModal" tabindex="-1" role="dialog" aria-labelledby="requestModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
   <form action="<?= site_url('superadmin/request/submit') ?>" method="post">

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="requestModalLabel">Request File</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="file_id" id="request-file-id">

          <div class="form-group">
            <label>File Name</label>
            <input type="text" class="form-control" id="request-file-name" readonly>
          </div>

          <div class="form-group">
            <label for="request-reason">Reason for Request</label>
            <textarea name="reason" id="request-reason" class="form-control" rows="4" required placeholder="Please explain why you are requesting this file..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Submit Request</button>
        </div>
      </div>
    </form>
  </div>
</div>


<!-- 🟡 Rename File Modal -->
<div class="modal fade" id="renameModal" tabindex="-1" role="dialog" aria-labelledby="renameModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="post" action="<?= site_url($role . '/files/renameFile') ?>">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="renameModalLabel">Rename File</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="file_id" id="rename-file-id">
          <div class="form-group">
            <label for="rename-file-name">New File Name</label>
            <input type="text" name="new_name" id="rename-file-name" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- 🗑️ Deleted Files Modal -->
<div class="modal fade" id="deletedFilesModal" tabindex="-1" aria-labelledby="deletedFilesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 750px;"> <!-- wider modal -->
    <div class="modal-content border-0 shadow-sm rounded-3">
      <div class="modal-header bg-danger text-white py-2">
        <h6 class="modal-title fw-semibold mb-0" id="deletedFilesModalLabel">
          <i class="fa-solid fa-trash-can me-2"></i> Deleted Files
        </h6>
        
      </div>

      <div class="modal-body p-0">
        <!-- Column Headers -->
        <div class="d-flex fw-semibold text-secondary border-bottom bg-light px-3 py-2 small">
          <div class="flex-grow-1">File Name</div>
          <div class="text-nowrap" style="width: 150px;">Category</div>
          <div class="text-nowrap" style="width: 130px;">Deleted By</div>
          <div class="text-nowrap" style="width: 170px;">Deleted At</div>
        </div>

        <!-- Scrollable List -->
        <div id="deletedFilesList" class="list-group list-group-flush" style="max-height: 320px; overflow-y: auto;">
          <div class="text-center text-muted py-3">Loading...</div>
        </div>
      </div>

      <div class="modal-footer py-2 bg-light">
        <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal">Close</button>

      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
$('#renameModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var fileId = button.data('file-id');
    var fileName = button.data('file-name');

    var modal = $(this);
    modal.find('#rename-file-id').val(fileId);
    modal.find('#rename-file-name').val(fileName);
});

//request\
$('#requestModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var fileId = button.data('file-id');
    var fileName = button.data('file-name');

    var modal = $(this);
    modal.find('#request-file-id').val(fileId);
    modal.find('#request-file-name').val(fileName);
});


</script>

<script>
$(document).ready(function () {
    // Restore the previously active tab
    const activeTab = localStorage.getItem('activeFileTab');
    if (activeTab) {
        $('#fileTabs a[href="' + activeTab + '"]').tab('show');
    }

    // Save the selected tab on click
    $('#fileTabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeFileTab', $(e.target).attr('href'));
    });
});
</script>

<script>
    // Searchh
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('input[name="search"]');
    const searchForm  = searchInput.closest('form');

    searchInput.addEventListener('input', () => {
        if (searchInput.value.trim() === '') {
            searchForm.submit(); // submit empty search to show all
        }
    });
});
</script>

<script>
$(document).ready(function () {
  const showDeletedBtn = $('#showDeletedBtn');

  // Show "Deleted Files" button only on Expired tab
  $('#expired-tab').on('shown.bs.tab', function () {
    showDeletedBtn.removeClass('d-none');
  });

  // Hide on other tabs
  $('#active-tab, #archived-tab').on('shown.bs.tab', function () {
    showDeletedBtn.addClass('d-none');
  });

  // When "Deleted Files" button clicked
  showDeletedBtn.on('click', function () {
    const listContainer = $('#deletedFilesList');
    listContainer.html('<div class="text-center text-muted py-3">Loading...</div>');

    $.ajax({
      url: "<?= base_url($role . '/files/getDeletedFiles') ?>",
      type: "GET",
      dataType: "json",
      cache: false,
      success: function (data) {
        console.log("Deleted files:", data);
        listContainer.empty();

        if (!data || data.length === 0) {
          listContainer.html('<div class="text-center text-muted py-3">No deleted files found.</div>');
        } else {
          data.forEach(file => {
            listContainer.append(`
              <div class="list-group-item">
                <div class="file-name">${file.file_name ?? '(Unnamed File)'}</div>
                <div class="file-category">${file.category_name ?? 'Uncategorized'}</div>
                <div class="file-deleted-by">${file.deleted_by_name ?? 'Unknown'}</div>
                <div class="file-deleted-at">${file.deleted_at ? new Date(file.deleted_at).toLocaleString() : '—'}</div>
              </div>
            `);
          });
        }

        $('#deletedFilesModal').modal('show');
      },
      error: function (xhr, status, error) {
        console.error('❌ Error loading deleted files:', error, xhr.responseText);
        alert('Failed to load deleted files.');
      }
    });
  });
});
</script>

</body>
</html>
<style>
#deletedFilesList .list-group-item {
  display: flex;
  align-items: center;
  font-size: 14px;
  padding: 8px 12px;
  border: none;
  border-bottom: 1px solid #f1f1f1;
  transition: background-color 0.2s;
}
#deletedFilesList .list-group-item:hover {
  background-color: #f8f9fa;
}
#deletedFilesList .file-name {
  flex-grow: 1;
  font-weight: 500;
  color: #212529;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
#deletedFilesList .file-category,
#deletedFilesList .file-deleted-by,
#deletedFilesList .file-deleted-at {
  font-size: 13px;
  color: #6c757d;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
#deletedFilesList .file-category { width: 150px; }
#deletedFilesList .file-deleted-by { width: 130px; }
#deletedFilesList .file-deleted-at { width: 170px; }
#deletedFilesList::-webkit-scrollbar { width: 6px; }
#deletedFilesList::-webkit-scrollbar-thumb { background: #ccc; border-radius: 3px; }
/* ====== General Modal Styling ====== */
.modal-content {
    border-radius: 8px;
}

/* ====== Main Container ====== */
.main-container {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    padding: 20px;
    margin-left: 220px;
    width: calc(100% - 220px);
    min-height: 100vh;
    background: #fff;
    overflow-y: auto;
}

/* ====== Top Bar Alignment ====== */
.top-bar {
    display: flex;
    justify-content: space-between; 
    align-items: center;            
    width: 100%;
    margin-bottom: 15px;
    gap: 20px;                      
}

/* Ensure page header stays one line and large */
.page-header-container {
    display: flex;
    align-items: center;
}

.page-header {
    font-size: 36px;      
    font-weight: 700;
    color: #3550A0;
    display: flex;
    align-items: center;
    white-space: nowrap;
    margin-left: 20px;
}

.page-header i {
    font-size: 42px;
    margin-right: 12px;
    color: #3550A0;
}




/* ====== Unified Folder Button Styling ====== */
.add-folder-btn,
.add-subfolder-btn,
.edit-subfolder-btn,
.delete-folder-btn,
.delete-btn {
    min-width: 180px; 
    height: 46px; 
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 15px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 0 20px;
    transition: all 0.3s ease;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
    margin-left: 20px;
    margin-top: -20px;
}

/* ====== Individual Button Colors ====== */
.add-folder-btn {
    background: linear-gradient(135deg, #3550A0, #4A68C0);
    color: #fff;
}
.add-folder-btn:hover {
    background: linear-gradient(135deg, #4A68C0, #5c7dd6);
    transform: translateY(-2px);
}

/* ✅ Add Subfolder */
.add-subfolder-btn {
    background: linear-gradient(135deg, #34c759, #28a745);
    color: #fff;
}
.add-subfolder-btn:hover {
    background: linear-gradient(135deg, #28a745, #34c759);
    transform: translateY(-2px);
}

/* 🟡 Edit Subfolder */
.edit-subfolder-btn {
    background: linear-gradient(135deg, #ffc107, #e0a800);
    color: #212529; /* Dark text for better contrast on yellow */
}
.edit-subfolder-btn:hover {
    background: linear-gradient(135deg, #e0a800, #ffc107);
    transform: translateY(-2px);
}

/* 🗑 Delete Folder */
.delete-folder-btn,
.delete-btn {
    background: linear-gradient(135deg, #dc3545, #b52d2d);
    color: #fff;
}
.delete-folder-btn:hover,
.delete-btn:hover {
    background: linear-gradient(135deg, #b52d2d, #dc3545);
    transform: translateY(-2px);
}

/* ====== Ensure buttons align perfectly in top-bar ====== */
.button-container {
    display: flex;
    gap: 10px;
    align-items: center;
}



/* ====== Icon Size ====== */
.add-folder-btn i,
.add-subfolder-btn i,
.edit-subfolder-btn i,
.delete-btn i {
    font-size: 18px;
}

/* ====== Folder Grid ====== */
.folder-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
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
    margin-left: 20px;
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

.folder:hover i {
    color: #3550A0;
}

.folder-name {
    margin-top: 12px;
    font-size: 15px;
    font-weight: 600;
    color: #2f3e64;
    word-break: break-word;
}

/* ====== Button Containers ====== */
.button-container {
    display: flex;
    margin-bottom: 15px;
}

/* ====== Delete Button====== */
.delete-btn {
    background-color: #D9534F;
}

.delete-btn:hover {
    background-color: #B52D2D;
}

/* ====== Table Action Buttons ====== */
td .btn-group {
    display: flex;
    gap: 5px;
    justify-content: center;
}

.btn-group-sm .btn {
    padding: 4px 8px;
    font-size: 10px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Button colors */
.btn-info {
    background-color: #17a2b8;
    border: none;
}
.btn-info:hover {
    background-color: #138496;
}

.btn-success {
    background-color: #28a745;
    border: none;
}
.btn-success:hover {
    background-color: #218838;
}

.btn-danger {
    background-color: #dc3545;
    border: none;
}
.btn-danger:hover {
    background-color: #b52d2d;
}

.btn-warning {
    background-color: #ffc107;
    border: none;
}
.btn-warning:hover {
    background-color: #e0a800;
}

/* ====== Table General ====== */
.table {
    max-width: 100%;
    font-size: 0.95rem;
    border-collapse: collapse;
    border-spacing: 0;
    margin-left: 20px;
}

.table thead th {
    background-color: #343a40;
    color: white;
    text-align: center;
    vertical-align: middle;
}

.table td, .table th {
    vertical-align: middle;
    padding: 9px;
}

.btn-group-sm .btn {
    margin-right: 6px;
   
}

.folder-container {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.folder {
    text-align: center;
    padding: 15px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    width: 160px;
    background-color: #f8f9fa;
    transition: all 0.3s;
}

.folder:hover {
    background-color: #e9ecef;
    transform: scale(1.03);
}


.top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}
/* Buttons */
.btn-group-sm .btn {
    padding: 4px 8px;
    font-size: 11px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.search-bar {
    width: 500px;
    max-width: 800px;
    margin-top: -20px;
    margin-bottom: 13px;
    margin-right: 100px;
}

.search-bar .input-group {
    display: flex;
    flex-direction: row-reverse; /* puts button on the left */
    align-items: center;
    border-radius: 5px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.32);
}

.search-bar .form-control {
    border: none;
    padding-left: 15px;
    padding-right: 15px;
    height: 42px;
}

.search-bar .btn {
    border: none;
    padding: 13px 20px;
    background-color: #3550A0;
    color: white;
    transition: background-color 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.search-bar .btn:hover {
    background-color: #4A68C0;
}

.top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    width: 100%;
    margin-top: 15px;
}

.button-container {
    display: flex;
    gap: 5px;
}
.custom-breadcrumb {
    background: transparent;
    margin-left: 20px;
}

.custom-breadcrumb .breadcrumb-item a {
    color: #3550A0;
    text-decoration: none;
    font-weight: 500;
}

.custom-breadcrumb .breadcrumb-item a:hover {
    text-decoration: underline;
}

.custom-breadcrumb .breadcrumb-item.active {
    color: #555;
    font-weight: 600;
}


</style>
</html>
