<?php
// Include your database connection
require '../db/config.php'; // Adjust the path to your config file

// Check if the userID is set in the session
if (!isset($_SESSION['userID'])) {
  echo json_encode(['error' => 'User not authenticated.']);
  exit;
}

$userID = $_SESSION['userID'];

try {
  // Prepare and execute the query to fetch data from Indexes table
  $stmt = $pdo->prepare("SELECT * FROM Indexes WHERE user_id = :userID ORDER BY created_at desc");
  $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
  $stmt->execute();

  $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Return the data as JSON
  echo json_encode($indexes);
} catch (PDOException $e) {
  // Handle any errors
  echo json_encode(['error' => 'Error fetching indexes: ' . $e->getMessage()]);
}
