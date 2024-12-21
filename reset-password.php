<?php
require 'db/config.php';  // Database connection
require 'smtp/config.php';  // SMTP email config

$toastMessage = '';
$toastType = '';

if (!isset($_GET['token'])) {
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'send-reset-link' && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Check if email exists in users table
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
      // Generate token
      $token = bin2hex(random_bytes(16));

      // Calculate expiration time (current time + 10 minutes)
      $expires = time() + (10 * 60);

      // Insert token, email, and expiration time into password_reset table
      $stmt = $pdo->prepare('INSERT INTO password_reset (email, token, expires) VALUES (:email, :token, :expires)');
      $stmt->execute(['email' => $email, 'token' => $token, 'expires' => $expires]);

      // Send reset email
      $reset_link = "http://localhost/fus/reset-password.php?token=$token";
      $subject = "Reset your password";
      $message = "
        <html>
        <body style='font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;'>
            <div style='max-width: 600px; margin: auto; background-color: #ffffff; padding: 20px; border-radius: 5px;'>
                <h2 style='color: #333;'>Reset your password for FUS (File Upload System)</h2>
                <p>Dear {$user['name']},</p>
                <p>You requested a password reset for your account on <strong>FUS</strong>. Please click the button below to reset your password:</p>
                <a href='$reset_link' style='display: inline-block; background-color: #3498db; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 4px;'>Reset Password</a>
                <p>If you didn't request this, please ignore this email. Your password will remain unchanged.</p>

                <p style='font-size: 0.9em;'>If you are having trouble clicking the button, copy and paste the following link into your browser:</p>
                <p style='font-size: 0.9em;'>$reset_link</p>

                <hr style='border-top: 1px solid #eee; margin-top: 30px;' />
                <p style='text-align: center; color: #999;'>This email was sent by FUS (File Upload System) | <a href='http://localhost/fus' style='color: #3498db;'>Visit Website</a></p>
                <p style='text-align: center; color: #999;'>Contact us: <a style='color: #3498db;' href='mailto:ihsakib@outlook.com'>ihsakib@outlook.com</a></p>
            </div>
        </body>
        </html>";

      $headers = "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      $headers .= "From: FUS <no-reply@fus.com>" . "\r\n";

      // Use the pre-configured SMTP variable $mail
      $mail->setFrom('no-reply@fus.com', 'Password Reset - FUS');
      $mail->addAddress($email);
      $mail->Subject = $subject;
      $mail->Body = $message;
      $mail->isHTML(true);

      if ($mail->send()) {
        $toastMessage = "Reset link has been sent to $email";
        $toastType = 'success';
      } else {
        $toastMessage = "Failed to send reset link. Try again later.";
        $toastType = 'error';
      }
    } else {
      $toastMessage = "No user found with that email address.";
      $toastType = 'error';
    }
  }
}


// Handle password reset if token is present in the URL
if (isset($_GET['token'])) {
  $token = $_GET['token'];

  // Check if token exists and is not expired
  $stmt = $pdo->prepare('SELECT * FROM password_reset WHERE token = :token');
  $stmt->execute(['token' => $token]);
  $reset = $stmt->fetch();

  if ($reset && time() < $reset['expires']) {
    // Token is valid, show password reset form
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_password'])) {
      $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

      // Update password in users table
      $stmt = $pdo->prepare('UPDATE users SET password = :password WHERE email = :email');
      $stmt->execute(['password' => $new_password, 'email' => $reset['email']]);

      // Delete the token after password reset
      $stmt = $pdo->prepare('DELETE FROM password_reset WHERE token = :token');
      $stmt->execute(['token' => $token]);

      $toastMessage = "Password has been reset successfully!";
      $toastType = 'success';
    }
  } else {
    // Token is expired or invalid
    $toastMessage = "Invalid or expired token. Please request a new password reset link.";
    $toastType = 'error';
  }
}
?>

<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | FUS</title>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <!-- Tailwind CSS & DaisyUI -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="css/style.css">
  </head>

  <body class="min-h-screen px-6 bg-gray-100 flex items-center justify-center">
    <div class="bg-white p-8  rounded shadow-md w-full max-w-md">
      <h2 class="text-2xl font-bold text-center mb-4">Reset Your Password</h2>
      <?php if (!isset($_GET['token'])): ?>
        <!-- Email Form -->
        <form id="emailForm" action="" method="POST" <?php echo isset($_GET['token']) ? 'style="display: none;"' : ''; ?>>
          <input type="hidden" name="action" value="send-reset-link">
          <div class="form-control">
            <label class="label">
              <span class="label-text">Enter Your Email</span>
            </label>
            <input type="email" name="email" class="input input-bordered" required />
          </div>
          <div class="form-control mt-4">
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-paper-plane"></i> Send Reset Link
            </button>
          </div>
          <!-- Back to Login Link -->
          <div class="text-sm mt-4 text-center ">
            <a href="registration" class="text-blue-500 hover:underline">
              <i class="fa fa-arrow-left"></i> Back to Login
            </a>
          </div>
        </form>
      <?php endif; ?>

      <!-- Reset Password Form -->
      <?php if (isset($_GET['token'])): ?>
        <form id="resetForm" action="" method="POST">
          <div class="form-control">
            <label class="label">
              <span class="label-text">New Password</span>
            </label>
            <div class="relative">
              <input type="password" id="password" name="new_password" class="input input-bordered password-input w-full"
                placeholder="Enter new password" minlength="8" required>
              <div class="absolute right-3 top-0 bottom-0 flex items-center">
                <i class="fas fa-eye cursor-pointer toggle-password" id="togglePassword"></i>
              </div>
            </div>
          </div>
          <div class="form-control mt-4">
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-lock"></i> Reset Password
            </button>
          </div>
          <!-- Back to Login Link -->
          <div class="flex gap-4 justify-center">
            <div class="text-sm mt-4 text-center">
              <a href="registration.php" class="text-blue-500 hover:underline">
                <i class="fa fa-arrow-left"></i> Back to Login
              </a>
            </div>
            <div class="text-sm mt-4 text-center">
              <a href="reset-password" class="text-blue-500 hover:underline">
                <i class="fa fa-arrow-left"></i> New reset request
              </a>
            </div>
          </div>
        </form>
      <?php endif; ?>
    </div>



    <!-- Toast Notification -->
    <?php if ($toastMessage): ?>
      <div class="toast toast-<?php echo $toastType; ?> fixed bottom-4 right-4">
        <div class="alert alert-<?php echo $toastType; ?>">
          <span class="text-white"><?php echo $toastMessage; ?></span>
        </div>
      </div>
    <?php endif; ?>

    <script>
      $(document).ready(function () {
        // Auto-hide toast after 5 seconds
        setTimeout(function () {
          $('.toast').fadeOut();
        }, 5000);

        $('#togglePassword').on('click', function () {
          const passwordField = $('#password');
          const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
          passwordField.attr('type', type);
          $(this).toggleClass('fa-eye fa-eye-slash');
        });
      });
    </script>
  </body>

</html>