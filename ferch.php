<!-- File: fetch.php -->
<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$db = "dbfuvgl4xdhglp";
$conn = new mysqli($host, $user, $pass, $db);

$current_user = $_SESSION['user_id'];
$receiver = $_POST['receiver_id'];

$sql = "SELECT * FROM messages WHERE 
  (sender_id = $current_user AND receiver_id = $receiver)
  OR
  (sender_id = $receiver AND receiver_id = $current_user)
  ORDER BY created_at ASC";

$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
  $cls = $row['sender_id'] == $current_user ? 'sent' : 'received';
  echo "<div class='msg $cls'>" . htmlspecialchars($row['message']) . "</div>";
}
?>
