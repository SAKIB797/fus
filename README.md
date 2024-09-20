# File Upload System (FUS)

Welcome to the File Upload System (FUS)! This project is designed to help students at Chandpur Science and Technology University (CSTU) easily upload and manage their files and documents. It addresses the challenge of uploading files while using lab PCs, where logging into Google Drive or other services is often impractical. With this system, students can create an account and upload their files through a user-friendly interface.

## Table of Contents
- [Getting Started](#getting-started)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [License](#license)

## Getting Started

To get started with the File Upload System, follow the steps below to download the project and set it up on your local environment.

### Prerequisites

- PHP (version 7.0 or higher)
- MySQL (version 5.6 or higher)
- A web server (like Apache or Nginx)

## Installation

1. **Download the Project**

   Clone the repository or download the zip file containing the project.

   ```bash
   git clone https://github.com/ihSakib/fus.git
   cd fus
   ```

2. **Import the Database**

   Download the `fus.sql` file from the `database` folder in the project directory.

   Use the following command to import the SQL file into your MySQL database:

   ```bash
   mysql -u username -p database_name < path/to/fus.sql
   ```

   Replace `username`, `database_name`, and `path/to/fus.sql` with your MySQL username, the name of your database, and the path to the SQL file, respectively.

## Configuration

1. **Edit Database Configuration**

   Open the `db/config.php` file and update the database connection details:

   ```php
   $dbHost = 'localhost'; // Database host
   $dbUser = 'your_username'; // Database username
   $dbPass = 'your_password'; // Database password
   $dbName = 'your_database'; // Database name
   ```

2. **Edit SMTP Configuration**

   Open the `smtp/config.php` file and update the SMTP settings for email notifications:

   ```php
   $smtpHost = 'smtp.your-email-provider.com'; // SMTP host
   $smtpUser = 'your_email@example.com'; // SMTP username
   $smtpPass = 'your_email_password'; // SMTP password
   $smtpPort = 587; // SMTP port (usually 587 for TLS)
   ```

## Usage

1. **Start the Server**

   Make sure your web server is running. You can use the built-in PHP server for testing:

   ```bash
   php -S localhost:8000
   ```

2. **Access the Application**

   Open your web browser and go to `http://localhost:8000` (or your configured web server URL).

3. **Upload Files**

   Use the file upload interface to select and upload your files. Follow the on-screen instructions to manage your uploaded files.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.

---

Feel free to contribute or report issues! Happy uploading!  
Let me know if you need any more changes!

