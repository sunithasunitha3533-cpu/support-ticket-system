<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../support_ticket_system/db.php";

$user_id = (int) $_SESSION["user_id"];

// Count unread notifications
$stmt = $conn->prepare(
    "SELECT COUNT(*) AS unread_count
     FROM notifications
     WHERE user_id = ? AND is_read = 0"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$unread_count = (int) $row["unread_count"];
$stmt->close();

// Get user's tickets
$stmt = $conn->prepare(
    "SELECT id, subject, category, priority, status, created_at
     FROM tickets
     WHERE user_id = ?
     ORDER BY created_at DESC"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$tickets = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Support Ticket System</title>
    <link rel="stylesheet" href="../static/css/style.css">
</head>
<body>

    <h2>
        Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?>!
    </h2>

    <p>This is your Support Ticket Dashboard.</p>

    <p>
        <a href="create_ticket.php">Create New Ticket</a>
    </p>

    <p>
        <a href="notifications.php">
            Notifications 🔔 (<?php echo $unread_count; ?>)
        </a>
    </p>

    <h3>My Tickets</h3>

    <?php if ($tickets->num_rows > 0): ?>

        <table border="1" cellpadding="10">
            <tr>
                <th>Ticket ID</th>
                <th>Subject</th>
                <th>Category</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>

            <?php while ($ticket = $tickets->fetch_assoc()): ?>
                <tr>
                    <td><?php echo (int) $ticket["id"]; ?></td>

                    <td>
                        <?php echo htmlspecialchars($ticket["subject"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($ticket["category"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($ticket["priority"]); ?>
                    </td>

                    <td>
                        <span class="status-badge <?php echo strtolower(str_replace(' ', '-', $ticket["status"])); ?>">
                            <?php echo htmlspecialchars($ticket["status"]); ?>
                        </span>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($ticket["created_at"]); ?>
                    </td>

                    <td>
                        <a href="ticket_details.php?id=<?php echo (int) $ticket["id"]; ?>">
                            View Details
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>

        </table>

    <?php else: ?>

        <p>No tickets found. Create your first ticket!</p>

    <?php endif; ?>

    <br>

    <p><a href="logout.php">Logout</a></p>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>