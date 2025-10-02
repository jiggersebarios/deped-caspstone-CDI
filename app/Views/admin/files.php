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

        <style>
.sidebar {
    width: 280px;
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
}

.main-container {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    padding: 20px;
    margin-left: 220px; /* Match sidebar width */
    width: calc(100% - 220px); /* Match sidebar width */
    min-height: 100vh;
    background: #e2e2e2; /* Or your chosen background */
    overflow-y: auto;
}

            .page-title {
                margin-bottom: 20px;
                font-size: 24px;
                font-weight: bold;
                color: #3550A0;
            }
           
            .button-container {
                display: flex;
                justify-content: flex-end;
                gap: 15px;
                margin-bottom: 20px;
                flex-wrap: wrap;
               
            }
            @media (max-width: 576px) {
                .button-container {
                    flex-direction: column;
                    align-items: stretch;
                }
                .button-container button {
                    width: 100%;
                }
            }

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
                background: #afaeae6c;
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
                background: #e1e5ee;
                transform: translateY(-4px);
                box-shadow: 0 6px 12px rgba(0,0,0,0.12);
            }
            .folder i { font-size: 60px; color: #ECB439; }
            .folder:hover i { color: #3550A0; }
            .folder-name {
                margin-top: 12px;
                font-size: 15px;
                font-weight: 600;
                color: #2f3e64;
                word-break: break-word;
            }
        </style>
    </head>
    <body>

        <?= $this->include('admin/sidebar') ?>

        <div class="main-container">

            <!-- Search -->
            <?php if (!isset($parentFolder)): ?>
                <form action="<?= base_url('admin/files') ?>" method="get" class="form-inline mb-3">
                    <input type="text" name="search" class="form-control mr-2" placeholder="Search folders..."
                        value="<?= esc($search ?? '') ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            <?php else: ?>
                <form action="<?= site_url('admin/files/view/' . $parentFolder['id']) ?>" method="get" class="form-inline mb-3">
                    <input type="text" name="search" class="form-control mr-2" placeholder="Search subfolders..."
                        value="<?= esc($search ?? '') ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            <?php endif; ?>

            <!--  Buttons  -->
            <?php if (!isset($parentFolder)): ?>
    <div class="d-flex justify-content-end flex-wrap mb-3">
        <button class="btn btn-primary mr-2 mb-2" data-toggle="modal" data-target="#addFolderModal">
            <i class="fa fa-plus mr-2"></i> ADD FOLDER
        </button>
        <button class="btn btn-danger mb-2" data-toggle="modal" data-target="#deleteFolderModal">
            <i class="fa fa-trash mr-2"></i> DELETE FOLDER
        </button>
    </div>

            <?php elseif (isset($depth) && $depth < 3): ?>
                <div class="button-container">
                    <button class="btn btn-warning d-flex align-items-center" data-toggle="modal" data-target="#addSubFolderModal">
                        <i class="fa fa-plus mr-2"></i> ADD SUBFOLDER
                    </button>
                    <button class="btn btn-danger d-flex align-items-center" data-toggle="modal" data-target="#deleteSubFolderModal">
                        <i class="fa fa-trash mr-2"></i> DELETE SUBFOLDER
                    </button>
                </div>
            <?php endif; ?>

            <!-- Delete Folder Modal -->
<div class="modal fade" id="deleteFolderModal" tabindex="-1" role="dialog" aria-labelledby="deleteFolderModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="<?= base_url($role . '/files/delete') ?>" method="post">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteFolderModalLabel">Delete Folder</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <?php if (!empty($folders)): ?>
            <div class="form-group">
              <label for="delete_folder_id">Select Folder</label>
              <select name="delete_folder_id" id="delete_folder_id" class="form-control" required>
                <?php foreach ($folders as $folder): ?>
                  <option value="<?= $folder['id'] ?>"><?= esc($folder['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <input type="hidden" name="parent_folder_id" value="">
          <?php else: ?>
            <p class="text-muted">No folders available to delete.</p>
          <?php endif; ?>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <?php if (!empty($folders)): ?>
            <button type="submit" class="btn btn-danger">Delete</button>
          <?php endif; ?>
        </div>
      </div>
    </form>
  </div>
</div>


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
                        <li class="breadcrumb-item"><a href="<?= site_url('admin/files') ?>">FILES</a></li>
                        <?php foreach ($breadcrumb as $index => $crumb): ?>
                            <?php if ($index === array_key_last($breadcrumb)): ?>
                                <li class="breadcrumb-item active"><?= esc($crumb['folder_name']) ?></li>
                            <?php else: ?>
                                <li class="breadcrumb-item">
                                    <a href="<?= site_url('admin/files/view/' . $crumb['id']) ?>">
                                        <?= esc($crumb['folder_name']) ?>
                                    </a>
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
                        <a href="<?= site_url('admin/files/view/' . $folder['id']) ?>" class="folder">
                            <i class="fad fa-folder-open"></i>
                            <div class="folder-name"><?= esc($folder['folder_name']) ?></div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php if (!isset($depth) || $depth < 3): ?>
                        <div class="alert alert-info">
                            <?= $parentFolder ?? false
                                ? 'No subfolders inside "' . esc($parentFolder['folder_name']) . '".'
                                : 'No folders available.' ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            
    <!-- Add Folder Modal -->
    <div class="modal fade" id="addFolderModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="<?= site_url('admin/files/add') ?>" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <?= isset($parentFolder) 
                                ? 'Add Subfolder in "' . esc($parentFolder['folder_name']) . '"' 
                                : 'Add Folder' ?>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Folder Name</label>
                            <input type="text" class="form-control" name="folder_name" required>
                            <?php if (isset($parentFolder)): ?>
                                <input type="hidden" name="parent_folder_id" value="<?= $parentFolder['id'] ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">
                            <?= isset($parentFolder) ? 'Add Subfolder' : 'Add Folder' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Subfolder Modal -->
    <?php if (isset($parentFolder)): ?>
    <div class="modal fade" id="addSubFolderModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <!-- Pass parentId in URL -->
                <form action="<?= site_url('admin/files/addSubfolder/' . $parentFolder['id']) ?>" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Add Subfolder inside "<?= esc($parentFolder['folder_name']) ?>"
                        </h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Subfolder Name</label>
                            <input type="text" class="form-control" name="folder_name" required>
                            <!-- Hidden field to double-confirm parent id -->
                            <input type="hidden" name="parent_folder_id" value="<?= $parentFolder['id'] ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Subfolder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>


    <!-- Delete Folder Modal -->
    <div class="modal fade" id="deleteFolderModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="<?= site_url('admin/files/delete') ?>" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Folder</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <?php if (!empty($folders)): ?>
                            <ul class="list-group">
                                <?php foreach ($folders as $folder): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= esc($folder['folder_name']) ?>
                                        <button type="submit" name="delete_folder_id" value="<?= $folder['id'] ?>" class="btn btn-danger btn-sm">Delete</button>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <?= isset($parentFolder) 
                                    ? 'No subfolders inside "' . esc($parentFolder['folder_name']) . '".' 
                                    : 'No folders available.' ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Subfolder Modal -->
    <div class="modal fade" id="deleteSubFolderModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="<?= site_url('admin/files/deleteSubfolder') ?>" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Delete Subfolder in "<?= esc($parentFolder['folder_name'] ?? '') ?>"
                        </h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <?php if (!empty($folders)): ?>
                            <ul class="list-group">
                                <?php foreach ($folders as $folder): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= esc($folder['folder_name']) ?>
                                        <button type="submit" name="delete_folder_id" value="<?= $folder['id'] ?>" class="btn btn-danger btn-sm">
                                            Delete
                                        </button>
                                    </li>
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


            <!-- Upload + Files Table (only on depth 3) -->
            <?php if (isset($depth) && $depth === 3): ?>

    <!-- Upload Button -->
    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">
            <i class="fa fa-upload mr-2"></i> Upload File
        </button>
    </div>

<!-- Files Table -->
<?php if (!empty($files)): ?>
    <h5>Files in <?= esc($parentFolder['folder_name']) ?></h5>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>File Name</th>
                <th>Category</th>
                <th>Uploaded By</th>
                <th>Date Uploaded</th>
                <th>Status</th>
                <th style="width: 250px;">Actions</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($files as $file): ?>
    <tr>
        <td><?= $file['id'] ?></td>
        <td><?= esc($file['file_name']) ?></td>
        <td><?= esc($file['category_name'] ?? 'Uncategorized') ?></td>
        <td><?= esc($file['uploader_name'] ?? 'Unknown') ?></td>

        <td><?= date('Y-m-d H:i', strtotime($file['uploaded_at'])) ?></td>
        <td>
            <?php if ($file['archived_at']): ?>
                <span class="badge badge-secondary">Archived</span>
            <?php elseif ($file['deleted_at']): ?>
                <span class="badge badge-danger">Deleted</span>
            <?php else: ?>
                <span class="badge badge-success">Active</span>
            <?php endif; ?>
        </td>
        <td>
            <a href="<?= base_url('writable/uploads/files/' . $file['file_path']) ?>"
               target="_blank"
               class="btn btn-sm btn-info">View</a>
            <a href="<?= site_url('admin/files/download/' . $file['id']) ?>"
               class="btn btn-sm btn-success">Download</a>
            <form action="<?= site_url('admin/files/deleteFile/' . $file['id']) ?>"
                  method="post"
                  style="display:inline;">
                <button type="submit"
                        class="btn btn-sm btn-danger"
                        onclick="return confirm('Delete this file?')">Delete</button>
            </form>
        </td>
    </tr>
<?php endforeach; ?>

        </tbody>
    </table>
<?php endif; ?>


    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="<?= site_url('admin/files/upload/' . $parentFolder['id']) ?>"
                      method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload File to "<?= esc($parentFolder['folder_name']) ?>"</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Select Category</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">-- Select Category --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= esc($cat['category_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Choose File</label>
                            <input type="file" name="upload_file" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php endif; ?>


        </div> <!-- end main-container -->

        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>
