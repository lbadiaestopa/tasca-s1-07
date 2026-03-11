<?php

class AgeException extends ValidationException
{
    public function __construct(Rule $rule, ?Throwable $previous = null)
    {
        parent::__construct(Field::AGE, $rule, $previous);
    }

    protected function buildMessage(Field $field, Rule $rule): string
    {
        return match ($rule) {
            Rule::MISSING    => 'Age field does not exist.',
            Rule::EMPTY      => 'Age cannot be empty.',
            Rule::HAS_LETTER => 'Age cannot contain letters.',
            Rule::OUT_LIMIT  => 'Age must be between 0 and 120.',
            default          => 'Invalid age.',
        };
    }
}