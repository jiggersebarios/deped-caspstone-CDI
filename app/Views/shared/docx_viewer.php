<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc($fileName) ?></title>
    <script src="https://unpkg.com/mammoth@1.5.1/mammoth.browser.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; margin: 20px; }
        #output { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 5px rgba(0,0,0,0.2); }
    </style>
</head>
<body>
    <h5 class="mb-3 text-center"><?= esc($fileName) ?></h5>
    <div id="output" class="container">Loading document...</div>

    <script>
        const fileUrl = "<?= esc($fileUrl) ?>";

        fetch(fileUrl)
            .then(response => response.arrayBuffer())
            .then(arrayBuffer => mammoth.convertToHtml({ arrayBuffer }))
            .then(result => {
                document.getElementById("output").innerHTML = result.value;
            })
            .catch(err => {
                document.getElementById("output").innerHTML = "⚠️ Unable to display document.";
                console.error("DOCX preview error:", err);
            });
    </script>
</body>
</html>
