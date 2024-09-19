<?php
require 'db/config.php';

// Get user ID from session
if (isset($_SESSION['userID'])) {
  $userID = $_SESSION['userID'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'del_index') {
  // Get the index ID from POST data
  $index_id = $_POST['index_id'];

  // Fetch all files associated with this index_id (topic_id)
  $stmt = $pdo->prepare("SELECT file_path FROM files WHERE topic_id = :index_id");
  $stmt->bindParam(':index_id', $index_id, PDO::PARAM_INT);
  $stmt->execute();
  $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Delete the files from the file system
  foreach ($files as $file) {
    $file_path = 'uploads/user' . $userID . '/' . $file['file_path']; // Assuming user_id is known or fetched
    if (file_exists($file_path)) {
      unlink($file_path); // Delete the file from the server
    }
  }

  // Delete files from the database
  $stmt = $pdo->prepare("DELETE FROM files WHERE topic_id = :index_id");
  $stmt->bindParam(':index_id', $index_id, PDO::PARAM_INT);
  $stmt->execute();

  // Now delete the index from the indexes table
  $stmt = $pdo->prepare("DELETE FROM indexes WHERE id = :id");
  $stmt->bindParam(':id', $index_id, PDO::PARAM_INT);

  if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Index and associated files deleted successfully.']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete index or files.']);
  }

  exit; // Stop further execution
}
?>




