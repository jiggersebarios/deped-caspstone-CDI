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
            max-width: 800px;
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

        .category-form {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
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
        .btn-delete { background-color: #dc3545; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; }
        .btn-delete:hover { background-color: #c82333; }

        #alert-box {
            max-width: 800px;
            margin: 0 auto 20px auto;
        }
    </style>
</head>
<body>

<?= $this->include('superadmin/sidebar') ?>

<div class="content">
    <h2 class="page-header mb-4"><i class="fas fa-tags"></i> Categories</h2>

    <form action="<?= site_url('superadmin/category/add') ?>" method="post" class="category-form">
        <input type="text" name="category_name" placeholder="Category Name" required>
        <input type="number" name="retention_years" placeholder="Retention Years" required>
        <button type="submit">Add Category</button>
    </form>

    <table class="table table-bordered align-middle category-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category Name</th>
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
                        <td><?= esc($cat['retention_years']) ?> year(s)</td>
                        <td>
                            <a href="<?= site_url('superadmin/category/edit/' . $cat['id']) ?>" class="btn-edit">Edit</a>
                            <a href="<?= site_url('superadmin/category/delete/' . $cat['id']) ?>" class="btn-delete" onclick="return confirm('Delete this category?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4" class="text-center text-muted">No categories found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
