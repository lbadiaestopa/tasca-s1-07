<?php

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