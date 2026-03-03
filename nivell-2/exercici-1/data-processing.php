<?php
session_start();

enum Field: string
{
    case NAME     = 'name';
    case AGE      = 'age';
    case PASSWORD = 'password';
}

enum Rule: string
{
    case MISSING       = 'missing';
    case EMPTY         = 'empty';

        // Age rules
    case HAS_LETTER    = 'has_letter';
    case OUT_LIMIT     = 'out_limit';

        // Password rules
    case TOO_SHORT     = 'too_short';
    case CONTAINS_SPACE = 'contains_space';
    case NO_LETTER     = 'no_letter';
    case NO_NUMBER     = 'no_number';
}

abstract class ValidationException extends Exception
{
    protected const HTTP_STATUS = 422;

    protected Field $field;
    protected Rule  $rule;

    public function __construct(Field $field, Rule $rule, ?Throwable $previous = null)
    {
        $this->field = $field;
        $this->rule  = $rule;

        parent::__construct(
            $this->buildMessage($field, $rule),
            self::HTTP_STATUS,
            $previous
        );
    }

    abstract protected function buildMessage(Field $field, Rule $rule): string;

    public function getField(): Field
    {
        return $this->field;
    }

    public function getRule(): Rule
    {
        return $this->rule;
    }
}

class NameException extends ValidationException
{
    public function __construct(Rule $rule, ?Throwable $previous = null)
    {
        parent::__construct(Field::NAME, $rule, $previous);
    }

    protected function buildMessage(Field $field, Rule $rule): string
    {
        return match ($rule) {
            Rule::MISSING => 'Name field does not exist.',
            Rule::EMPTY   => 'Name cannot be empty.',
            default       => 'Invalid name.',
        };
    }
}

class AgeException extends ValidationException
{
    public function __construct(Rule $rule, ?Throwable $previous = null)
    {
        parent::__construct(Field::AGE, $rule, $previous);
    }

    protected function buildMessage(Field $field, Rule $rule): string
    {
        return match ($rule) {
            Rule::MISSING        => 'Age field does not exist.',
            Rule::EMPTY          => 'Age cannot be empty.',
            Rule::HAS_LETTER     => 'Age cannot contain letters.',
            Rule::OUT_LIMIT      => 'Age must be between 0 and 120.',
            default              => 'Invalid age.',
        };
    }
}

class PasswordException extends ValidationException
{
    public function __construct(Rule $rule, ?Throwable $previous = null)
    {
        parent::__construct(Field::PASSWORD, $rule, $previous);
    }

    protected function buildMessage(Field $field, Rule $rule): string
    {
        return match ($rule) {
            Rule::MISSING        => 'Password field does not exist.',
            Rule::EMPTY          => 'Password cannot be empty.',
            Rule::TOO_SHORT      => 'Password must be at least 8 characters.',
            Rule::CONTAINS_SPACE => 'Password cannot contain spaces.',
            Rule::NO_LETTER      => 'Password must contain at least one letter.',
            Rule::NO_NUMBER      => 'Password must contain at least one number.',
            default              => 'Invalid password.',
        };
    }
}


try {
    if (!isset($_POST['name'])) {
        throw new NameException(Rule::MISSING);
    }

    $name = trim($_POST['name']);

    if ($name === '') {
        throw new NameException(Rule::EMPTY);
    }

    $_SESSION['name'] = $name;

    if (!isset($_POST['age'])) {
        throw new AgeException(Rule::MISSING);
    }

    $rawAge = trim($_POST['age']);

    if ($rawAge === '') {
        throw new AgeException(Rule::EMPTY);
    }

    if (!ctype_digit($rawAge)) {
        throw new AgeException(Rule::HAS_LETTER);
    }

    $age = (int) $rawAge;

    if ($age < 0 || $age > 120) {
        throw new AgeException(Rule::OUT_LIMIT);
    }

    $_SESSION['age'] = $age;

    if (!isset($_POST['pass'])) {
        throw new PasswordException(Rule::MISSING);
    }

    $password = $_POST['pass'];

    if ($password === '') {
        throw new PasswordException(Rule::EMPTY);
    }

    if (strlen($password) < 8) {
        throw new PasswordException(Rule::TOO_SHORT);
    }

    if (strpos($password, ' ') !== false) {
        throw new PasswordException(Rule::CONTAINS_SPACE);
    }

    if (!preg_match('/[a-zA-Z]/', $password)) {
        throw new PasswordException(Rule::NO_LETTER);
    }

    if (!preg_match('/\d/', $password)) {
        throw new PasswordException(Rule::NO_NUMBER);
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