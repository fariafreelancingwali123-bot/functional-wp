<?php
session_start();
$conn = new mysqli("localhost", "u1fkgwiwpmjub", "mp8cjl5322br", "dbfuvgl4xdhglp");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, number FROM users WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password, $number);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_number'] = $number;
            header("Location: chat.php");
            exit();
        } else {
            $error = "Wrong password!";
        }
    } else {
        $error = "No user found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { font-family: Arial; background: #f0f0f0; padding: 50px; }
        form { background: white; padding: 20px; border-radius: 10px; max-width: 300px; margin: auto; }
        input, button { width: 100%; padding: 10px; margin-top: 10px; }
        button { background-color: #007BFF; color: white; border: none; }
    </style>
</head>
<body>
    <form method="post">
        <h2>Login</h2>
        <input type="text" name="name" placeholder="Your Name" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit">Login</button>
        <p>Don't have an account? <a href="signup.php">Signup here</a></p>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    </form>
</body>
</html>
