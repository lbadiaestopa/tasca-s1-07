<?php

function calculator(float $num1, float $num2, string $operator): float
{

    if ($num2 == 0.0) {
        throw new DivisionByZeroError("Error: Division by zero!");
    }

    switch ($operator) {
        case '+':
            return $num1 + $num2;
        case '-':
            return $num1 - $num2;
        case '*':
            return $num1 * $num2;
        case '/':
            return $num1 / $num2;
        default:
            throw new InvalidArgumentException("Invalid operator");
    }
}

try {
    $result1 = calculator(6, 0, "/");
    echo "Resultat 1: $result1<br>";
} catch (DivisionByZeroError $e) {
    echo "Error 1: " . $e->getMessage() . "\n";
} catch (InvalidArgumentException $e) {
    echo "Error 1: " . $e->getMessage();
}

try {
    $result2 = calculator(5, 3, "@");
    echo "Resultat 2: $result2<br>";
} catch (DivisionByZeroError $e) {
    echo "Error 2: " . $e->getMessage();
} catch (InvalidArgumentException $e) {
    echo "Error 2: " . $e->getMessage();
}
