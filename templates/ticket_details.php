<?php
session_start();

require_once __DIR__ . "/../support_ticket_system/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$ticket_id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
$user_id = (int) $_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT id, subject, category, priority, message, status, created_at
     FROM tickets
     WHERE id = ? AND user_id = ?"
);

$stmt->bind_param("ii", $ticket_id, $user_id);
$stmt->execute();

$result = $stmt->get_result();
$ticket = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ticket Details</title>
</head>
<body>

<h2>Ticket Details</h2>

<?php if ($ticket): ?>

    <p><strong>Ticket ID:</strong>
        <?= htmlspecialchars((string)$ticket["id"]) ?></p>

    <p><strong>Subject:</strong>
        <?= htmlspecialchars($ticket["subject"]) ?></p>

    <p><strong>Category:</strong>
        <?= htmlspecialchars($ticket["category"]) ?></p>

    <p><strong>Priority:</strong>
        <?= htmlspecialchars($ticket["priority"]) ?></p>

    <p><strong>Message:</strong>
        <?= nl2br(htmlspecialchars($ticket["message"])) ?></p>

    <p><strong>Status:</strong>
        <?= htmlspecialchars($ticket["status"]) ?></p>

    <p><strong>Created At:</strong>
        <?= htmlspecialchars($ticket["created_at"]) ?></p>

<?php else: ?>

    <p>Ticket not found.</p>

<?php endif; ?>

<br>
<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
