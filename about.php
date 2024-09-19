<?php require 'db/config.php'; ?>
<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About | FUS</title>

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
    <?php include 'components/header.php'; ?>

    <main class="container mx-auto flex-grow">
      <!-- About FUS Section -->
      <section
        class="min-h-screen mx-4  flex flex-col justify-center -mt-[50px] md:-mt-[100px] lg:-mt-[120px]   md:mx-8 lg:mx-15 xl:mb-20">
        <div class="container max-w-screen-xl mx-auto grid grid-cols-1 items-center md:grid-cols-5 ">
          <!-- Left Side: Text -->
          <div class=" mb-6 md:mb-0 px-4 md:col-span-3  ">
            <h1 class=" text-3xl md:text-3xl lg:text-4xl font-bold mb-4 text-gray-900">About FUS</h1>
            <p class=" lg:text-lg text-gray-700 mb-4">
              The <strong class="font-semibold">File Upload System (FUS)</strong> is a user-friendly platform designed
              to
              assist students at <strong>Chandpur Science and Technology University (CSTU)</strong> in easily uploading
              and
              managing their academic files.
            </p>
            <p class=" lg:text-lg text-gray-700">
              FUS simplifies the submission process for academic purposes, ensuring a smooth experience for students.
              Our
              university, <strong>CSTU</strong>, is committed to using technology to enhance student productivity and
              improve
              communication with faculty. FUS is a part of this initiative to streamline academic workflows.
            </p>
          </div>

          <!-- Right Side: Image -->
          <div class=" px-4 hidden  md:col-span-2 md:block ">
            <img src="img/5614966_2933150.svg" alt="About FUS Image" class=" h-auto rounded-lg  object-cover">
          </div>
        </div>
      </section>


      <!-- Developer Profile Section -->
      <section class="min-h-screen  flex flex-col justify-center mx-8">
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-center text-gray-900 mb-6 md:mb-8 underline">
          Developer Profile</h2>

        <div class="flex flex-col mb-4 md:flex-row bg-white shadow-lg rounded-lg overflow-hidden max-w-4xl mx-auto ">
          <figure class="md:w-1/3 p-6 md:p-0">
            <img src="img/me.jpg" alt="Developer Image"
              class="w-1/2 rounded-full mx-auto  border-2 md:h-full md:w-full md:rounded-none md:border-none object-cover" />
          </figure>
          <div class="md:w-2/3 p-6 pt-0 md:pt-6">
            <h3 class="text-2xl font-bold mb-2 text-gray-800">Iftekhar Sakib</h3>
            <p class="text-gray-600 mb-4">
              Iftekhar is the developer behind FUS. As a student at CSTU, he designed this platform to simplify file
              uploads
              for fellow students. John is passionate about building efficient solutions to improve academic workflows.
            </p>
            <div class="mb-4">
              <p class="text-sm text-gray-500 mb-2">
                <i class="fas fa-envelope mr-2"></i> Email: <a href="mailto:ihsakib@outlook.com"
                  class="text-blue-500">ihsakib@outlook.com</a>
              </p>
              <p class="text-sm text-gray-500">
                <i class="fab fa-linkedin mr-2"></i> LinkedIn: <a href="https://linkedin.com/in/ihsakib"
                  class="text-blue-500" target="_blank">linkedin.com/in/ihsakib</a>
              </p>
            </div>
            <a href="mailto:ihsakib@outlook.com"
              class="btn btn-primary bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg px-4 py-2">
              Contact
            </a>
          </div>
        </div>
      </section>



    </main>

    <?php include 'components/footer.php'; ?>
  </body>

</html>