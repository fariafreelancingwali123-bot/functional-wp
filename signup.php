<?php
session_start();

// Allow access to signup page even if logged in
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $number = $_POST['number'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $conn = new mysqli("localhost", "u1fkgwiwpmjub", "mp8cjl5322br", "dbfuvgl4xdhglp");

    if ($conn->connect_error) {
        die("Connection failed");
    }

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (name, number, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $number, $password);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['user_number'] = $number;
        header("Location: login.php"); // go to login after signup
        exit();
    } else {
        echo "Signup failed: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
</head>
<body>
    <h2>Signup</h2>
    <form method="POST">
        <input type="text" name="name" placeholder="Name" required /><br><br>
        <input type="text" name="number" placeholder="Number" required /><br><br>
        <input type="password" name="password" placeholder="Password" required /><br><br>
        <button type="submit">Signup</button>
    </form>
</body>
</html>
