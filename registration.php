<?php
require 'db/config.php';
if (isset($_SESSION['userID'])) {
  header("Location: index.php");
}
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $action = $_POST['action'];
}

// Registration logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'registration') {
  // Sanitize user input
  $name = htmlspecialchars($_POST['name']);
  $email = htmlspecialchars($_POST['email']);
  $username = htmlspecialchars($_POST['username']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $dept = $_POST['dept'];
  $sid = htmlspecialchars($_POST['sid']);
  $session = htmlspecialchars($_POST['session']);

  // Check if email or username already exists
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? OR username = ?");
  $stmt->execute([$email, $username]);
  $exists = $stmt->fetchColumn();

  if ($exists) {
    $_SESSION['reg-error'] = "Email or Username already exists.";
  } else {
    // Insert user into the database
    $stmt = $pdo->prepare("INSERT INTO users (name, email, username, password, dept, sid, session) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $username, $password, $dept, $sid, $session])) {
      // Get the user ID of the newly inserted user
      $userID = $pdo->lastInsertId();

      // Create a user-specific folder in the 'uploads' directory
      $userFolder = 'uploads/user' . $userID;
      $profileImgFolder = $userFolder . '/profile_img';

      // Check if the directory doesn't exist, then create it
      if (!file_exists($userFolder)) {
        mkdir($userFolder, 0777, true);
      }

      // Create a subfolder 'profile_img' inside the user's folder
      if (!file_exists($profileImgFolder)) {
        mkdir($profileImgFolder, 0777, true);
      }

      // Handle profile image upload
      if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] == 0) {
        $fileTmpPath = $_FILES['profile_img']['tmp_name'];
        $fileName = $_FILES['profile_img']['name'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = 'profile_' . $userID . '.' . $fileExtension;

        $uploadPath = $profileImgFolder . '/' . $newFileName;
        move_uploaded_file($fileTmpPath, $uploadPath);

        // Update user record with profile image
        $stmt = $pdo->prepare("UPDATE users SET img = ? WHERE id = ?");
        $stmt->execute([$newFileName, $userID]);
      }

      // Redirect to email confirmation page
      header("Location: email-confirmation.php?email=$email&action=send_cm");
      exit();
    } else {
      echo "Error registering user.";
    }
  }
}

// Login logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'login') {
  $loginIdentifier = $_POST['login_identifier']; // Can be email or username
  $password = $_POST['password'];

  // Check if login identifier is an email or username
  if (filter_var($loginIdentifier, FILTER_VALIDATE_EMAIL)) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
  } else {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
  }

  $stmt->execute([$loginIdentifier]);
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user['password'])) {
    // Check if user has verified their email
    if ($user['verified'] == 'no') {
      $_SESSION['login-error'] = "You haven't verified your email.<br><a class='text-blue-500' href='email-confirmation.php?email=" . urlencode($user['email']) . "&action=send_cm'>Resend verification email</a>";
    } else {
      $_SESSION['userID'] = $user['id'];
      header("Location: index.php");
    }
  } else {
    $_SESSION['login-error'] = "Invalid login credentials.";
  }
}

?>

