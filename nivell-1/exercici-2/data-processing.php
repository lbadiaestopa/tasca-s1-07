<?php
session_start();

function validateRequiredField(array $source, string $fieldName): string
{
    if (!isset($source[$fieldName])) {
        throw new InvalidArgumentException("$fieldName field does not exist.");
    }

    $value = trim($source[$fieldName]);

    if ($value === '') {
        throw new InvalidArgumentException("$fieldName field cannot be empty.");
    }

    return $value;
}

function validateAge(string $rawAge): int
{
    if (!ctype_digit($rawAge)) {
        throw new InvalidArgumentException("Error: Age must be a number.");
    }

    $age = (int) $rawAge;

    if ($age < 0 || $age > 120) {
        throw new InvalidArgumentException("Error: Age must be between 0 and 120.");
    }

    return $age;
}

function validatePassword(string $password): string
{
    if (strlen($password) < 8) {
        throw new InvalidArgumentException("Error: Password must be at least 8 characters.");
    }

    if (strpos($password, ' ') !== false) {
        throw new InvalidArgumentException("Error: Password cannot contain spaces.");
    }

    if (!preg_match('/[a-zA-Z]/', $password)) {
        throw new InvalidArgumentException("Error: Password must contain at least one letter.");
    }

    if (!preg_match('/\d/', $password)) {
        throw new InvalidArgumentException("Error: Password must contain at least one number.");
    }

    return $password;
}

try {
    $name = validateRequiredField($_POST, "name");
    $_SESSION['name'] = $name;

    $rawAge = validateRequiredField($_POST, "age");
    $age = validateAge($rawAge);
    $_SESSION['age'] = $age;

    $password = validateRequiredField($_POST, "pass");
    $_SESSION['pass'] = validatePassword($password);
} catch (InvalidArgumentException $e) {
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