<?php
require 'db/config.php';

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
  echo 'No user logged in.';
  exit();
}

$userID = $_SESSION['userID'];

// Fetch user details from the database
$stmt = $pdo->prepare("SELECT name, username, email, dept, session, sid, created_at, img FROM users WHERE id = :userID");
$stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission to update the profile
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
  $name = $_POST['name'];
  $username = $_POST['username'];
  $dept = $_POST['dept'];
  $session = $_POST['session'];
  $sid = $_POST['sid'];

  // Path for image upload
  $uploadDir = 'uploads/user' . $userID . '/profile_img/';
  $newImageName = '';

  // Create the upload directory if it does not exist
  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
  }

  // Handle image upload
  if (!empty($_FILES['profile_image']['name'])) {
    // Delete old image if exists
    $oldImagePath = $uploadDir . $user['img'];
    if (!empty($user['img']) && file_exists($oldImagePath)) {
      unlink($oldImagePath);
    }

    // New image details
    $newImageName = basename($_FILES['profile_image']['name']);
    $newImagePath = $uploadDir . $newImageName;

    // Move the uploaded file
    move_uploaded_file($_FILES['profile_image']['tmp_name'], $newImagePath);
  } else {
    // Keep the existing image if no new image is uploaded
    $newImageName = $user['img'];
  }

  // Update the user details in the database
  $updateStmt = $pdo->prepare("UPDATE users SET name = :name, username = :username, dept = :dept, session = :session, sid = :sid, img = :profile_image WHERE id = :userID");
  $updateStmt->bindParam(':name', $name, PDO::PARAM_STR);
  $updateStmt->bindParam(':username', $username, PDO::PARAM_STR);
  $updateStmt->bindParam(':dept', $dept, PDO::PARAM_STR);
  $updateStmt->bindParam(':session', $session, PDO::PARAM_STR);
  $updateStmt->bindParam(':sid', $sid, PDO::PARAM_STR);
  $updateStmt->bindParam(':profile_image', $newImageName, PDO::PARAM_STR);
  $updateStmt->bindParam(':userID', $userID, PDO::PARAM_INT);

  if ($updateStmt->execute()) {
    // Reload the page to show the updated data
    header('Location: profile.php');
    exit();
  } else {
    echo 'Error updating profile.';
  }
}
?>

