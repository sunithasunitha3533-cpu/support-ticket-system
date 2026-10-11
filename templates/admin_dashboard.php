<?php
session_start();

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../support_ticket_system/db.php";

// Get ticket statistics
$stats_sql = "SELECT
    COUNT(*) AS total,
    COALESCE(SUM(status = 'Open'), 0) AS open_count,
    COALESCE(SUM(status = 'In Progress'), 0) AS progress_count,
    COALESCE(SUM(status IN ('Closed', 'Resolved')), 0) AS closed_count
    FROM tickets";

$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();

// Get all tickets
$sql = "SELECT tickets.id, tickets.subject, tickets.category,
               tickets.priority, tickets.status, tickets.created_at,
               users.name
        FROM tickets
        JOIN users ON tickets.user_id = users.id
        ORDER BY tickets.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Support Ticket System</title>
    <link rel="stylesheet" href="../static/css/style.css">
</head>
<body>

    <h2>Admin Dashboard</h2>

    <p>
        Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?>!
    </p>

    <h3>Ticket Statistics</h3>

    <div class="stats-container">

        <div class="stats-card">
            <h3>Total Tickets</h3>
            <p><?php echo (int)($stats["total"] ?? 0); ?></p>
        </div>

        <div class="stats-card">
            <h3>Open</h3>
            <p><?php echo (int)($stats["open_count"] ?? 0); ?></p>
        </div>

        <div class="stats-card">
            <h3>In Progress</h3>
            <p><?php echo (int)($stats["progress_count"] ?? 0); ?></p>
        </div>

        <div class="stats-card">
            <h3>Closed</h3>
            <p><?php echo (int)($stats["closed_count"] ?? 0); ?></p>
        </div>

    </div>

    <h3>All Tickets</h3>

    <?php if ($result && $result->num_rows > 0): ?>

        <table border="1" cellpadding="10">
            <tr>
                <th>Ticket ID</th>
                <th>User</th>
                <th>Subject</th>
                <th>Category</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>

            <?php while ($ticket = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo (int)$ticket["id"]; ?></td>

                    <td>
                        <?php echo htmlspecialchars($ticket["name"]); ?>
                    </td>

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
                        <a href="ticket_details.php?id=<?php echo (int)$ticket["id"]; ?>">
                            View Details
                        </a>
                        |
                        <a href="reply.php?id=<?php echo (int)$ticket["id"]; ?>">
                            Reply
                        </a>
                        |
                        <a href="update_status.php?id=<?php echo (int)$ticket["id"]; ?>">
                            Update Status
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>

        </table>

    <?php else: ?>

        <p>No tickets found.</p>

    <?php endif; ?>

    <br>

    <p><a href="logout.php">Logout</a></p>

</body>
</html>

<?php
$conn->close();
?>
