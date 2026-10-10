<?php
session_start();

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../support_ticket_system/db.php";

$message = "";
$ticket_id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($ticket_id <= 0) {
    die("Invalid ticket ID. Please open this page from Admin Dashboard.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $status = $_POST["status"] ?? "";
    $allowed_statuses = ["Open", "In Progress", "Resolved"];

    if (!in_array($status, $allowed_statuses, true)) {
        $message = "Please select a valid status.";
    } else {
        $stmt = $conn->prepare(
            "UPDATE tickets SET status = ? WHERE id = ?"
        );

        $stmt->bind_param("si", $status, $ticket_id);

        if ($stmt->execute() && $stmt->affected_rows >= 0) {
            $message = "Ticket status updated successfully!";
        } else {
            $message = "Failed to update ticket status.";
        }

        $stmt->close();
    }
}

$stmt = $conn->prepare(
    "SELECT id, subject, status FROM tickets WHERE id = ?"
);
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$ticket = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$ticket) {
    die("Ticket not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Ticket Status</title>
    <link rel="stylesheet" href="../static/css/style.css">
</head>
<body>

    <h2>Update Ticket Status</h2>

    <p>
        <strong>Ticket ID:</strong>
        <?php echo (int)$ticket["id"]; ?>
    </p>

    <p>
        <strong>Subject:</strong>
        <?php echo htmlspecialchars($ticket["subject"]); ?>
    </p>

    <p>
        <strong>Current Status:</strong>
        <?php echo htmlspecialchars($ticket["status"]); ?>
    </p>

    <?php if ($message !== ""): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Status</label><br>

        <select name="status" required>
            <option value="">Select Status</option>
            <option value="Open">Open</option>
            <option value="In Progress">In Progress</option>
            <option value="Resolved">Resolved</option>
        </select>

        <br><br>
        <button type="submit">Update Status</button>
    </form>

    <br>
    <a href="admin_dashboard.php">Back to Admin Dashboard</a>

</body>
</html>

<?php
$conn->close();
?>