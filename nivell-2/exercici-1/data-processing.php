<?php
session_start();

class NameException extends Exception {}
class AgeException extends Exception {}
class PasswordException extends Exception {}


try {
    if (!isset($_POST['name'])) {
        throw new NameException("Error: Name field does not exist.");
    }

    $name = trim($_POST['name']);

    if ($name === '') {
        throw new NameException("Error: Name field cannot be empty.");
    }

    $_SESSION['name'] = $name;

    if (!isset($_POST['age'])) {
        throw new AgeException("Error: Age field does not exist.");
    }

    $rawAge = trim($_POST['age']);

    if ($rawAge === '') {
        throw new AgeException("Error: Age field cannot be empty.");
    }

    if (!is_numeric($rawAge)) {
        throw new AgeException("Error: Age must be a number.");
    }

    $age = (int) $rawAge;

    if ($age < 0 || $age > 120) {
        throw new AgeException("Error: Age must be between 0 and 120.");
    }

    $_SESSION['age'] = $age;

    if (!isset($_POST['pass'])) {
        throw new AgeException("Error: Password field does not exist.");
    }

    $password = $_POST['pass'];

    if ($password === '') {
        throw new PasswordException("Error: Password cannot be empty.");
    }

    if (strlen($password) < 8) {
        throw new PasswordException("Error: Password must be at least 8 characters.");
    }

    if (strpos($password, ' ') !== false) {
        throw new NameException("Error: Password cannot contain spaces.");
    }

    if (!preg_match('/[a-zA-Z]/', $password)) {
        throw new PasswordException("Error: Password must contain at least one letter.");
    }

    if (!preg_match('/\d/', $password)) {
        throw new PasswordException("Error: Password must contain at least one number.");
    }

    $_SESSION['pass'] = $password;
    
} catch (NameException | AgeException | PasswordException $e) {
    $error = $e->getMessage();
}

?>

<html>
<link rel="stylesheet" href="css/styles.css">

<body>
    <div class="container">

        <?php if (isset($error)) : ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php else : ?>
            <h1>Dades rebudes</h1>
            <p>Hola, <?= htmlspecialchars($name) ?>! Tens <?= $age ?> anys i la teva contrassenya és <?= $password ?></p>
        <?php endif; ?>

    </div>
</body>

</html>