<?php

class PhoneException extends ValidationException
{
    public function __construct(Rule $rule, ?Throwable $previous = null)
    {
        parent::__construct(Field::PHONE, $rule, $previous);
    }

    protected function buildMessage(Field $field, Rule $rule): string
    {
        return match ($rule) {
            Rule::MISSING           => 'Phone field does not exist.',
            Rule::EMPTY             => 'Phone cannot be empty.',
            Rule::INCORRECT_NUMBER  => 'Phone must have 9 numbers.', 
            Rule::HAS_LETTER        => 'Phone cannot have letters.',
            default                 => 'Invalid phone number.',
        };
    }
}