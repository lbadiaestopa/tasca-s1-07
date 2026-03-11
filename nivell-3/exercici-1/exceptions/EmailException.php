<?php

class EmailException extends ValidationException
{
    public function __construct(Rule $rule, ?Throwable $previous = null)
    {
        parent::__construct(Field::EMAIL, $rule, $previous);
    }

    protected function buildMessage(Field $field, Rule $rule): string
    {
        return match ($rule) {
            Rule::MISSING        => 'Email field does not exist.',
            Rule::EMPTY          => 'Email cannot be empty.',
            Rule::HAS_SPACE      => 'Email cannot contain spaces.',
            Rule::INVALID_FORMAT => 'Invalid email format.',
            default              => 'Invalid email.',
        };
    }
}