<?php

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