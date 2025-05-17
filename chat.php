<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$userNumber = $_SESSION['user_number'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>WhatsApp Clone</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #e5ddd5;
        }
        .header {
            background-color: #075e54;
            color: white;
            padding: 15px;
            font-size: 20px;
            text-align: center;
        }
        .container {
            display: flex;
            height: 90vh;
        }
        .sidebar {
            width: 30%;
            background: #ffffff;
            border-right: 1px solid #ccc;
            padding: 10px;
            box-sizing: border-box;
        }
        .chatbox {
            width: 70%;
            background: #f0f0f0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .chat-messages {
            padding: 15px;
            overflow-y: scroll;
            flex: 1;
        }
        .chat-input {
            display: flex;
            padding: 10px;
            background: #ffffff;
        }
        .chat-input input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 25px;
            margin-right: 10px;
        }
        .chat-input button {
            padding: 10px 20px;
            background: #25D366;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
        }
        .number-box {
            margin: 10px;
            padding: 10px;
            background: #dcf8c6;
            border-radius: 10px;
            font-weight: bold;
        }
        .search-bar input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 20px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>

<div class="header">Your Number: <?php echo $userNumber; ?></div>

<div class="container">
    <div class="sidebar">
        <div class="search-bar">
            <form method="GET" action="">
                <input type="text" name="search_number" placeholder="Search by number" />
            </form>
        </div>

        <?php
        // Connect to DB
        $conn = new mysqli("localhost", "u1fkgwiwpmjub", "mp8cjl5322br", "dbfuvgl4xdhglp");
        if ($conn->connect_error) die("DB Error");

        if (isset($_GET['search_number'])) {
            $search = $conn->real_escape_string($_GET['search_number']);
            $result = $conn->query("SELECT * FROM users WHERE number = '$search' AND id != '$userId'");
            if ($result->num_rows > 0) {
                $contact = $result->fetch_assoc();
                $_SESSION['chat_with'] = $contact['id'];
                $_SESSION['chat_number'] = $contact['number'];
            } else {
                echo "<p>No user found</p>";
            }
        }

        if (isset($_SESSION['chat_number'])) {
            echo "<div class='number-box'>Chatting with: " . $_SESSION['chat_number'] . "</div>";
        }
        ?>
    </div>

    <div class="chatbox">
        <div class="chat-messages">
            <?php
            if (isset($_SESSION['chat_with'])) {
                $chatWith = $_SESSION['chat_with'];

                $msgs = $conn->query("SELECT * FROM messages WHERE 
                    (sender_id = '$userId' AND receiver_id = '$chatWith') OR 
                    (sender_id = '$chatWith' AND receiver_id = '$userId')
                    ORDER BY created_at ASC");

                while ($msg = $msgs->fetch_assoc()) {
                    $align = ($msg['sender_id'] == $userId) ? "right" : "left";
                    echo "<div style='text-align:$align; margin: 5px 0;'><span style='background:#dcf8c6; padding:8px 15px; border-radius:15px; display:inline-block; max-width:60%;'>" . htmlspecialchars($msg['message']) . "</span></div>";
                }
            } else {
                echo "<p style='text-align:center;'>No conversation selected</p>";
            }
            ?>
        </div>

        <?php if (isset($_SESSION['chat_with'])) { ?>
        <form class="chat-input" method="POST" action="">
            <input type="text" name="message" placeholder="Type a message" required />
            <button type="submit" name="send">Send</button>
        </form>
        <?php } ?>
    </div>
</div>

<?php
if (isset($_POST['send']) && isset($_SESSION['chat_with'])) {
    $msg = $conn->real_escape_string($_POST['message']);
    $chatWith = $_SESSION['chat_with'];
    $conn->query("INSERT INTO messages (sender_id, receiver_id, message) VALUES ('$userId', '$chatWith', '$msg')");
    header("Location: chat.php");
    exit();
}
$conn->close();
?>

</body>
</html>
