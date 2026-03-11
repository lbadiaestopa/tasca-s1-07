<?php
session_start();

require_once __DIR__ . '/exceptions/ValidationException.php';
require_once __DIR__ . '/exceptions/NameException.php';
require_once __DIR__ . '/exceptions/AgeException.php';
require_once __DIR__ . '/exceptions/PasswordException.php';

enum Field: string
{
    case NAME = 'name';
    case AGE  = 'age';
    case PASS = 'pass';
}

enum Rule: string
{
    case MISSING       = 'missing';
    case EMPTY         = 'empty';

    case HAS_LETTER    = 'has_letter';
    case OUT_LIMIT     = 'out_limit';

    case TOO_SHORT     = 'too_short';
    case CONTAINS_SPACE = 'contains_space';
    case NO_LETTER     = 'no_letter';
    case NO_NUMBER     = 'no_number';
}

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
        throw new AgeException(Rule::HAS_LETTER);
    }

    $age = (int) $rawAge;

    if ($age < 0 || $age > 120) {
        throw new AgeException(Rule::OUT_LIMIT);
    }

    return $age;
}

function validatePassword(string $password): string
{
    if (strlen($password) < 8) {
        throw new PasswordException(Rule::TOO_SHORT);
    }

    if (strpos($password, ' ') !== false) {
        throw new PasswordException(Rule::HAS_SPACE);
    }

    if (!preg_match('/[a-zA-Z]/', $password)) {
        throw new PasswordException(Rule::NO_LETTER);
    }

    if (!preg_match('/\d/', $password)) {
        throw new PasswordException(Rule::NO_NUMBER);
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
            <p>Hola, <?= htmlspecialchars($name) ?>! Tens <?= $age ?> anys i la teva contrasenya és <?= $password ?></p>
        <?php endif; ?>

    </div>
</body>

</html>