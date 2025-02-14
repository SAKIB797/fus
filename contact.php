<?php
require 'smtp/config.php';
require 'db/config.php'; // Your database configuration


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $message = $_POST['message'];
  $subject = $_POST['subject'];

  try {

    // Recipients
    $mail->setFrom('no-reply@fus.com', 'New Message - FUS');
    $mail->addAddress('ihsakib@outlook.com'); // Add a recipient

    // Content
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = 'New Message - FUS';
    $mail->Body = "
            <html>
            <head>
                <style>
                    .container {
                        font-family: Arial, sans-serif;
                        color: #333;
                        padding: 20px;
                        border: 1px solid #ccc;
                        max-width: 600px;
                        margin: 0 auto;
                    }
                    .footer {
                        margin-top: 20px;
                        padding: 10px;
                        text-align: center;
                        font-size: 12px;
                        color: #777;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    
                    <p><strong>Subject:</strong> {$subject}</p>
                    <p><strong>Name:</strong> {$name}</p>
                    <p><strong>Email:</strong> {$email}</p>
                    <p><strong>Message:</strong> {$message}</p>
                </div>
                <div class='footer'>
                    &copy; " . date('Y') . " FUS. All rights reserved.
                </div>
            </body>
            </html>
        ";

    $mail->send();
    echo json_encode(['status' => 'success', 'message' => 'Message sent successfully!']);
  } catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo]);
  }
  exit;
}
?>


