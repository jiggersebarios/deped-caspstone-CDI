<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        .content {
            margin-left: 220px; 
            padding: 20px;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .category-table {
            max-width: 900px;
            margin: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .category-table th {
            background-color: #2C2C2C;
            color: #fff;
            text-transform: uppercase;
            font-size: 14px;
        }

        .category-table td {
            font-size: 14px;
            vertical-align: middle;
        }

        .category-form input {
            padding: 8px 12px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .category-form button {
            padding: 8px 15px;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            border: none;
        }

        .category-form button:hover {
            background-color: #0056b3;
        }

        .btn-edit { background-color: #ffc107; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; }
        .btn-edit:hover { background-color: #e0a800; }
        .btn-delete { background-color: #dc3545; color: white; padding: 6px 12px; border-radius: 4px;  }
        .btn-delete:hover { background-color: #c82333; }

        #alert-box {
            max-width: 900px;
            margin: 0 auto 20px auto;
        }
    </style>
</head>
<body>

<?= $this->include('superadmin/sidebar') ?>

<div class="content">
    <h2 class="page-header mb-4"><i class="fas fa-tags"></i> Categories</h2>

    <!-- Button trigger modal for normal category -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        <i class="fas fa-plus"></i> Add Category
    </button>

    <!-- Modal for adding normal category -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form action="<?= site_url('superadmin/category/add') ?>" method="post">
            <div class="modal-header">
              <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex flex-column gap-2">
                <input type="text" name="category_name" class="form-control" placeholder="Category Name" required>
                <input type="number" name="archive_years" class="form-control" placeholder="Archive Years" required>
                <input type="number" name="retention_years" class="form-control" placeholder="Retention Years" required>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Add</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Demo Category Form -->
 <h4>for Demo</h4> 
 <form action="<?= site_url('superadmin/category/addDemo') ?>" method="post" class="category-form mb-4"> 
    <input type="text" name="category_name" placeholder="Demo Category Name" required> 
    <input type="number" name="archive_seconds" placeholder="Archive After Seconds" required> 
    <input type="number" name="retention_seconds" placeholder="Retention After Archive (Seconds)" required> 
    <button type="submit">Add</button> 
</form>

    <!-- Categories Table -->
    <table class="table table-bordered align-middle category-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Archive Years</th>
                <th>Retention Years</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= esc($cat['id']) ?></td>
                        <td><?= esc($cat['category_name']) ?></td>
                        <td><?= esc($cat['archive_after_years'] ?? 0) ?> year(s)</td>
                        <td><?= esc($cat['retention_years'] ?? 0) ?> year(s)</td>
                        <td class="d-flex gap-2">
                            <a href="<?= site_url('superadmin/category/edit/' . $cat['id']) ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="<?= site_url('superadmin/category/delete/' . $cat['id']) ?>" method="post" style="display:inline;">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this category?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center text-muted">No categories found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
