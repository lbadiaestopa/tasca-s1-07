<?php

class PasswordException extends ValidationException
{
    public function __construct(Rule $rule, ?Throwable $previous = null)
    {
        parent::__construct(Field::PASS, $rule, $previous);
    }

    protected function buildMessage(Field $field, Rule $rule): string
    {
        return match ($rule) {
            Rule::MISSING   => 'Password field does not exist.',
            Rule::EMPTY     => 'Password cannot be empty.',
            Rule::TOO_SHORT => 'Password must be at least 8 characters.',
            Rule::HAS_SPACE => 'Password cannot contain spaces.',
            Rule::NO_LETTER => 'Password must contain at least one letter.',
            Rule::NO_NUMBER => 'Password must contain at least one number.',
            default         => 'Invalid password.',
        };
    }
}