<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | FUS</title>
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
    <main class="flex-grow min-h-dvh flex flex-col justify-center">
      <div
        class="grid sm:grid-cols-2 items-end gap-16 p-6 md:p-10 mx-auto max-w-4xl bg-white font-[sans-serif] -mt-[80px] sm:-mt-[100px] md:-mt-[120px] lg:-mt-[170px] ">
        <div>
          <h1 class="text-gray-800 text-3xl font-extrabold">Let's Talk</h1>
          <p class="text-sm text-gray-500 mt-4">Got a big idea or project that needs efficient file management? Reach
            out to us! We'd love to hear about your project and help streamline your file uploading process with FUS
            (File Upload System).</p>

          <div class="mt-8 sm:mt-12">
            <h2 class="text-gray-800 text-base font-bold">Email</h2>
            <ul class="mt-4">
              <li class="flex items-center">
                <div class="bg-[#e6e6e6cf] h-10 w-10 rounded-full flex items-center justify-center shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill='#007bff'
                    viewBox="0 0 479.058 479.058">
                    <path
                      d="M434.146 59.882H44.912C20.146 59.882 0 80.028 0 104.794v269.47c0 24.766 20.146 44.912 44.912 44.912h389.234c24.766 0 44.912-20.146 44.912-44.912v-269.47c0-24.766-20.146-44.912-44.912-44.912zm0 29.941c2.034 0 3.969.422 5.738 1.159L239.529 264.631 39.173 90.982a14.902 14.902 0 0 1 5.738-1.159zm0 299.411H44.912c-8.26 0-14.971-6.71-14.971-14.971V122.615l199.778 173.141c2.822 2.441 6.316 3.655 9.81 3.655s6.988-1.213 9.81-3.655l199.778-173.141v251.649c-.001 8.26-6.711 14.97-14.971 14.97z"
                      data-original="#000000" />
                  </svg>
                </div>
                <a href="mailto:ihsakib@outlook.com" class="text-[#007bff] text-sm ml-4">
                  <small class="block">Mail</small>
                  <strong>ihsakib@outlook.com</strong>
                </a>
              </li>
            </ul>
          </div>

          <div class="mt-8 sm:mt-12">
            <h2 class="text-gray-800 text-base font-bold">Socials</h2>
            <ul class="flex mt-4 space-x-4">
              <li class="bg-[#e6e6e6cf] h-10 w-10 rounded-full flex items-center justify-center shrink-0">
                <a href="https://www.linkedin.com/in/ihSakib" target="_blank"><svg xmlns="http://www.w3.org/2000/svg"
                    width="20px" height="20px" fill='#007bff' viewBox="0 0 511 512">
                    <path
                      d="M111.898 160.664H15.5c-8.285 0-15 6.719-15 15V497c0 8.285 6.715 15 15 15h96.398c8.286 0 15-6.715 15-15V175.664c0-8.281-6.714-15-15-15zM96.898 482H30.5V190.664h66.398zM63.703 0C28.852 0 .5 28.352.5 63.195c0 34.852 28.352 63.2 63.203 63.2 34.848 0 63.195-28.352 63.195-63.2C126.898 28.352 98.551 0 63.703 0zm0 96.395c-18.308 0-33.203-14.891-33.203-33.2C30.5 44.891 45.395 30 63.703 30c18.305 0 33.195 14.89 33.195 33.195 0 18.309-14.89 33.2-33.195 33.2zm289.207 62.148c-22.8 0-45.273 5.496-65.398 15.777-.684-7.652-7.11-13.656-14.942-13.656h-96.406c-8.281 0-15 6.719-15 15V497c0 8.285 6.719 15 15 15h96.406c8.285 0 15-6.715 15-15V320.266c0-22.735 18.5-41.23 41.235-41.23 22.734 0 41.226 18.495 41.226 41.23V497c0 8.285 6.719 15 15 15h96.403c8.285 0 15-6.715 15-15V302.066c0-79.14-64.383-143.523-143.524-143.523zM466.434 482h-66.399V320.266c0-39.278-31.953-71.23-71.226-71.23-39.282 0-71.239 31.952-71.239 71.23V482h-66.402V190.664h66.402v11.082c0 5.77 3.309 11.027 8.512 13.524a15.01 15.01 0 0 0 15.875-1.82c20.313-16.294 44.852-24.907 70.953-24.907 62.598 0 113.524 50.926 113.524 113.523zm0 0"
                      data-original="#000000" />
                  </svg>
                </a>
              </li>

              <li class="bg-[#e6e6e6cf] h-10 w-10 rounded-full flex items-center justify-center shrink-0">
                <a href="https://github.com/ihSakib" target="_blank" rel="noopener noreferrer">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="#007bff" viewBox="0 0 24 24">
                    <path
                      d="M12 2C6.475 2 2 6.475 2 12c0 4.42 2.865 8.165 6.839 9.49.5.092.682-.218.682-.484 0-.24-.009-.87-.013-1.708-2.782.604-3.369-1.343-3.369-1.343-.455-1.155-1.11-1.462-1.11-1.462-.908-.62.069-.608.069-.608 1.003.07 1.53 1.03 1.53 1.03.893 1.53 2.342 1.088 2.912.832.091-.647.35-1.088.637-1.34-2.22-.252-4.555-1.11-4.555-4.933 0-1.09.39-1.98 1.03-2.68-.103-.253-.447-1.27.098-2.645 0 0 .84-.27 2.75 1.03a9.564 9.564 0 0 1 2.5-.336 9.562 9.562 0 0 1 2.5.336c1.91-1.3 2.75-1.03 2.75-1.03.546 1.374.202 2.392.1 2.645.64.7 1.03 1.59 1.03 2.68 0 3.83-2.34 4.677-4.567 4.923.36.31.68.92.68 1.852 0 1.337-.012 2.417-.012 2.745 0 .268.18.58.688.482C19.135 20.165 22 16.42 22 12c0-5.525-4.475-10-10-10Z">
                    </path>
                  </svg>
                </a>
              </li>

              <li class="bg-[#e6e6e6cf] h-10 w-10 rounded-full flex items-center justify-center shrink-0">
                <a href="https://facebook.com/ihSakib0" target="_blank">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill='#007bff' viewBox="0 0 24 24">
                    <path
                      d="M6.812 13.937H9.33v9.312c0 .414.335.75.75.75l4.007.001a.75.75 0 0 0 .75-.75v-9.312h2.387a.75.75 0 0 0 .744-.657l.498-4a.75.75 0 0 0-.744-.843h-2.885c.113-2.471-.435-3.202 1.172-3.202 1.088-.13 2.804.421 2.804-.75V.909a.75.75 0 0 0-.648-.743A26.926 26.926 0 0 0 15.071 0c-7.01 0-5.567 7.772-5.74 8.437H6.812a.75.75 0 0 0-.75.75v4c0 .414.336.75.75.75zm.75-3.999h2.518a.75.75 0 0 0 .75-.75V6.037c0-2.883 1.545-4.536 4.24-4.536.878 0 1.686.043 2.242.087v2.149c-.402.205-3.976-.884-3.976 2.697v2.755c0 .414.336.75.75.75h2.786l-.312 2.5h-2.474a.75.75 0 0 0-.75.75V22.5h-2.505v-9.312a.75.75 0 0 0-.75-.75H7.562z"
                      data-original="#000000" />
                  </svg>
                </a>
              </li>


            </ul>
          </div>
        </div>

        <form method="post" action="" id="contactForm" class="ml-auto space-y-4">
          <input type="text" placeholder="Name" name="name"
            class="w-full rounded-md py-3 px-4 bg-gray-100 text-gray-800 text-sm outline-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
            required>
          <input type="email" placeholder="Email" name="email"
            class="w-full rounded-md py-3 px-4 bg-gray-100 text-gray-800 text-sm outline-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
            required>
          <input type="text" placeholder="Subject" name="subject"
            class="w-full rounded-md py-3 px-4 bg-gray-100 text-gray-800 text-sm outline-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
            required>
          <textarea placeholder="Message" rows="6" name="message"
            class="w-full rounded-md px-4 bg-gray-100 text-gray-800 text-sm pt-3 outline-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
            required></textarea>
          <button type="submit"
            class="text-white bg-blue-500 hover:bg-blue-600 tracking-wide rounded-md text-sm px-4 py-3 w-full mt-6 relative">
            <i id="loading-icon" class="fa fa-spinner fa-spin mr-2" style="display: none;"></i>
            <span id="sendText">Send</span>
          </button>
        </form>

      </div>

      <!-- Toast Notifications Container -->
      <div class="toast-container">
        <div id="success-toast"
          class="toast toast-success bottom-20 z-[1000] right-6 hidden bg-green-500 text-white p-4 rounded-lg shadow-md">
          <div class="toast-icon inline-block mr-2">
            <i class="fa fa-check-circle"></i>
          </div>
          <div class="toast-content inline-block">
            <span id="success-message">File uploaded successfully!</span>
          </div>
        </div>
        <div id="error-toast"
          class="toast toast-error bottom-20 right-6 z-[1000] hidden bg-red-500 text-white p-4 rounded-lg shadow-md">
          <div class="toast-icon inline-block mr-2">
            <i class="fa fa-times-circle"></i>
          </div>
          <div class="toast-content inline-block">
            <span id="error-message">File upload failed, please try again.</span>
          </div>
        </div>
      </div>

    </main>
    <?php include 'components/footer.php' ?>
    <script>
      function showToast(toastId, message) {
        // Set the message for the toast
        $('#' + toastId + ' #success-message').text(message);
        $('#' + toastId + ' #error-message').text(message);

        // Show the toast
        $('#' + toastId).removeClass('hidden').addClass('block');

        // Hide the toast after 2 seconds
        setTimeout(function () {
          $('#' + toastId).removeClass('block').addClass('hidden');
        }, 3000);
      }
    </script>
    <script>
      $(document).ready(function () {
        $('#contactForm').on('submit', function (e) {
          e.preventDefault();
          $('#loading-icon').show();
          $('#sendText').html("Sending...")
          $.ajax({
            url: '',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
              if (response.status === 'success') {
                $('#loading-icon').hide();
                $('#sendText').html("Send")
                showToast('success-toast', 'Message sent successfully!');
                $('#contactForm')[0].reset(); // Reset the form
              } else {
                alert('Failed to send message!');
              }
            }
          });
        });
      });
    </script>
  </body>

</html>