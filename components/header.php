<?php

// Assuming userID is stored in session
if (isset($_SESSION['userID'])) {
  $userID = $_SESSION['userID'];

  // Fetch name, email, and img from the users table using the session userID
  $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :userID");
  $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  // Check if user data is found
  if ($user) {
    // Construct image path
    $imagePath = 'uploads/user' . $userID . '/profile_img/' . $user['img'];
    $name = $user['name'];
    $dept = $user['dept'];
    $session = $user['session'];
    $sid = $user['sid'];
  }
} else {
}
?>

<nav class="navbar bg-base-100  px-4 lg:px-16 border-b z-[500]">
  <div class="navbar-start">
    <div class="dropdown">
      <button tabindex="0" class="btn btn-ghost lg:hidden">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" />
        </svg>
      </button>
      <ul tabindex="0" class="menu menu-sm dropdown-content bg-base-100 rounded-box z-[1] mt-3 w-52 p-2 shadow-lg">
        <li><a href="index.php">Home</a></li>
        <li><a href="about.php">About</a></li>
        <li><a href="contact.php">Contact</a></li>
      </ul>
    </div>
    <a href="index.php" class="text-xl hidden lg:inline">FUS</a>
  </div>

  <div class="lg:hidden">
    <a href="index.php" class="text-xl">FUS</a>
  </div>

  <div class="navbar-center hidden lg:flex">
    <ul class="menu menu-horizontal px-1">
      <li><a href="index.php">Home</a></li>
      <li><a href="about.php">About</a></li>
      <li><a href="contact.php">Contact</a></li>
    </ul>
  </div>

  <div class="navbar-end">
    <?php if (!isset($_SESSION['userID'])): ?>
      <a href="registration.php" class="btn btn-sm z-50">Login</a>
    <?php else: ?>
      <div x-data="{ isOpen: false }" class="relative">
        <button @click="isOpen = !isOpen" class="flex items-center">
          <img src="<?php echo $imagePath ?>" alt="Profile" class="bg-gray-100 rounded-full p-1 w-10 h-10">
        </button>
        <div x-show="isOpen" @click.away="isOpen = false"
          class="absolute right-0 mt-6 w-52 p-4 bg-base-100 border border-gray-200  rounded-md shadow-lg z-50">
          <div class="flex flex-col items-center mt-6  -mx-2">
            <img class="object-cover w-24 h-24 mx-2 rounded-full border-2 border-gray-700" src="<?php echo $imagePath ?>"
              alt="avatar">
            <h4 class="mx-2 mt-2 font-medium text-gray-800 "><?php echo $name ?></h4>
            <p class="mx-2 mt-1 text-sm font-medium text-gray-600 "><?php echo $dept . ' ' . $session ?>
            </p>
            <p class="mx-2 mt-1 text-sm font-medium text-gray-600 "><?php echo $sid ?></p>
          </div>
          <hr class="my-4">
          <div class="mb-6">
            <a href="profile.php" class="flex items-center px-3 py-2 hover:bg-blue-100  rounded-md">
              <svg class="w-6 h-6 mr-3 text-blue-500 " fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 14l6.16-3.422A12.045 12.045 0 0112 22.026v-7.622z" />
              </svg>
              View Profile
            </a>
            <a href="functions/logout.php" class="flex items-center px-3 py-2 text-red-600 hover:bg-red-100  rounded-md">
              <svg class="w-6 h-6 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H3m0 0l-3 3m3-3l3-3" />
              </svg>
              Logout
            </a>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</nav>