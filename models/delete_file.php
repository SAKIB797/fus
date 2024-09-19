<?php
// Include your database configuration
include '../db/config.php';

// Check if the userID is set in the session
if (!isset($_SESSION['userID'])) {
  echo json_encode(['error' => 'User not authenticated.']);
  exit;
}

if (isset($_POST['file_id'])) {
  $file_id = $_POST['file_id'];

  // Query to get the file path
  $stmt = $pdo->prepare("SELECT file_path FROM files WHERE id = :id");
  $stmt->execute(['id' => $file_id]);
  $file = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($file) {
    // Full path of the file to be deleted
    $file_path = "../uploads/user" . $_SESSION['userID'] . "/" . $file['file_path'];

    // Delete the file from the folder
    if (file_exists($file_path)) {
      unlink($file_path); // Remove file from directory
    }

    // Delete the record from the database
    $stmt = $pdo->prepare("DELETE FROM files WHERE id = :id");
    $stmt->execute(['id' => $file_id]);

    echo 'success'; // Return success response
  } else {
    echo 'error'; // Return error response if file is not found
  }
}
?>