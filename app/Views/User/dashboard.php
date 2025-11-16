<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= esc($title) ?></title>

<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.15.4/css/all.css">
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<style>
/* Basic body and main content */
body {
    background-color: #f8f9fa;
    font-family: Arial, sans-serif;
}
.main-content {
    margin-left: 220px; /* Match sidebar width */
    padding: 20px;
}

/* Cards */
.cards-wrapper {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}
.card {
    border-radius: 12px;
    height: 180px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #f9fafc;
    border: 1px solid #e1e5ee;
    text-align: center;
    cursor: pointer;
    padding: 20px;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}
.card i { font-size: 40px; color: #ECB439; margin-bottom: 12px; }
.card h5 { margin: 0; font-size: 16px; font-weight: 600; color: #2f3e64; }
.card h2 { margin: 8px 0 0; font-size: 26px; font-weight: bold; color: #333; }
.card:hover { background: #e2e2e2; transform: translateY(-4px); box-shadow: 0 6px 12px rgba(0, 0, 0, 0.12); }

/* Responsive */
@media (max-width: 992px) {
    .main-content { margin-left: 80px; padding: 20px 15px; }
    .cards-wrapper { grid-template-columns: 1fr; gap: 15px; }
    .card { height: 150px; }
}

/* Notification Panel */
.notification-panel {
    position: fixed; /* Fixed panel */
    top: 80px; /* Below header */
    right: 20px; /* Keep away from sidebar */
    width: 580px;
    max-height: 70vh;
    overflow-y: auto;
    z-index: 1050;
    padding: 5px;
}

/* Notification alerts */
.notification-panel .alert {
    padding: 12px 16px;
    font-size: 0.95rem; /* Increased base font size */
    border-radius: 8px;
    margin-bottom: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

/* Alert types */
.notification-panel .alert-danger { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
.notification-panel .alert-success { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }
.notification-panel .alert-info { background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460; }

/* Heading & text inside notifications */
.notification-panel .alert-heading { font-size: 1.05rem; font-weight: 600; margin-bottom: 5px; }
.notification-panel p { margin: 0; font-size: 0.9rem; } /* Increased message font size */
.notification-panel p strong { font-weight: 600; } /* Make "Reason:" bold */

/* Close button */
.notification-panel .close {
    font-size: 1.2rem; /* Made close button slightly larger */
    line-height: 1;
    position: absolute; /* Position relative to alert */
    top: 5px;
    right: 8px;
}

.delete-notification-btn {
    position: absolute;
    bottom: 8px;
    right: 8px;
}

/* Scrollbar */
.notification-panel::-webkit-scrollbar { width: 6px; }
.notification-panel::-webkit-scrollbar-thumb { background-color: rgba(0,0,0,0.2); border-radius: 3px; }
.notification-panel::-webkit-scrollbar-track { background-color: transparent; }
</style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <?= $this->include('user/sidebar') ?>

    <!-- Main Content -->
    <div class="main-content container-fluid">
        <!-- Welcome Header -->
        <div class="mb-4">
            <h3 class="font-weight-bold">Welcome, <?= esc($user['username'] ?? 'User') ?>!</h3>
            <p class="text-muted mb-1">Main Folder: <strong><?= esc($user['main_folder'] ?? 'No folder assigned') ?></strong></p>
        </div>

        <!-- Cards Section -->
        <div class="cards-wrapper">
            <a href="<?= site_url('user/files') ?>" class="text-decoration-none text-dark">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <i class="fas fa-file-alt fa-2x text-success mb-2"></i>
                        <h5>Total Files</h5>
                        <h2><?= esc($totalFiles ?? 0) ?></h2>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Notification Panel -->
<div class="notification-panel">
    <?php if (!empty($notifications)): ?>
        <?php foreach ($notifications as $n): ?>
            <?php
                $alertClass = 'alert-info';
                if ($n['type'] === 'rejected') $alertClass = 'alert-danger';
                if ($n['type'] === 'accepted') $alertClass = 'alert-success';
            ?>
            <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert" data-notification-id="<?= $n['id'] ?>">
                <h5 class="alert-heading"><?= esc($n['title']) ?></h5>
                <p><?= esc($n['message']) ?></p>
                <?php if (!empty($n['reason'])): ?>
                    <p class="mb-0"><strong>Reason:</strong> <?= esc($n['reason']) ?></p>
                <?php endif; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger delete-notification-btn" title="Delete Notification"><i class="fa fa-trash"></i></button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const notificationPanel = document.querySelector('.notification-panel');

    // Function to delete a notification via API
    function deleteNotification(notificationId, alertElement) {
        if (notificationId) {
            fetch(`<?= base_url('notifications/delete/') ?>${notificationId}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.ok ? response.json() : Promise.reject('Network error'))
            .then(data => {
                console.log('Notification deleted:', data);
                // Remove the alert from the DOM
                if (alertElement) {
                    alertElement.remove();
                }
            })
            .catch(error => console.error('Error deleting notification:', error));
        }
    }

    // Listener for the custom delete button
    notificationPanel.addEventListener('click', function(event) {
        const deleteButton = event.target.closest('.delete-notification-btn');
        if (deleteButton) {
            const alertElement = deleteButton.closest('.alert');
            const notificationId = alertElement.dataset.notificationId;
            // Call delete directly and remove alert
            deleteNotification(notificationId, alertElement);
        }
    });

    // Optional: handle default 'x' close button to also delete notification
    notificationPanel.addEventListener('click', function(event) {
        const closeButton = event.target.closest('.close');
        if (closeButton) {
            const alertElement = closeButton.closest('.alert');
            const notificationId = alertElement.dataset.notificationId;
            deleteNotification(notificationId, alertElement);
        }
    });
});
</script>
</body>
</html>
