<?php
session_start();

require_once __DIR__ . "/../support_ticket_system/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $subject = trim($_POST["subject"] ?? "");
    $category = $_POST["category"] ?? "";
    $priority = $_POST["priority"] ?? "";
    $ticket_message = trim($_POST["message"] ?? "");
    $user_id = (int) $_SESSION["user_id"];

    $valid_categories = ["Technical", "Account", "Other"];
    $valid_priorities = ["Low", "Medium", "High"];

    if (
        $subject === "" ||
        $category === "" ||
        $priority === "" ||
        $ticket_message === ""
    ) {
        $message = "Please fill all fields.";
    } elseif (
        !in_array($category, $valid_categories, true) ||
        !in_array($priority, $valid_priorities, true)
    ) {
        $message = "Please select a valid category and priority.";
    } else {

        $stmt = $conn->prepare(
            "INSERT INTO tickets
            (user_id, subject, category, priority, message)
            VALUES (?, ?, ?, ?, ?)"
        );

        if ($stmt) {
            $stmt->bind_param(
                "issss",
                $user_id,
                $subject,
                $category,
                $priority,
                $ticket_message
            );

            if ($stmt->execute()) {
                $message = "Ticket created successfully!";
            } else {
                $message = "Ticket creation failed. Please try again.";
            }

            $stmt->close();
        } else {
            $message = "Database query failed. Please check the database.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Ticket - Support Ticket System</title>

    <link rel="stylesheet" href="../static/css/style.css">
</head>

<body>

    <h2>Create Support Ticket</h2>

    <?php if ($message !== ""): ?>
        <p>
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">

        <label>Subject</label><br>
        <input type="text" name="subject" required>

        <br><br>

        <label>Category</label><br>
        <select name="category" required>
            <option value="">Select Category</option>
            <option value="Technical">Technical</option>
            <option value="Account">Account</option>
            <option value="Other">Other</option>
        </select>

        <br><br>

        <label>Priority</label><br>
        <select name="priority" required>
            <option value="">Select Priority</option>
            <option value="Low">Low</option>
            <option value="Medium">Medium</option>
            <option value="High">High</option>
        </select>

        <br><br>

        <label>Message</label><br>
        <textarea name="message" rows="6" required></textarea>

        <br><br>

        <label>Attachment</label><br>
        <input
            type="file"
            name="attachment"
            accept=".jpg,.jpeg,.png,.pdf"
        >

        <br><br>

        <button type="submit">Submit Ticket</button>

    </form>

    <br>

    <a href="dashboard.php">Back to Dashboard</a>

</body>

</html>