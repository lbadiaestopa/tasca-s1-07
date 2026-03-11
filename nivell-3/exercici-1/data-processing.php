<?php
session_start();

require_once __DIR__ . '/exceptions/ValidationException.php';
require_once __DIR__ . '/exceptions/NameException.php';
require_once __DIR__ . '/exceptions/AgeException.php';
require_once __DIR__ . '/exceptions/PasswordException.php';
require_once __DIR__ . '/exceptions/EmailException.php';
require_once __DIR__ . '/exceptions/PhoneException.php';


enum Field: string
{
    case NAME  = 'name';
    case AGE   = 'age';
    case PASS  = 'pass';
    case EMAIL = 'email';
    case PHONE = 'phone';
}

enum Rule: string
{
    case MISSING          = 'missing';
    case EMPTY            = 'empty';

    case OUT_LIMIT        = 'out_limit';

    case TOO_SHORT        = 'too_short';
    case HAS_SPACE        = 'has_space';
    case NO_LETTER        = 'no_letter';
    case NO_NUMBER        = 'no_number';

    case INVALID_FORMAT   = 'invalid-format';

    case INCORRECT_NUMBER = 'incorrect_number';
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

    if($fieldName === "age" || $fieldName === "phone") {
        $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    return $value;
}

function validateAge(string $rawAge): int
{
    $age = filter_var($rawAge, FILTER_VALIDATE_INT, ['options' => ['min_range'=>0,'max_range'=>120]]);
    if ($age === false) {
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

function validateEmail(string $email): string
{
    if (strpos($email, ' ') !== false) {
        throw new EmailException(Rule::HAS_SPACE);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new EmailException(Rule::INVALID_FORMAT);
    }

    return $email;
}

function validatePhone(string $phone): string
{
    $phone = filter_var($phone, FILTER_SANITIZE_NUMBER_INT);

    if (strlen($phone) !== 9) {
        throw new PhoneException(Rule::INCORRECT_NUMBER);
    }

    return $phone;
}


try {
    $name = validateRequiredField($_POST, Field::NAME->value);
    $_SESSION['name'] = $name;

    $rawAge = validateRequiredField($_POST, Field::AGE->value);
    $age = validateAge($rawAge);
    $_SESSION['age'] = $age;

    $password = validateRequiredField($_POST, Field::PASS->value);
    $_SESSION['pass'] = validatePassword($password);

    $email = validateRequiredField($_POST, Field::EMAIL->value);
    $email = validateEmail($email);
    $_SESSION['email'] = $email;

    $phone = validateRequiredField($_POST, Field::PHONE->value);
    $phone = validatePhone($phone);
    $_SESSION['phone'] = $phone;    

} catch (NameException | AgeException | PasswordException | EmailException | PhoneException $e) {
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
            <p>Hola, <?= htmlspecialchars($name) ?>! Tens <?= $age ?> anys, la teva contrassenya és <?= $password ?>, 
            el teu correu és <?= htmlspecialchars($email) ?>, i el teu telèfon és <?= $phone ?>.</p>
        <?php endif; ?>

    </div>
</body>

</html>