<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | FUS</title>
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

  <body class="bg-base-100 dark:bg-base-200 min-h-screen flex flex-col">
    <?php include "components/header.php" ?>
    <main class="flex-grow">
      <?php if (isset($_SESSION['userID'])): ?>
        <!-- FUS Tabs -->
        <section class="max-w-fit px-6 md:px-8 lg:px-12 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-10">

          <div class="flex justify-center">
            <div role="tablist" class="tabs tabs-boxed">
              <!-- Tab 1 (Upload) -->
              <label for="upload-tab" class="tab tab-lifted text-primary tab-active">
                Upload
              </label>

              <!-- Tab 2 (Library) -->
              <label for="library-tab" class="tab tab-lifted text-primary">
                Library
              </label>
            </div>
          </div>

          <!-- Tab content for Upload -->
          <input type="radio" id="upload-tab" name="my_tabs" class="hidden peer/upload-tab" checked />
          <div role="tabpanel"
            class="tab-content bg-base-100 border-base-300 rounded-box p-6  peer-checked/upload-tab:block">
            <h2 class="text-xl font-bold">Upload Files</h2>
            <p class="text-gray-600">Easily upload and manage your files.</p>

            <!-- upload form  -->
            <form id="file-upload-form" method="post" action="models/add_file.php" enctype="multipart/form-data"
              class="max-w-xl md:min-w-[500px] my-4 bg-white rounded-lg ">
              <!-- Topic Input -->
              <div class="form-control mb-4">
                <label class="label">
                  <span class="label-text">Topic</span>
                </label>
                <input type="text" name="topic" placeholder="Enter your topic" class="input input-bordered w-full"
                  required>
              </div>

              <!-- Semester Selection -->
              <div class="form-control mb-4">
                <label class="label">
                  <span class="label-text">Semester</span>
                </label>
                <select name="semester" class="select select-bordered w-full" required>
                  <option disabled selected>Select your semester</option>
                  <option value="1">Semester 1</option>
                  <option value="2">Semester 2</option>
                  <option value="3">Semester 3</option>
                  <option value="4">Semester 4</option>
                  <option value="5">Semester 5</option>
                  <option value="6">Semester 6</option>
                  <option value="7">Semester 7</option>
                  <option value="8">Semester 8</option>
                </select>
              </div>

              <!-- File Input with Font Awesome Icon and Browse Button -->
              <div class="form-control mb-6">
                <label class="label">
                  <span class="label-text">Upload File</span>
                </label>

                <label id="file-drop-zone"
                  class="flex items-center justify-center border-2 border-dashed border-gray-300 rounded-lg p-4 cursor-pointer hover:bg-gray-50">
                  <div class="flex items-center justify-center flex-col">
                    <div><i class="fa fa-cloud-upload-alt text-2xl text-gray-500"></i></div>
                    <div> <span class="text-gray-500">Drag and drop files here or click to browse</span></div>
                  </div>
                  <input id="file-input" type="file" name="files[]" class="hidden" multiple required>
                </label>

                <!-- Display the selected files here -->
                <div id="file-list" class="mt-4"></div>
              </div>


              <!-- Submit Button -->
              <div class="form-control">
                <button type="submit" class="btn btn-primary w-full">
                  <i id="loading-icon" class="fa fa-spinner fa-spin mr-2" style="display: none;"></i>
                  Upload File
                </button>
              </div>
            </form>



          </div>

          <!-- Tab content for Library -->
          <input type="radio" id="library-tab" name="my_tabs" class="hidden peer/library-tab" />
          <div role="tabpanel"
            class="tab-content bg-base-100 border-base-300 rounded-box p-6  hidden peer-checked/library-tab:block">
            <h2 class="text-xl font-bold">File Library</h2>
            <p class="text-gray-600">View and organize your uploaded files.</p>

            <!-- Filter Section -->
            <div class="my-4 grid grid-cols-3 gap-4">
              <!-- Topic Filter -->
              <input id="filter-topic" type="text" placeholder="Topic"
                class="input input-bordered input-sm md:input-md w-full">

              <!-- Semester Filter -->
              <input id="filter-semester" type="text" placeholder="Semester"
                class="input input-bordered input-sm md:input-md w-full">

              <!-- Date Filter -->
              <input id="filter-date" type="date" class="input input-bordered input-sm md:input-md w-full">
            </div>

            <!-- HTML Table for displaying indexes -->
            <div class="table-container overflow-x-auto my-4">

            </div>

          </div>

        </section>

        <!-- modal for displaying files  -->
        <!-- DaisyUI Modal -->
        <div id="fileModal" class="modal">
          <div class="modal-box w-11/12 max-w-5xl">
            <div class="overflow-x-auto no-scrollbar mt-4">
              <table class="table w-full">
                <thead>
                  <tr>
                    <th class="px-4 py-3 text-left">File Name</th>
                    <th class="px-4 py-3 text-left">File Type</th>
                    <th class="px-4 py-3 text-left">Size</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                  </tr>
                </thead>
                <tbody id="modal-file-list">
                  <!-- Table rows will be inserted here dynamically -->
                </tbody>
              </table>
            </div>
            <div class="modal-action">
              <button class="btn btn-primary" onclick="closeModal()">Close</button>
            </div>
          </div>
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

      <?php else: ?>
        <!-- Hero section -->
        <section class="bg-base-100 min-h-screen mx-4 md:flex flex-col justify-center md:-mt-[100px] lg:-mt-[120px] ">
          <div class="grid max-w-screen-xl px-4 md:px-8 lg:px-12 py-8 mx-auto md:gap-8 xl:gap-0 md:py-10 md:grid-cols-12">
            <div class="mr-auto place-self-center md:col-span-7">
              <h1
                class="max-w-2xl mb-4 text-3xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl dark:text-primary-content">
                Easy File Upload for CSTU's Students
              </h1>
              <p class="max-w-2xl mb-6 font-light text-gray-500 lg:mb-8 md:text-lg lg:text-xl dark:text-gray-400">
                Simplify your file submissions with a user-friendly platform. Upload your documents and manage your files
                efficiently with FUS.
              </p>
              <?php if (!isset($_SESSION['userID'])): ?>
                <a href="registration.php"
                  class="inline-flex items-center justify-center px-5 py-3 mr-3 text-base font-medium text-center text-white rounded-lg bg-primary hover:bg-primary-focus focus:ring-4 focus:ring-primary-content">
                  Get Started
                  <svg class="w-5 h-5 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                      d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                      clip-rule="evenodd"></path>
                  </svg>
                </a>
              <?php endif; ?>
            </div>
            <div class="hidden md:mt-0 md:col-span-5 md:flex">
              <img src="img/upload.gif" alt="mockup" class=" w-full h-full rounded-lg">
            </div>
          </div>
        </section>

      <?php endif; ?>
    </main>
    <?php include "components/footer.php" ?>

    <script>
      $(document).ready(function () {
        const fileInput = $('#file-input');
        const fileList = $('#file-list');
        let selectedFiles = [];

        // Function to display the selected files
        function displaySelectedFiles() {
          fileList.empty(); // Clear the list
          if (selectedFiles.length > 0) {
            selectedFiles.forEach((file, index) => {
              fileList.append(`
          <div class="flex items-center justify-between p-2 bg-gray-100 rounded mb-2">
            <span>${file.name}</span>
            <button type="button" class="remove-file text-red-500" data-index="${index}">
              <i class="fa fa-trash-alt"></i>
            </button>
          </div>
        `);
            });
          } else {
            fileList.html('<span class="text-gray-500">No files selected.</span>');
          }
        }

        // Function to update the file input element with the correct files
        function updateFileInput() {
          const dataTransfer = new DataTransfer();
          selectedFiles.forEach(file => {
            dataTransfer.items.add(file);
          });
          fileInput[0].files = dataTransfer.files; // Update the file input
        }

        // Handle file input change (when files are selected)
        fileInput.on('change', function (e) {
          const newFiles = Array.from(e.target.files);

          // Add new files to the existing ones (without duplicates)
          newFiles.forEach(newFile => {
            if (!selectedFiles.some(file => file.name === newFile.name)) {
              selectedFiles.push(newFile);
            }
          });

          displaySelectedFiles();
          updateFileInput(); // Sync input element
        });

        // Handle removing a file
        fileList.on('click', '.remove-file', function () {
          const index = $(this).data('index');
          selectedFiles.splice(index, 1);
          displaySelectedFiles();
          updateFileInput(); // Sync input element
        });

        // Handle drag-and-drop
        $('#file-drop-zone').on('dragover', function (e) {
          e.preventDefault();
          $(this).addClass('bg-blue-100 border-blue-500');
        }).on('dragleave', function () {
          $(this).removeClass('bg-blue-100 border-blue-500');
        }).on('drop', function (e) {
          e.preventDefault();
          $(this).removeClass('bg-blue-100 border-blue-500');

          const newFiles = Array.from(e.originalEvent.dataTransfer.files);

          // Add new files to the existing ones (without duplicates)
          newFiles.forEach(newFile => {
            if (!selectedFiles.some(file => file.name === newFile.name)) {
              selectedFiles.push(newFile);
            }
          });

          displaySelectedFiles();
          updateFileInput(); // Sync input element
        });
      });

    </script>
    <script>
      // Open modal and fetch files
      function openFilesModal(topic_id) {
        fetchFiles(topic_id);
        document.getElementById('fileModal').classList.add('modal-open');
      }

      // Fetch files and populate table
      function fetchFiles(topic_id) {
        $.ajax({
          url: 'models/fetch_files.php',
          type: 'POST',
          data: { topic_id: topic_id },
          dataType: 'json',
          success: function (data) {
            let rows = '';
            $.each(data, function (index, file) {
              rows += `
                        <tr class="border-t" id="file-row-${file.id}">
                            <td class="px-4 py-2">${file.file_name}</td>
                            <td class="px-4 py-2">${file.file_type}</td>
                            <td class="px-4 py-2">${file.size} MB</td>
                            <td class="px-4 py-2 ">
                               <div class="flex gap-2 justify-center items-center">
                                <a href="uploads/user<?= $_SESSION['userID'] ?>/${file.file_name}" class="btn btn-sm text-blue-500" download>Download</a>
                                  <button onclick="deleteFile(${file.id})" class="file-del-btn btn btn-sm text-red-500">Delete</button>
                               </div>
                              </td>
                          </tr>`;
            });
            $('#modal-file-list').html(rows);
          }
        });
      }

      // Close modal
      function closeModal() {
        document.getElementById('fileModal').classList.remove('modal-open');
      }

      // Close modal when clicking outside of it
      window.onclick = function (event) {
        const modal = document.getElementById('fileModal');
        if (event.target === modal) {
          closeModal();
        }
      };

      // Delete file using AJAX call with confirmation
      function deleteFile(file_id) {
        // Ask for confirmation before deleting the file
        const confirmDelete = confirm('Are you sure you want to delete this file?');

        if (confirmDelete) {
          $.ajax({
            url: 'models/delete_file.php',
            type: 'POST',
            data: { file_id: file_id },
            success: function (response) {
              // If deletion was successful, remove the row from the table
              if (response === 'success') {
                showToast('error-toast', 'File deleted successfully!');
                $('#file-row-' + file_id).remove();
              } else {
                alert('Failed to delete the file');
              }
            }
          });
        }
      }

    </script>

    <script>
      function renderTable() {
        // Clear the table container before rendering new data
        $('.table-container').html('<p>Loading...</p>');

        // Perform AJAX request to fetch the data
        $.ajax({
          url: 'models/fetch_indexes.php', // URL of the PHP file that fetches data
          type: 'GET',
          dataType: 'json',
          success: function (data) {
            // Check if there was an error
            if (data.error) {
              $('.table-container').html('<p>Error: ' + data.error + '</p>');
              return;
            }

            // Check if the data is empty
            if (data.length === 0) {
              $('.table-container').html('<p>No topics found.</p>');
              return;
            }

            // Start building the table HTML
            let tableHtml = `
        <table class="table w-full" id="indexesTable">
          <thead>
            <tr>
              <th>Topic</th>
              <th>Semester</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
        `;

            // Loop through the returned data and create table rows
            data.forEach(function (index) {
              tableHtml += `
          <tr id="tr-${index.id}">
            <td class="topic whitespace-nowrap">${index.topics}</td>
            <td class="semester whitespace-nowrap">${index.semester}</td>
            <td class="date whitespace-nowrap">${index.created_at}</td>
            <td class="flex gap-2 whitespace-nowrap">
              <button class="text-green-500 btn btn-sm" onclick="openFilesModal(${index.id})">Files</button>
              <button class="text-red-500 btn btn-sm" onclick="deleteIndex(${index.id})">Delete</button>
            </td>
          </tr>
        `;
            });

            // Close the table tag
            tableHtml += `
          </tbody>
        </table>
      `;

            // Render the constructed table HTML into the .table-container
            $('.table-container').html(tableHtml);
          },
          error: function (xhr, status, error) {
            console.error('An error occurred:', error);
            $('.table-container').html('<p>Error loading data</p>');
          }
        });
      }

      // Call the renderTable function to load the data
      renderTable();
    </script>

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
        }, 2000);
      }

      function deleteIndex(index_id) {
        if (confirm("Are you sure you want to delete this topic?")) {

          // Create a FormData object to hold the POST data
          let formData = new FormData();
          formData.append('action', 'del_index');
          formData.append('index_id', index_id);

          // Perform the AJAX request
          $.ajax({
            url: '', // Same page URL (or specify if different)
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
              // Show a toast message for success
              showToast('error-toast', 'Topic deleted successfully!');
              // Remove the associated table row
              $('#tr-' + index_id).remove();
            },
            error: function (xhr, status, error) {
              // Handle the error
              console.error(error);
              alert('An error occurred while deleting the index.');
            }
          });
        }
      }
    </script>

    <script>
      $(document).ready(function () {
        // Function to filter the table
        function filterTable() {
          const topicFilter = $('#filter-topic').val().toLowerCase();
          const semesterFilter = $('#filter-semester').val().toLowerCase();
          const dateFilter = $('#filter-date').val();

          $('#indexesTable tbody tr').each(function () {
            const topic = $(this).find('.topic').text().toLowerCase();
            const semester = $(this).find('.semester').text().toLowerCase();
            const date = $(this).find('.date').text();

            // Check if each filter condition is met
            const topicMatch = topic.includes(topicFilter);
            const semesterMatch = semester.includes(semesterFilter);
            const dateMatch = dateFilter === "" || date.includes(dateFilter);

            // Show or hide the row based on filter match
            if (topicMatch && semesterMatch && dateMatch) {
              $(this).show();
            } else {
              $(this).hide();
            }
          });
        }

        // Attach event listeners to filter inputs
        $('#filter-topic').on('input', filterTable);
        $('#filter-semester').on('input', filterTable);
        $('#filter-date').on('change', filterTable);
      });

    </script>

    <script>
      $(document).ready(function () {
        $('#file-upload-form').on('submit', function (e) {
          e.preventDefault();

          var formData = new FormData(this);
          formData.append('user_id', <?= $userID; ?>); // Pass user ID from session

          // Show loading spinner
          $('#loading-icon').show();

          $.ajax({
            url: 'models/add_file.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
              $('#loading-icon').hide();
              $('#file-upload-form').trigger("reset");
              $('#file-list').empty();
              renderTable();
              // Show success toast
              showToast('success-toast', 'File uploaded successfully!');

            },
            error: function () {
              $('#loading-icon').hide();

              // Show error toast
              showToast('error-toast', 'File upload failed, please try again.');
            }
          });
        });


      });
    </script>

    <script>
      $('.tabs .tab').on('click', function () {
        $('.tabs .tab').removeClass("tab-active");
        $(this).addClass("tab-active");
      })
    </script>
  </body>

</html>