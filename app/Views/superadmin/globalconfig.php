<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        .content {
            margin-left: 220px;
            padding: 20px;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .config-table {
            max-width: 800px;
            margin: 0 auto 20px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .config-table th {
            background-color: #2C2C2C;
            color: #fff;
            text-transform: uppercase;
            font-size: 14px;
        }
        .config-table td {
            font-size: 14px;
            vertical-align: middle;
        }
        #alert-box {
            max-width: 800px;
            margin: 0 auto 20px auto;
        }
    </style>
</head>
<body>
    <?= $this->include('superadmin/sidebar') ?>

    <div class="content">

        <h2 class="page-header mb-4">
            <i class="fa-solid fa-sliders"></i> <?= esc($title) ?>
        </h2>

        <div id="alert-box"></div>

        <!-- ================= System Settings ================= -->
        <table class="table table-bordered align-middle config-table mb-4">
            <tr><th colspan="2">System Settings</th></tr>
            <tr>
                <th style="width:70%">Setting</th>
                <th style="width:30%">Status</th>
            </tr>
            <tbody>
                <?php foreach ($all_controls as $uc): ?>
                    <?php if ($uc['config_key'] === 'system'): ?>
                        <tr>
                            <td class="fw-medium"><?= ucwords(str_replace("_", " ", $uc['setting_key'])) ?></td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-center">
                                    <input class="form-check-input toggle-switch me-2" type="checkbox" 
                                           data-id="<?= $uc['id'] ?>" <?= $uc['setting_value'] == 1 ? 'checked' : '' ?>>
                                    <label class="form-check-label mb-0"><?= $uc['setting_value'] == 1 ? 'ON' : 'OFF' ?></label>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- ================= Admin Settings ================= -->
        <table class="table table-bordered align-middle config-table mb-4">
            <tr><th colspan="2">Admin Settings</th></tr>
            <tr>
                <th style="width:70%">Setting</th>
                <th style="width:30%">Status</th>
            </tr>
            <tbody>
                <?php foreach ($all_controls as $uc): ?>
                    <?php if ($uc['config_key'] === 'admin'): ?>
                        <tr>
                            <td class="fw-medium"><?= ucwords(str_replace("_", " ", $uc['setting_key'])) ?></td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-center">
                                    <input class="form-check-input toggle-switch me-2" type="checkbox" 
                                           data-id="<?= $uc['id'] ?>" <?= $uc['setting_value'] == 1 ? 'checked' : '' ?>>
                                    <label class="form-check-label mb-0"><?= $uc['setting_value'] == 1 ? 'ON' : 'OFF' ?></label>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- ================= User Settings ================= -->
        <table class="table table-bordered align-middle config-table mb-4">
            <tr><th colspan="2">User Settings</th></tr>
            <tr>
                <th style="width:70%">Setting</th>
                <th style="width:30%">Status</th>
            </tr>
            <tbody>
                <?php foreach ($all_controls as $uc): ?>
                    <?php if ($uc['config_key'] === 'user'): ?>
                        <tr>
                            <td class="fw-medium"><?= ucwords(str_replace("_", " ", $uc['setting_key'])) ?></td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-center">
                                    <input class="form-check-input toggle-switch me-2" type="checkbox" 
                                           data-id="<?= $uc['id'] ?>" <?= $uc['setting_value'] == 1 ? 'checked' : '' ?>>
                                    <label class="form-check-label mb-0"><?= $uc['setting_value'] == 1 ? 'ON' : 'OFF' ?></label>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>

    <!-- Bootstrap + jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
$(function() {
    // ===== Toggle switches =====
    $(document).on('change', '.toggle-switch, .toggle-file-type', function() {
        let id = $(this).data('id');
        let status = $(this).is(':checked') ? 1 : 0;
        let label = $(this).closest('.form-check').find('.form-check-label');

        $.post("<?= site_url('superadmin/globalconfig/toggle') ?>", 
               { id: id, status: status }, 
               function(response) {
            if (response.success) {
                label.text(status ? 'ON' : 'OFF');
                showAlert('success', 'Setting updated.');
            } else {
                showAlert('danger', response.message || 'Failed to update setting.');
            }
        }, 'json');
    });

    // ===== Alert helper =====
    function showAlert(type, message) {
        $('#alert-box').html(
            '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
            '<i class="fa-solid ' + (type === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation') + ' me-2"></i>' +
            message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
            '</div>'
        );
    }
});
</script>

</body>
</html>
