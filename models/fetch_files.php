<?php
// Include your database configuration
include '../db/config.php';

// Check if the userID is set in the session
if (!isset($_SESSION['userID'])) {
  echo json_encode(['error' => 'User not authenticated.']);
  exit;
}

$user_id = $_SESSION['userID']; // Assuming the user ID is passed in the request

if (isset($_POST['topic_id'])) {

  $topic_id = $_POST['topic_id'];

  // Query to fetch id, file_path, and file_type by topic_id
  $stmt = $pdo->prepare("SELECT id, file_path, file_type FROM files WHERE topic_id = :topic_id");
  $stmt->execute(['topic_id' => $topic_id]);
  $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Directory where files are stored (user-specific)
  $directory = "../uploads/user{$user_id}/";

  $fileData = [];

  // Loop through each file and calculate the file size
  foreach ($files as $file) {
    $file_path = $directory . $file['file_path'];

    // Check if the file exists before calculating the size
    if (file_exists($file_path)) {
      $file_size = filesize($file_path); // Get file size in bytes
      $file_size_in_mb = round($file_size / (1024 * 1024), 2); // Convert to MB

      // Append file data including id and size to the response
      $fileData[] = [
        'id' => $file['id'], // Include file id
        'file_name' => basename($file['file_path']),
        'file_type' => $file['file_type'],
        'size' => $file_size_in_mb // Size in MB
      ];
    } else {
      // Handle case where file does not exist
      $fileData[] = [
        'id' => $file['id'], // Include file id even if the file is missing
        'file_name' => basename($file['file_path']),
        'file_type' => $file['file_type'],
        'size' => 'File not found'
      ];
    }
  }

  // Return the files data as JSON
  echo json_encode($fileData);
}
?>