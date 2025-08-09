<?php
session_start();
require('config.php');

if (!isset($_SESSION['matric_number'])) {
    die("Access denied. Please log in.");
}

$matric_number = $_SESSION['matric_number'];

// Fetch favorite materials
$stmt = $conn->prepare("SELECT * FROM favorites WHERE matric_number = :matric_number");
$stmt->bindParam(':matric_number', $matric_number);
$stmt->execute();
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Favorites</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <script>
        function removeFavorite(filePath) {
            fetch('toggle_favorite.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'file_path=' + encodeURIComponent(filePath)
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            });
        }
    </script>
</head>
<body>

<div class="container">
    <h2>My Favorite Materials</h2>
    <?php if ($favorites): ?>
        <table class="materials">
            <tr>
                <th>Subject</th>
                <th>File</th>
                <th>Note</th>
                <th>Faculty</th>
                <th>Upload Date</th>
                <th>Remove</th>
            </tr>
            <?php foreach ($favorites as $favorite): ?>
                <tr>
                    <td><?php echo htmlspecialchars($favorite['subject']); ?></td>
                    <td>
                        <a href="<?php echo htmlspecialchars($favorite['file_path']); ?>" target="_blank" download>Download</a> |
                        <a href="view_file.php?file=<?php echo urlencode($favorite['file_path']); ?>" target="_blank">View</a>
                    </td>
                    <td><?php echo htmlspecialchars($favorite['note']); ?></td>
                    <td><?php echo htmlspecialchars($favorite['faculty_id']); ?></td>
                    <td><?php echo htmlspecialchars($favorite['upload_date']); ?></td>
                    <td>
                        <button onclick="removeFavorite('<?php echo urlencode($favorite['file_path']); ?>')">❌ Remove</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>You have no favorite materials.</p>
    <?php endif; ?>
</div>

</body>
</html>
