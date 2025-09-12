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
.add-subfolder-btn:hover {
    background-color: #ec971f; /* darker yellow on hover */
}
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
</head>
<body>

    <?= $this->include('admin/sidebar') ?>

    <div class="main-container">

<?php if (!isset($parentFolder)): ?>
    <!-- Search MAIN folders -->
    <form action="<?= base_url('admin/files') ?>" method="get" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2"
               placeholder="Search folders..."
               value="<?= esc($search ?? '') ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
    
<?php else: ?>
    <!-- Search SUBfolders -->
    <form action="<?= site_url('admin/files/view/' . $parentFolder['id']) ?>" method="get" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2"
               placeholder="Search subfolders..."
               value="<?= esc($search ?? '') ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
<?php endif; ?>

<?php if (!isset($parentFolder)): ?>
    <!-- On MAIN folder page -->
    <div class="button-container">
        <button class="add-folder-btn" data-toggle="modal" data-target="#addFolderModal">
            <i class="fa fa-plus"></i> ADD FOLDER
        </button>

        <button class="add-folder-btn delete-btn" data-toggle="modal" data-target="#deleteFolderModal">
            <i class="fa fa-trash"></i> DELETE FOLDER
        </button>
    </div>
<?php else: ?>
    <!-- On SUBFOLDER page -->
    <div class="button-container">
        <button class="add-subfolder-btn" data-toggle="modal" data-target="#addSubFolderModal">
            <i class="fa fa-plus"></i> ADD SUBFOLDER
        </button>

        <button class="add-folder-btn delete-btn" data-toggle="modal" data-target="#deleteSubFolderModal">
            <i class="fa fa-trash"></i> DELETE SUBFOLDER
        </button>
    </div>
<?php endif; ?>

        <!-- Flash messages -->
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

<?php if (isset($breadcrumb) && !empty($breadcrumb)): ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= site_url('admin/files') ?>">FILES</a>
            </li>
            <?php foreach ($breadcrumb as $index => $crumb): ?>
                <?php if ($index === array_key_last($breadcrumb)): ?>
                    <li class="breadcrumb-item active">
                        <?= esc($crumb['folder_name']) ?>
                    </li>
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
        <div class="alert alert-info">
            <?= isset($parentFolder) 
                ? 'No subfolders inside "' . esc($parentFolder['folder_name']) . '".' 
                : 'No folders available.' ?>
        </div>
    <?php endif; ?>
</div>



        
    </div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if(session()->getFlashdata('error')): ?>
        alert("<?= session()->getFlashdata('error') ?>");
    <?php endif; ?>

    <?php if(session()->getFlashdata('success')): ?>
        alert("<?= session()->getFlashdata('success') ?>");
    <?php endif; ?>
});
</script>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