<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
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
    <script>
      $(document).ready(function () {

        $('#editProfile').on('click', function (e) {
          e.preventDefault();
          $('input').prop('readonly', false).addClass("bg-green-50");
          $('#dept').prop('disabled', false).addClass("bg-green-50");
          $('#username').prop('readonly', true).removeClass("bg-green-50");
          $('#email').prop('readonly', true).removeClass("bg-green-50");
          $(this).hide();
          $("#cancel-btn").removeClass("hidden");
          $("#update-btn").removeClass("hidden");
        });

        $('#cancel-btn').on("click", function (e) {
          e.preventDefault();
          $('input').prop('readonly', true).removeClass("bg-green-50");
          $('#editProfile').show();
          $(this).addClass("hidden");
          $('#dept').prop('disabled', true).removeClass("bg-green-50");
          $("#update-btn").addClass("hidden");
        })
      });
    </script>
  </head>

  <body class="min-h-screen flex flex-col">
    <?php include 'components/header.php' ?>
    <main class="flex-grow">
      <div class="px-4 md:px-8">
        <div class="container mx-auto my-5 p-5">
          <div class="md:flex no-wrap gap-4 md:-mx-2">
            <!-- Left Side -->
            <div class="w-full md:w-3/12 md:mx-2 mb-5 md:mb-0">
              <!-- Profile Card -->
              <div class="bg-white p-3 border-t-4 border-t-green-400 border">
                <div class="image overflow-hidden">
                  <img id="profileImage" class="h-auto w-full mx-auto rounded-md"
                    src="uploads/user<?php echo htmlspecialchars($userID); ?>/profile_img/<?php echo htmlspecialchars($user['img']); ?>"
                    alt="">
                </div>
                <h1 id="profileName" class="text-gray-900 font-bold text-xl leading-8 my-1 text-center md:text-left">
                  <?php echo htmlspecialchars($user['name']); ?>
                </h1>
                <ul
                  class="bg-gray-100 text-gray-600 hover:text-gray-700 hover:shadow py-2 px-3 mt-3 divide-y rounded shadow-sm">
                  <li class="flex items-center justify-between py-3">
                    <span>Status</span>
                    <span class="ml-auto"><span
                        class="bg-green-500 py-1 px-2 rounded text-white text-sm">Active</span></span>
                  </li>
                  <li class="flex items-center justify-between py-3">
                    <span>Member since</span>
                    <span><?php echo htmlspecialchars(date('M d, Y', strtotime($user['created_at']))); ?></span>
                  </li>
                </ul>
              </div>
              <!-- End of profile card -->
            </div>
            <!-- Right Side -->
            <div class="w-full md:w-9/12">
              <!-- Profile tab -->
              <!-- About Section -->
              <div id="aboutSection" class="bg-white p-3 border rounded-sm shadow-sm">
                <div class="flex items-center space-x-2 font-semibold text-gray-900 leading-8 mb-4">
                  <span class="text-green-500">
                    <svg class="h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                  </span>
                  <span class="tracking-wide">About</span>
                </div>
                <form id="profileForm" method="POST" enctype="multipart/form-data">
                  <div class="grid gap-4 md:grid-cols-2 text-sm">
                    <div class="grid grid-cols-3 gap-2">
                      <div class="px-2 py-2 font-semibold">Name</div>
                      <div class="px-2 py-2 col-span-2"><input type="text" name="name"
                          value="<?php echo htmlspecialchars($user['name']); ?>" class=" px-2 py-1 rounded w-full"
                          readonly></div>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                      <div class="px-2 py-2 font-semibold">Email</div>
                      <div class="px-2 py-2 col-span-2"><input type="text" id="email"
                          value="<?php echo htmlspecialchars($user['email']); ?>" class=" px-2 py-1 rounded w-full"
                          readonly></div>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                      <div class="px-2 py-2 font-semibold">Username</div>
                      <div class="px-2 py-2 col-span-2"><input type="text" id="username" name="username"
                          value="<?php echo htmlspecialchars($user['username']); ?>" class=" px-2 py-1 rounded w-full"
                          readonly></div>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                      <div class="px-2 py-2 font-semibold">Department</div>
                      <div class="px-2 py-2 col-span-2">
                        <select id="dept" name="dept" class="px-2 py-1 rounded w-full appearance-none text-black"
                          disabled>
                          <option value="CSE" <?php if ($user['dept'] == 'CSE')
                            echo 'selected'; ?>>CSE</option>
                          <option value="ICT" <?php if ($user['dept'] == 'ICT')
                            echo 'selected'; ?>>ICT</option>
                          <option value="DBA" <?php if ($user['dept'] == 'DBA')
                            echo 'selected'; ?>>DBA</option>
                        </select>
                      </div>
                    </div>



                    <div class="grid grid-cols-3 gap-2">
                      <div class="px-2 py-2 font-semibold">Session</div>
                      <div class="px-2 py-2 col-span-2"><input type="text" name="session"
                          value="<?php echo htmlspecialchars($user['session']); ?>" class=" px-2 py-1 rounded w-full"
                          readonly></div>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                      <div class="px-2 py-2 font-semibold">Student ID</div>
                      <div class="px-2 py-2 col-span-2"><input type="text" name="sid"
                          value="<?php echo htmlspecialchars($user['sid']); ?>" class=" px-2 py-1 rounded w-full"
                          readonly></div>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                      <div class="px-2 py-2 font-semibold">Profile Image</div>
                      <div class="px-2 py-2 col-span-2"><input type="file" name="profile_image"
                          class=" px-2 py-1 rounded w-full" readonly>
                      </div>
                    </div>
                  </div>
                  <div class="my-4 flex gap-3">
                    <!-- Edit Button -->
                    <button id="editProfile" class="bg-blue-500 text-white px-4 py-2 rounded">Edit</button>
                    <button id="cancel-btn" name="update_profile"
                      class="bg-blue-500 hidden text-white px-4 py-2 rounded">Cancel</button>
                    <button id="update-btn" type="submit" name="update_profile"
                      class="bg-blue-500 hidden text-white px-4 py-2 rounded">Update</button>
                  </div>
                </form>
              </div>
              <!-- End of About Section -->
            </div>
          </div>
        </div>
      </div>
    </main>
    <?php include 'components/footer.php' ?>
  </body>

</html>