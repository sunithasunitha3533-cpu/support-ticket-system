<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../support_ticket_system/db.php";

$user_id = (int) $_SESSION["user_id"];
$role = $_SESSION["role"] ?? "user";
$ticket_id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
$message = "";

// Get ticket details
$stmt = $conn->prepare(
    "SELECT id, user_id, subject FROM tickets WHERE id = ?"
);
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$ticket = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$ticket) {
    die("Ticket not found. Open a valid ticket link.");
}

// Check permission
if ($role !== "admin" && (int)$ticket["user_id"] !== $user_id) {
    die("You are not allowed to access this ticket.");
}

// Send reply
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $reply_message = trim($_POST["reply_message"] ?? "");

    if ($reply_message === "") {
        $message = "Please enter a reply message.";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO replies (ticket_id, user_id, reply_message)
             VALUES (?, ?, ?)"
        );

        $stmt->bind_param("iis", $ticket_id, $user_id, $reply_message);

        if ($stmt->execute()) {
            $message = "Reply sent successfully!";

            // Notify ticket owner when admin replies
            if ($role === "admin") {
                $owner_id = (int)$ticket["user_id"];
                $notification_message =
                    "Admin replied to your ticket: " . $ticket["subject"];

                $notify = $conn->prepare(
                    "INSERT INTO notifications (user_id, ticket_id, message)
                     VALUES (?, ?, ?)"
                );

                if ($notify) {
                    $notify->bind_param(
                        "iis",
                        $owner_id,
                        $ticket_id,
                        $notification_message
                    );
                    $notify->execute();
                    $notify->close();
                }
            }
        } else {
            $message = "Unable to send reply. Please try again.";
        }

        $stmt->close();
    }
}

// Get previous replies
$stmt = $conn->prepare(
    "SELECT replies.reply_message, replies.created_at, users.name
     FROM replies
     JOIN users ON replies.user_id = users.id
     WHERE replies.ticket_id = ?
     ORDER BY replies.created_at ASC"
);

$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$replies = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply - Support Ticket System</title>
    <link rel="stylesheet" href="../static/css/style.css">
</head>
<body>

    <h2>Reply to Ticket</h2>

    <p><strong>Ticket ID:</strong>
        <?php echo (int)$ticket["id"]; ?>
    </p>

    <p><strong>Subject:</strong>
        <?php echo htmlspecialchars($ticket["subject"]); ?>
    </p>

    <?php if ($message !== ""): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Reply Message</label><br>
        <textarea name="reply_message" rows="6" required></textarea>
        <br><br>
        <button type="submit">Send Reply</button>
    </form>

    <h3>Previous Replies</h3>

    <?php if ($replies->num_rows > 0): ?>
        <?php while ($reply = $replies->fetch_assoc()): ?>
            <p>
                <strong><?php echo htmlspecialchars($reply["name"]); ?></strong>
                — <?php echo htmlspecialchars($reply["created_at"]); ?>
            </p>
            <p><?php echo nl2br(htmlspecialchars($reply["reply_message"])); ?></p>
            <hr>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No replies yet.</p>
    <?php endif; ?>

    <br>

    <?php if ($role === "admin"): ?>
        <a href="admin_dashboard.php">Back to Admin Dashboard</a>
    <?php else: ?>
        <a href="dashboard.php">Back to Dashboard</a>
    <?php endif; ?>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