<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Registration | FUS</title>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- custom stylesheet -->
    <link rel="stylesheet" href="css/style.css">
    <!-- alpine js  -->
    <script src="//unpkg.com/alpinejs"></script>
  </head>

  <body class="min-h-screen flex flex-col">
    <?php include 'components/header.php' ?>
    <main class="flex-grow">
      <section class=" mx-auto py-8 lg:py-10">

        <div class="flex justify-center">
          <div role="tablist" class="tabs tabs-boxed">
            <label for="upload-tab" class="tab tab-lifted text-primary tab-active">
              Login
            </label>

            <label for="library-tab" class="tab tab-lifted text-primary">
              Registration
            </label>
          </div>
        </div>

        <input type="radio" id="upload-tab" name="my_tabs" class="hidden peer/upload-tab" checked />
        <div role="tabpanel" class="tab-content mx-8 bg-base-100 rounded-box   peer-checked/upload-tab:block">
          <!--login form here -->
          <form action="" method="POST"
            class="w-full max-w-md mx-auto p-8 space-y-6 border mb-10 border-base-300  bg-white shadow-lg rounded-lg">
            <input type="hidden" name="action" value="login">
            <h2 class="text-2xl font-bold text-center">Login</h2>
            <?php if (isset($_SESSION['login-error'])): ?>
              <p class="my-2 text-center text-red-400">
                <?php
                echo $_SESSION['login-error'];
                unset($_SESSION['login-error'])
                  ?>
              </p>
            <?php endif; ?>

            <div class="form-control">
              <label for="login_identifier" class="label">
                <span class="label-text">
                  <i class="fas fa-user mr-2"></i> Username or Email
                </span>
              </label>
              <input type="text" id="login_identifier" name="login_identifier" class="input input-bordered w-full"
                placeholder="Enter your username or email" required>
            </div>

            <!-- Password -->
            <div class="form-control">
              <label for="password" class="label">
                <span class="label-text">
                  <i class="fas fa-lock mr-2"></i> Password
                </span>
              </label>
              <div class="relative">
                <input type="password" id="login-password" name="password"
                  class="input input-bordered password-input w-full" placeholder="Enter your password" minlength="8"
                  required>
                <div class="absolute right-3 top-0 bottom-0 flex items-center">
                  <i class="fas fa-eye cursor-pointer toggle-password" id="toggleLoginPassword"></i>
                </div>
              </div>
            </div>


            <div class="flex justify-between">
              <button type="submit" class="btn btn-primary w-full">Login</button>
            </div>

            <div>
              <p class="text-center text-sm ">
                <a href="reset-password.php" class="text-sm text-blue-500 hover:underline mt-2">
                  <i class="fas fa-unlock-alt"></i> Forgot Password?
                </a>
              </p>
            </div>
          </form>

        </div>

        <!-- Tab content for Library -->
        <input type="radio" id="library-tab" name="my_tabs" class="hidden peer/library-tab" />
        <div role="tabpanel" class="tab-content mx-8 bg-base-100 rounded-box hidden peer-checked/library-tab:block">
          <!-- registration form here -->
          <form action="" method="POST" enctype="multipart/form-data"
            class="w-full max-w-3xl mx-auto p-8 space-y-6 bg-white shadow-lg rounded-lg border  border-base-300 mb-10">
            <input type="hidden" name="action" value="registration">
            <h2 class="text-2xl font-bold text-center mb-6">Create an Account</h2>
            <?php if (isset($_SESSION['reg-error'])): ?>
              <p class="my-2 text-center text-red-400">
                <?php
                echo $_SESSION['reg-error'];
                unset($_SESSION['reg-error'])
                  ?>
              </p>
            <?php endif; ?>

            <!-- Two-column grid for large screens, one column for small screens -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <!-- Name -->
              <div class="form-control">
                <label for="name" class="label">
                  <span class="label-text">
                    <i class="fas fa-user mr-2"></i> Full Name
                  </span>
                </label>
                <input type="text" id="name" name="name" class="input input-bordered w-full"
                  placeholder="Enter your full name" required>
              </div>

              <!-- Username -->
              <div class="form-control">
                <label for="username" class="label">
                  <span class="label-text">
                    <i class="fas fa-user-tag mr-2"></i> Username
                  </span>
                </label>
                <input type="text" id="username" name="username" class="input input-bordered w-full"
                  placeholder="Choose a username" required>
              </div>

              <!-- Email -->
              <div class="form-control">
                <label for="email" class="label">
                  <span class="label-text">
                    <i class="fas fa-envelope mr-2"></i> Email
                  </span>
                </label>
                <input type="email" id="email" name="email" class="input input-bordered w-full"
                  placeholder="Enter your email" required>
              </div>

              <!-- Department -->
              <div class="form-control">
                <label for="dept" class="label">
                  <span class="label-text">
                    <i class="fas fa-building mr-2"></i> Department
                  </span>
                </label>
                <select id="dept" name="dept" class="select select-bordered w-full">
                  <option value="CSE">CSE</option>
                  <option value="ICT">ICT</option>
                  <option value="DBA">DBA</option>
                </select>
              </div>

              <!-- Session -->
              <div class="form-control">
                <label for="session" class="label">
                  <span class="label-text">
                    <i class="fas fa-calendar-alt mr-2"></i> Session
                  </span>
                </label>
                <input type="text" id="session" name="session" class="input input-bordered w-full"
                  placeholder="Enter your session" required>
              </div>

              <!-- Student ID -->
              <div class="form-control">
                <label for="sid" class="label">
                  <span class="label-text">
                    <i class="fas fa-id-card-alt mr-2"></i> Student ID
                  </span>
                </label>
                <input type="text" id="sid" name="sid" class="input input-bordered w-full"
                  placeholder="Enter your student ID" required>
              </div>
            </div>

            <!-- Full-width Profile Image Upload -->
            <div class="form-control w-full">
              <label for="profile_img" class="label">
                <span class="label-text">
                  <i class="fas fa-image mr-2"></i> Profile Image
                </span>
              </label>
              <input type="file" id="profile_img" name="profile_img" class="file-input file-input-bordered w-full"
                accept="image/*">
            </div>

            <!-- Two-column grid for Password and Confirm Password -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <!-- Password -->
              <div class="form-control">
                <label for="password" class="label">
                  <span class="label-text">
                    <i class="fas fa-lock mr-2"></i> Password
                  </span>
                </label>
                <div class="relative">
                  <input type="password" id="password" name="password"
                    class="input input-bordered password-input w-full" placeholder="Enter your password" minlength="8"
                    required>
                  <div class="absolute right-3 top-0 bottom-0 flex items-center">
                    <i class="fas fa-eye cursor-pointer toggle-password" id="togglePassword"></i>
                  </div>
                </div>
              </div>

              <!-- Confirm Password -->
              <div class="form-control">
                <label for="confirm_password" class="label">
                  <span class="label-text">
                    <i class="fas fa-lock mr-2"></i> Confirm Password
                  </span>
                </label>
                <div class="relative">
                  <input type="password" id="confirm_password" name="confirm_password"
                    class="input input-bordered password-input w-full" placeholder="Confirm your password" minlength="8"
                    required>
                  <div class="absolute right-3 top-0 bottom-0 flex items-center">
                    <i class="fas fa-eye cursor-pointer toggle-password" id="toggleConfirmPassword"></i>
                  </div>
                </div>
              </div>

            </div>

            <button type="submit" class="btn btn-primary w-full">
              <i class="fas fa-user-plus mr-2"></i> Register
            </button>

            <p class="text-center text-sm mt-6">
              Already have an account?
              <a href="#" class="text-blue-500 hover:underline">Login</a>
            </p>
          </form>


        </div>
      </section>
    </main>
    <?php include 'components/footer.php' ?>
    <script>
      $('.tabs .tab').on('click', function () {
        $('.tabs .tab').removeClass("tab-active");
        $(this).addClass("tab-active");
      })
    </script>
    <script>
      $(document).ready(function () {
        // Toggle password visibility for registration
        $('#togglePassword').on('click', function () {
          const passwordField = $('#password');
          const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
          passwordField.attr('type', type);
          $(this).toggleClass('fa-eye fa-eye-slash');
        });

        $('#toggleConfirmPassword').on('click', function () {
          const confirmPasswordField = $('#confirm_password');
          const type = confirmPasswordField.attr('type') === 'password' ? 'text' : 'password';
          confirmPasswordField.attr('type', type);
          $(this).toggleClass('fa-eye fa-eye-slash');
        });

        // Toggle password visibility for login
        $('#toggleLoginPassword').on('click', function () {
          const loginPasswordField = $('#login-password');
          const type = loginPasswordField.attr('type') === 'password' ? 'text' : 'password';
          loginPasswordField.attr('type', type);
          $(this).toggleClass('fa-eye fa-eye-slash');
        });

        // Validate password and confirm password match for registration
        $('form').on('submit', function (event) {
          const password = $('#password').val();
          const confirmPassword = $('#confirm_password').val();
          if (password !== confirmPassword) {
            alert('Passwords do not match.');
            event.preventDefault(); // Prevent form submission
          }
        });
      });
    </script>


  </body>

</html>