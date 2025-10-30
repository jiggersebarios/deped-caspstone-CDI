

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Optional icons -->
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
    margin: 0 ;
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

/* Alert box at top */
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

      <!-- admintable -->
    <table class="table table-bordered align-middle config-table">
        <tr>
            <th colspan="2">Admin Controls</th>
        </tr>

        <tr>
            <th style="width:70%">Setting</th>
            <th style="width:30%">Status</th>
        </tr>
    </thead>
        <tbody>
            <?php foreach ($configs as $config): ?>
                <tr>
                    <td class="fw-medium">
                        <?= ucwords(str_replace("_", " ", $config['setting_key'])) ?>
                    </td>
                    <td>
                        <div class="form-check form-switch d-flex align-items-center">
                            <input 
                                class="form-check-input toggle-switch me-2" 
                                type="checkbox" 
                                data-id="<?= $config['id'] ?>" 
                                <?= $config['setting_value'] == 1 ? 'checked' : '' ?>>
                            <label class="form-check-label mb-0">
                                <?= $config['setting_value'] == 1 ? 'ON' : 'OFF' ?>
                            </label>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<br>
  <!-- usertable -->
<!-- usertable -->
<table class="table table-bordered align-middle config-table">
    <tr>
        <th colspan="2">User Controls</th>
    </tr>
    <tr>
        <th style="width:70%">Setting</th>
        <th style="width:30%">Status</th>
    </tr>
    <tbody>
        <?php if (!empty($user_controls)): ?>
            <?php foreach ($user_controls as $uc): ?>
                <tr>
                    <td class="fw-medium">
                        <?= ucwords(str_replace("_", " ", $uc['setting_key'])) ?>
                    </td>
                    <td>
                        <div class="form-check form-switch d-flex align-items-center">
                            <input 
                                class="form-check-input toggle-switch me-2" 
                                type="checkbox" 
                                data-id="<?= $uc['id'] ?>" 
                                <?= $uc['setting_value'] == 1 ? 'checked' : '' ?>>
                            <label class="form-check-label mb-0">
                                <?= $uc['setting_value'] == 1 ? 'ON' : 'OFF' ?>
                            </label>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="2" class="text-center text-muted">No user settings available</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>




<!-- Bootstrap + jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(function(){
    $('.toggle-switch').on('change', function(){
        let id = $(this).data('id');
        let status = $(this).is(':checked') ? 1 : 0;
        let label = $(this).closest('.form-check').find('.form-check-label');

        $.post("<?= site_url('superadmin/globalconfig/toggle') ?>", {id: id, status: status}, function(response){
            if(response.success){
                label.text(status === 1 ? 'ON' : 'OFF');
                $('#alert-box').html(
                    '<div class="alert alert-success alert-dismissible fade show" role="alert">'
                    + '<i class="fa-solid fa-circle-check me-2"></i>' + response.message +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                );
            } else {
                $('#alert-box').html(
                    '<div class="alert alert-danger alert-dismissible fade show" role="alert">'
                    + '<i class="fa-solid fa-triangle-exclamation me-2"></i>' + response.message +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                );
            }
        }, 'json');
    });
});

</script>
</body>
</html>
