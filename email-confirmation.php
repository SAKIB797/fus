<?php

require 'db/config.php';
require 'smtp/config.php';

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && $action == 'send_cm' && isset($_GET['email'])) {

  $email = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);

  // Fetch the user's name
  $nameStmt = $pdo->prepare("SELECT name FROM users WHERE email = ?");
  $nameStmt->execute([$email]);
  $user = $nameStmt->fetch(PDO::FETCH_ASSOC);

  if (!$user) {
    echo "User not found!";
    exit;
  }

  // Delete any existing tokens for the email
  $deleteStmt = $pdo->prepare("DELETE FROM email_verification WHERE email = ?");
  $deleteStmt->execute([$email]);

  // Generate a new token
  $token = bin2hex(random_bytes(16));

  // Calculate expiration time (current time + 10 minutes)
  $expires = time() + (10 * 60);

  // Save the new token, email, and expiration time to the email_verification table
  $insertStmt = $pdo->prepare("INSERT INTO email_verification (email, token, expires) VALUES (?, ?, ?)");
  if ($insertStmt->execute([$email, $token, $expires])) {
    // Send email using existing mail setup
    try {
      $mail->setFrom('no-reply@fus.com', 'Email Confirmation - FUS');
      $mail->addAddress($email);

      // Email content with the beautiful template
      $mail->isHTML(true);
      $mail->Subject = 'Confirm your email';
      $mail->Body = "
        <html>
        <body style='font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;'>
          <div style='max-width: 600px; margin: auto; background-color: #ffffff; padding: 20px; border-radius: 5px;'>
            <h2 style='color: #333;'>Confirm your email for FUS</h2>
            <p>Dear {$user['name']},</p>
            <p>Please click the button below to confirm your email address:</p>
            <a href='http://fus/email-confirmation.php?action=check-token&token={$token}&email=" . urlencode($email) . "' 
              style='display: inline-block; background-color: #3498db; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 4px;'>Confirm Email</a>
            <p>If you didn't request this, please ignore this email.</p>

            <p style='font-size: 0.9em;'>If you're having trouble clicking the button, copy and paste the following link into your browser:</p>
            <p style='font-size: 0.9em;'>http://localhost/email-confirmation.php?action=check-token&token={$token}&email=" . urlencode($email) . "</p>

            <hr style='border-top: 1px solid #eee; margin-top: 30px;' />
            <p style='text-align: center; color: #999;'>This email was sent by FUS | 
            <a href='http://fus' style='color: #3498db;'>Visit Website</a></p>
          </div>
        </body>
        </html>";

      $mail->send();
    } catch (Exception $e) {
      echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
  }
}


if ($_SERVER['REQUEST_METHOD'] == 'GET' && $action == 'check-token' && isset($_GET['email']) && isset($_GET['token'])) {
  $email = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);
  $token = $_GET['token'];

  // Check the token and expiration
  $sql = "SELECT expires FROM email_verification WHERE email = :email AND token = :token";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':email', $email, PDO::PARAM_STR);
  $stmt->bindParam(':token', $token, PDO::PARAM_STR);
  $stmt->execute();
  $verification = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($verification) {
    $validity = $verification['expires'] ?? null;

    if ($validity && time() < $validity) {
      $updateStmt = $pdo->prepare("UPDATE users SET verified = 'yes' WHERE email = :email");
      $updateStmt->bindParam(':email', $email, PDO::PARAM_STR);
      if ($updateStmt->execute()) {
        $isValid = true;
      } else {
        echo 'Verification failed!';
      }
    } else {
      $isValid = false;
    }
  } else {
    echo 'Invalid verification token or email.';
  }
}
?>


<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Confirmation | FUS</title>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <!-- DaisyUI -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- custom stylesheet -->
    <link rel="stylesheet" href="css/style.css">
  </head>

  <body class="bg-gray-100">
    <main class="flex items-center justify-center min-h-screen">
      <div class="card w-96 bg-white shadow-lg text-white">
        <div class="card-body text-center">
          <?php if ($_SERVER['REQUEST_METHOD'] == 'GET' && $action == 'send_cm'): ?>
            <div class="alert alert-success text-white">
              <i class="fas fa-check-circle"></i> A verification email has been sent.
            </div>
            <a href="/" class="btn btn-primary mt-4">Back to Home</a>
          <?php endif; ?>

          <?php if ($_SERVER['REQUEST_METHOD'] == 'GET' && $action == 'check-token'): ?>
            <?php if ($isValid): ?>
              <div class="alert alert-success text-white">
                <i class="fas fa-check-circle"></i> Your email has been successfully verified!
              </div>
              <a href="registration.php" class="btn btn-primary mt-4">Login</a>
            <?php else: ?>
              <div class="alert alert-error text-white">
                <i class="fas fa-exclamation-circle"></i> Verification link has expired.
              </div>
              <a href="email-confirmation.php?email=<?php echo $email ?>&action=send_cm"
                class="btn btn-secondary mt-4">Resend verification email</a>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </main>
  </body>

</html>