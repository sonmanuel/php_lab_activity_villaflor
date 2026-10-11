<?php
require_once "dbcon.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}

$statement = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$statement->bind_param("i", $id);
$statement->execute();
$user = $statement->get_result()->fetch_assoc();
$statement->close();

if (!$user) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");

    if ($name !== "" && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $statement = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $statement->bind_param("ssi", $name, $email, $id);
        $statement->execute();
        $statement->close();
        header("Location: index.php?msg=updated");
        exit;
    }

    $error = "Please enter a name and a valid email address.";
    $user["name"] = $name;
    $user["email"] = $email;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User | PHP Activity Villaflor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <h1 class="h2 mb-4">Edit User</h1>
            <section class="bg-white border rounded p-4">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="POST" action="edit.php?id=<?= $id ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required maxlength="255" value="<?= htmlspecialchars($user["name"]) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required maxlength="255" value="<?= htmlspecialchars($user["email"]) ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                </form>
            </section>
        </div>
    </div>
</main>
</body>
</html>