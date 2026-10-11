<?php

session_start();

require_once __DIR__ . "/../support_ticket_system/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($name) || empty($email) || empty($password)) {

        $message = "Please fill all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email.";

    } elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters.";

    } else {

        $check = $conn->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $check->bind_param("s", $email);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "Email already registered.";

        } else {

            $password_hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO users
                (name, email, password_hash, role)
                VALUES (?, ?, ?, 'user')"
            );

            $stmt->bind_param(
                "sss",
                $name,
                $email,
                $password_hash
            );

            if ($stmt->execute()) {

                $message = "Registration successful. You can login now.";

            } else {

                $message = "Registration failed.";

            }

            $stmt->close();
        }

        $check->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Register - Support Ticket System</title>

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800&display=swap"
          rel="stylesheet">

    <link rel="stylesheet"
          href="../static/css/style.css">

</head>

<body>

    <h2>Support Ticket System</h2>

    <h3>Create Account</h3>

    <?php if (!empty($message)): ?>

        <p>
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>

    <form method="POST">

        <label>Name</label><br>

        <input
            type="text"
            name="name"
            required
        >

        <br><br>

        <label>Email</label><br>

        <input
            type="email"
            name="email"
            required
        >

        <br><br>

        <label>Password</label><br>

        <input
            type="password"
            name="password"
            minlength="6"
            required
        >

        <br><br>

        <button type="submit">
            Register
        </button>

    </form>

    <p>

        Already have an account?

        <a href="login.php">
            Login
        </a>

    </p>

</body>

</html>
