<?php
include 'partials/header.php';
include 'partials/navigation.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];

    $allowedMimeTypes = ['video/mp4', 'video/ogg', 'video/webm', 'video/quicktime', 'video/x-msvideo', 'video/x-matroska'];
    $allowedExtensions = ['mp4', 'ogg', 'webm', 'mov', 'avi', 'mkv'];

    if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['video']['tmp_name'];
        $fileName = basename($_FILES['video']['name']);
        $fileSize = $_FILES['video']['size'];
        $fileType = mime_content_type($fileTmpPath);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($fileType, $allowedMimeTypes) && in_array($fileExt, $allowedExtensions)) {
            $targetDir = 'uploads/';
            $targetFile = $targetDir . time() . "_" . $fileName;

            move_uploaded_file($fileTmpPath, $targetFile);

            $stmt = $conn->prepare("INSERT INTO orders (user_id, title, video_path) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user_id, $title, $targetFile);
            $stmt->execute();

            $message = "<p class='success-message'>✅ Užsakymas pateiktas!</p>";
        } else {
            $message = "<p class='error-message'>❌ Nepalaikomas failo tipas. Įkelkite tik video failus.</p>";
        }
    } else {
        $message = "<p class='error-message'>❌ Klaida įkeliant video failą.</p>";
    }

}
?>

<body class="upload">
<div class="form-container">
    <form method="post" enctype="multipart/form-data">
        <h2>Užsakymo pateikimo forma</h2>
        <?php if ($message) echo $message; ?>
        <label for="title">Pavadinimas:</label>
        <input type="text" placeholder="Įveskite pavadinimą" name="title" id="title" required>

        <label for="video">Video failas:</label>
        <input type="file" name="video" id="video" accept="video/*" required>

        <input type="submit" value="Pateikti">
    </form>
</div>
</body>

<?php include 'partials/footer.php'; ?>
