<?php
session_start();

$host = 'localhost';
$username = 'user';
$password = 'password123';
$dbname = 'dolphin_crm';

$firstName = $_POST["firstName"] ?? '';
$lastName = $_POST["lastName"] ?? '';
$email = $_POST["email"] ?? '';
$password_get = $_POST["password"] ?? '';
$role = $_POST["role"] ?? '';

$firstName_filter = filter_var($firstName, FILTER_SANITIZE_STRING);
$lastName_filter = filter_var($lastName, FILTER_SANITIZE_STRING);
$email_filter = filter_var($email, FILTER_SANITIZE_EMAIL);
$password_filter = filter_var($password_get, FILTER_SANITIZE_STRING);
$role_filter = filter_var($role, FILTER_SANITIZE_STRING);

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    echo "Failed to connect: " . $conn->connect_error;
    exit;
}

try {
    // Password validation
    if (!preg_match('/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password_filter)) {
        echo "<script>
                document.getElementById('result').innerHTML = 'Password does not meet these requirements: at least one number, one lowercase letter, one uppercase letter, and at least 8 characters long.';
                document.getElementById('text').style.display = 'block';
                setTimeout(function() {
                    document.getElementById('text').style.display = 'none';
                }, 5000);
              </script>";
        exit;
    }

    // Hash the password if it passes validation
    $hashedPassword = password_hash($password_filter, PASSWORD_DEFAULT);

    // Role validation
    if ($role_filter !== 'Admin' && $role_filter !== 'Member') {
        echo "<script>
                document.getElementById('result').innerHTML = 'Invalid role selected.';
                document.getElementById('text').style.display = 'block';
                setTimeout(function() {
                    document.getElementById('text').style.display = 'none';
                }, 5000);
              </script>";
        exit;
    }

    $sql = "INSERT INTO Users (firstname, lastname, email, pwd, _role) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "<script>
                document.getElementById('result').innerHTML = 'Error in the SQL query: " . $conn->error . "';
                document.getElementById('text').style.display = 'block';
                setTimeout(function() {
                    document.getElementById('text').style.display = 'none';
                }, 5000);
              </script>";
        exit;
    }

    if (!$stmt->bind_param("sssss", $firstName_filter, $lastName_filter, $email_filter, $hashedPassword, $role_filter)) {
        echo "<script>
                document.getElementById('result').innerHTML = 'Error binding parameters: " . $stmt->error . "';
                document.getElementById('text').style.display = 'block';
                setTimeout(function() {
                    document.getElementById('text').style.display = 'none';
                }, 5000);
              </script>";
        exit;
    }

    if ($stmt->execute()) {
        echo "<script>
                document.getElementById('result').innerHTML = 'User Added Successfully!';
                document.getElementById('text').style.display = 'block';
                setTimeout(function() {
                    document.getElementById('text').style.display = 'none';
                }, 5000);
                setTimeout(function() {
                    window.location.assign('../php/dashboard.php');
                }, 2000);
              </script>";
    } else {
        echo "<script>
                document.getElementById('result').innerHTML = 'Error Adding User!';
                document.getElementById('text').style.display = 'block';
                setTimeout(function() {
                    document.getElementById('text').style.display = 'none';
                }, 5000);
              </script>";
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo "<script>
            document.getElementById('result').innerHTML = 'An error occurred: " . $e->getMessage() . "';
            document.getElementById('text').style.display = 'block';
            setTimeout(function() {
                document.getElementById('text').style.display = 'none';
            }, 5000);
          </script>";
    exit;
}
?>
