<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
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
        .users-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
        .users-table th {
            background: #2C2C2C;
            color: white;
            text-transform: uppercase;
            font-size: 14px;
        }
        .users-table td {
            font-size: 14px;
            vertical-align: middle;
        }
        .btn-action {
            margin-right: 8px;
        }
        /* Modal tweaks */
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

    <!-- Sidebar -->
    <?= $this->include('superadmin/sidebar') ?>

    <div class="content">
        <h2 class="page-header"><i class="fa-solid fa-users"></i> Manage Users</h2>
    
        
    <!-- ✅ Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    
        <!-- Add User Button -->
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fa fa-plus"></i> Add User
        </button>

  <!-- Users Table -->
        <table class="table table-bordered users-table">
            <thead>
                <tr>
                    <th>School ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Main Folder ID</th>
                    <th>Main Folder</th>
                    <th>Created</th>
                    <th>Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= esc($user['school_id'] ?? 'N/A') ?></td>
                            <td><?= esc($user['username']) ?></td>
                            <td><?= esc($user['email']) ?></td>
                            <td><?= esc($user['role']) ?></td>
                            <td><?= esc($user['main_folder_id'] ?? '—') ?></td>
                            <td><?= esc($user['main_folder'] ?? '—') ?></td>
                            <td><?= esc($user['created_at']) ?></td>
                            <td><?= esc($user['updated_at']) ?></td>
                            <td>
                                <!-- Edit button -->
                                <button class="btn btn-primary btn-sm btn-action" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editUserModal<?= $user['id'] ?>">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <!-- Delete -->
                                <a href="<?= site_url('superadmin/manage_users/delete/'.$user['id']) ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Delete this user?')">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal<?= $user['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('superadmin/manage_users/update/'.$user['id']) ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" value="<?= esc($user['username']) ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= esc($user['email']) ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>School ID</label>
                        <input type="text" name="school_id" value="<?= esc($user['school_id'] ?? '') ?>" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" class="form-select" required>
                            <option value="superadmin" <?= $user['role']=='superadmin'?'selected':'' ?>>Superadmin</option>
                            <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
                            <option value="user" <?= $user['role']=='user'?'selected':'' ?>>User</option>
                        </select>
                    </div>

                    <!-- ✅ New Folder Assignment Dropdown -->
                    <div class="mb-3">
                        <label>Main Folder</label>
                        <select name="main_folder_id" class="form-select">
                            <option value="">-- Select Folder --</option>
                            <?php foreach ($folders as $folder): ?>
                                <?php if (empty($folder['parent_folder_id'])): ?>
                                    <option value="<?= $folder['id']; ?>"
                                        <?= isset($user['main_folder_id']) && $user['main_folder_id'] == $folder['id'] ? 'selected' : ''; ?>>
                                        <?= esc($folder['folder_name']); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Password (leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>


                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>


    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?= site_url('superadmin/manage_users/store') ?>" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title">Add User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>School ID</label>
                            <input type="text" name="school_id" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Role</label>
                            <select name="role" class="form-select" required>
                                <option value="superadmin">Superadmin</option>
                                <option value="admin">Admin</option>
                                <option value="user" selected>User</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
