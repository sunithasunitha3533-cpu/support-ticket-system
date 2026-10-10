
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../support_ticket_system/db.php";

$user_id = (int) $_SESSION["user_id"];

// Fetch notifications for the logged-in user
$sql = "SELECT id, ticket_id, message, is_read, created_at
        FROM notifications
        WHERE user_id = ?
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database query error: " . htmlspecialchars($conn->error));
}

$stmt->bind_param("i", $user_id);
$stmt->execute();

$notifications = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Notifications</title>
    <link rel="stylesheet" href="../static/css/style.css">
</head>
<body>

    <h2>My Notifications</h2>

    <p>
        Logged-in User ID:
        <?php echo $user_id; ?>
    </p>

    <?php if ($notifications->num_rows > 0): ?>

        <?php while ($notification = $notifications->fetch_assoc()): ?>

            <div class="notification-card">

                <p>
                    <strong>
                        <?php
                        echo htmlspecialchars($notification["message"]);
                        ?>
                    </strong>
                </p>

                <p>
                    <strong>Ticket ID:</strong>
                    <?php echo (int) $notification["ticket_id"]; ?>
                </p>

                <p>
                    <strong>Date:</strong>
                    <?php
                    echo htmlspecialchars($notification["created_at"]);
                    ?>
                </p>

                <?php if ((int) $notification["is_read"] === 1): ?>
                    <p>✅ Read</p>
                <?php else: ?>
                    <p>🔔 New notification</p>
                <?php endif; ?>

                <a href="ticket_details.php?id=<?php
                    echo (int) $notification["ticket_id"];
                ?>">
                    View Ticket
                </a>

            </div>

            <hr>

        <?php endwhile; ?>

    <?php else: ?>

        <p>No notifications available.</p>

    <?php endif; ?>

    <br>

    <a href="dashboard.php">Back to Dashboard</a>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>