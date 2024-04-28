<?php
// Function to establish database connection
function connectToDatabase() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "project";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Function to execute SQL queries
function executeQuery($conn, $sql) {
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        return false;
    }
}

// Function to check if username or email already exists
function isUsernameOrEmailExists($conn, $username, $email) {
    $result = $conn->query("SELECT * FROM accounts WHERE username='$username' OR email='$email'");
    return ($result->num_rows > 0);
}

// Function to handle registration
function registerUser($conn, $username, $email, $password) {
    if (isUsernameOrEmailExists($conn, $username, $email)) {
        echo "Username or email already exists.";
    } else {
        if (executeQuery($conn, "INSERT INTO accounts (username, email, password) VALUES ('$username', '$email', '$password')")) {
            echo "Registration successful!";
        }
    }
}

// Function to handle login
function loginUser($conn, $username, $password) {
    $result = $conn->query("SELECT * FROM accounts WHERE username='$username' AND password='$password'");
    if ($result->num_rows > 0) {
        echo "Login successful!";
    } else {
        echo "Invalid username or password.";
    }
}

// Function to handle forgot password
function forgotPassword($conn, $username, $email) {
    $result = $conn->query("SELECT * FROM accounts WHERE username='$username' AND email='$email'");
    if ($result->num_rows > 0) {
        // Display form to reset password
        echo "
        <form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>
            New Password: <input type='password' name='new_password'><br>
            <input type='hidden' name='username' value='$username'>
            <input type='submit' name='reset' value='Reset Password'>
        </form>";
    } else {
        echo "Invalid username or email.";
    }
}

// Function to handle password reset
function resetPassword($conn, $username, $new_password) {
    if (executeQuery($conn, "UPDATE accounts SET password='$new_password' WHERE username='$username'")) {
        echo "Password reset successful!";
    }
}

// Function to handle account deletion
function deleteAccount($conn, $username, $password) {
    $result = $conn->query("SELECT * FROM accounts WHERE username='$username' AND password='$password'");
    if ($result->num_rows > 0) {
        if (executeQuery($conn, "DELETE FROM accounts WHERE username='$username'")) {
            echo "Account deleted successfully!";
        }
    } else {
        echo "Invalid username or password.";
    }
}

// Check if POST request and perform corresponding action
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = connectToDatabase();
    
    if (isset($_POST["register"])) {
        registerUser($conn, $_POST["username"], $_POST["email"], $_POST["password"]);
    } elseif (isset($_POST["login"])) {
        loginUser($conn, $_POST["username"], $_POST["password"]);
    } elseif (isset($_POST["forgot"])) {
        forgotPassword($conn, $_POST["username"], $_POST["email"]);
    } elseif (isset($_POST["reset"])) {
        resetPassword($conn, $_POST["username"], $_POST["new_password"]);
    } elseif (isset($_POST["delete"])) {
        deleteAccount($conn, $_POST["username"], $_POST["password"]);
    }
    
    // Close database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Management System</title>
</head>
<body>
<h2>User Management</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        Username: <input type="text" name="username"><br>
        Email: <input type="email" name="email"><br>
        Password: <input type="password" name="password"><br>

        <!-- Different buttons for different actions -->
        <input type="submit" name="register" value="Register">
        <input type="submit" name="login" value="Login">
        <input type="submit" name="forgot" value="Forgot Password">
        <input type="submit" name="delete" value="Delete Account">
    </form>
</body>
</html>
