<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc($fileName) ?></title>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; margin: 20px; }
        #output { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 5px rgba(0,0,0,0.2); overflow:auto; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 5px; text-align: left; }
    </style>
</head>
<body>
    <h4 class="text-center mb-3"><?= esc($fileName) ?></h4>
    <div id="output">Loading Excel file...</div>

    <script>
        const fileUrl = "<?= esc($fileUrl) ?>";

        fetch(fileUrl)
            .then(response => response.arrayBuffer())
            .then(data => {
                const workbook = XLSX.read(data, { type: "array" });
                let html = "";
                workbook.SheetNames.forEach(name => {
                    const sheet = workbook.Sheets[name];
                    html += `<h5>${name}</h5>` + XLSX.utils.sheet_to_html(sheet);
                });
                document.getElementById("output").innerHTML = html;
            })
            .catch(err => {
                document.getElementById("output").innerHTML = "Failed to load Excel file.";
                console.error(err);
            });
    </script>
</body>
</html>
