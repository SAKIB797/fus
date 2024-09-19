<?php
// Include database config
require '../db/config.php';

// Check if the userID is set in the session
if (!isset($_SESSION['userID'])) {
  echo json_encode(['error' => 'User not authenticated.']);
  exit;
}

// Get user ID from session
$userID = $_SESSION['userID'];

// Check if form is submitted and files are uploaded
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['files'])) {

  // Get the form data
  $topic = $_POST['topic'];
  $semester = $_POST['semester'];

  // Create user directory if not exists
  $uploadDir = '../uploads/user' . $userID . '/';
  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
  }

  try {
    // Insert into indexes table
    $stmt = $pdo->prepare("INSERT INTO indexes (user_id, topics, semester) VALUES (:user_id, :topics, :semester)");
    $stmt->bindParam(':user_id', $userID);
    $stmt->bindParam(':topics', $topic);
    $stmt->bindParam(':semester', $semester);
    $stmt->execute();

    // Get the topic_id from the last inserted topic
    $topicID = $pdo->lastInsertId();

    // Loop through each file in the files array
    foreach ($_FILES['files']['name'] as $key => $fileName) {
      $fileTmpPath = $_FILES['files']['tmp_name'][$key];
      $fileType = $_FILES['files']['type'][$key];
      $fileSize = $_FILES['files']['size'][$key];

      // Define file path
      $filePath = $uploadDir . basename($fileName);

      // Check if the file already exists and delete it
      if (file_exists($filePath)) {
        unlink($filePath);
      }

      // Move the uploaded file to the user directory
      if (move_uploaded_file($fileTmpPath, $filePath)) {
        // Insert into files table
        $stmt = $pdo->prepare("INSERT INTO files (topic_id, file_path, file_type) VALUES (:topic_id, :file_path, :file_type)");
        $stmt->bindParam(':topic_id', $topicID);
        $stmt->bindParam(':file_path', $fileName);
        $stmt->bindParam(':file_type', $fileType);
        $stmt->execute();
      } else {
        echo "Failed to upload file: $fileName.<br>";
      }
    }

    echo "Files uploaded successfully!";

  } catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
  }

} else {
  echo "No files uploaded.";
}
?>