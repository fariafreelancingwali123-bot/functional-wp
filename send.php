<!-- File: send.php -->
<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$db = "dbfuvgl4xdhglp";
$conn = new mysqli($host, $user, $pass, $db);

$sender = $_SESSION['user_id'];
$receiver = $_POST['receiver_id'];
$message = $_POST['message'];

$stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $sender, $receiver, $message);
$stmt->execute();
?>
