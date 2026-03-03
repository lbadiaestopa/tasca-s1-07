<?php
session_start();

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
        parent::__construct(Field::PASS, $rule, $previous);
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
            <p>Hola, <?= htmlspecialchars($name) ?>! Tens <?= $age ?> anys i la teva contrassenya és <?= $password ?></p>
        <?php endif; ?>

    </div>
</body>

</html>