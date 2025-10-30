<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Manage Categories') ?></title>
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
        .categories-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
        .categories-table th {
            background: #2C2C2C;
            color: white;
            text-transform: uppercase;
            font-size: 14px;
        }
        .categories-table td {
            font-size: 14px;
            vertical-align: middle;
        }
        .btn-action {
            margin-right: 8px;
        }
        /* Modal styling */
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

<?= $this->include('superadmin/sidebar') ?>


<div class="content">
    <h2 class="page-header"><i class="fa-solid fa-tags"></i> Manage Categories</h2>

    <!-- Add Category Button -->
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        <i class="fa fa-plus"></i> Add Category
    </button>

    <!-- Categories Table -->
    <table class="table table-bordered categories-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Archive After</th>
                <th>Retention After</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= esc($cat['id']) ?></td>
                        <td><?= esc($cat['category_name']) ?></td>
                        <td><?= esc($cat['archive_after_value']) . ' ' . esc($cat['archive_after_unit']) ?></td>
                        <td><?= esc($cat['retention_value']) . ' ' . esc($cat['retention_unit']) ?></td>
                        <td><?= esc($cat['description']) ?></td>
                        <td>
                            <!-- Edit -->
                            <button class="btn btn-primary btn-sm btn-action"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editCategoryModal<?= $cat['id'] ?>">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                    <form action="<?= site_url('superadmin/category/delete/'.$cat['id']) ?>" method="post" style="display:inline;" onsubmit="return confirm('Delete this category?')">
                                        <button type="submit" class="btn btn-danger btn-sm btn-action">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </form>
                                    </td>
                                    </tr>

                                    <!-- Edit Category Modal -->
                                    <div class="modal fade" id="editCategoryModal<?= $cat['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="<?= site_url('superadmin/category/update/'.$cat['id']) ?>" method="post">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Category</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label>Category Name</label>
                                            <input type="text" name="category_name" class="form-control"
                                                   value="<?= esc($cat['category_name']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Description</label>
                                            <textarea name="description" class="form-control"><?= esc($cat['description']) ?></textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label>Archive After</label>
                                                <div class="input-group">
                                                    <input type="number" name="archive_after_value" class="form-control"
                                                           value="<?= esc($cat['archive_after_value']) ?>" required>
                                                    <select name="archive_after_unit" class="form-select">
                                                        <option value="years" <?= $cat['archive_after_unit']=='years'?'selected':'' ?>>Years</option>
                                                        <option value="months" <?= $cat['archive_after_unit']=='months'?'selected':'' ?>>Months</option>
                                                        <option value="days" <?= $cat['archive_after_unit']=='days'?'selected':'' ?>>Days</option>
                                                        <option value="hours" <?= $cat['archive_after_unit']=='hours'?'selected':'' ?>>Hours</option>
                                                        <option value="minutes" <?= $cat['archive_after_unit']=='minutes'?'selected':'' ?>>Minutes</option>
                                                        <option value="seconds" <?= $cat['archive_after_unit']=='seconds'?'selected':'' ?>>Seconds</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label>Retention After</label>
                                                <div class="input-group">
                                                    <input type="number" name="retention_value" class="form-control"
                                                           value="<?= esc($cat['retention_value']) ?>" required>
                                                    <select name="retention_unit" class="form-select">
                                                        <option value="years" <?= $cat['retention_unit']=='years'?'selected':'' ?>>Years</option>
                                                        <option value="months" <?= $cat['retention_unit']=='months'?'selected':'' ?>>Months</option>
                                                        <option value="days" <?= $cat['retention_unit']=='days'?'selected':'' ?>>Days</option>
                                                        <option value="hours" <?= $cat['retention_unit']=='hours'?'selected':'' ?>>Hours</option>
                                                        <option value="minutes" <?= $cat['retention_unit']=='minutes'?'selected':'' ?>>Minutes</option>
                                                        <option value="seconds" <?= $cat['retention_unit']=='seconds'?'selected':'' ?>>Seconds</option>
                                                    </select>
                                                </div>
                                            </div>
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
                <tr><td colspan="6" class="text-center text-muted">No categories found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('superadmin/category/store') ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Category Name</label>
                        <input type="text" name="category_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Archive After</label>
                            <div class="input-group">
                                <input type="number" name="archive_after_value" class="form-control" min="0" value="0" required>
                                <select name="archive_after_unit" class="form-select">
                                    <option value="years">Years</option>
                                    <option value="months">Months</option>
                                    <option value="days" selected>Days</option>
                                    <option value="hours">Hours</option>
                                    <option value="minutes">Minutes</option>
                                    <option value="seconds">Seconds</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Retention After</label>
                            <div class="input-group">
                                <input type="number" name="retention_value" class="form-control" min="0" value="0" required>
                                <select name="retention_unit" class="form-select">
                                    <option value="years">Years</option>
                                    <option value="months">Months</option>
                                    <option value="days" selected>Days</option>
                                    <option value="hours">Hours</option>
                                    <option value="minutes">Minutes</option>
                                    <option value="seconds">Seconds</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
