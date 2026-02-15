<?php

namespace WitchBoy666\GCD\View;

use function cli\line;

function showWelcome(): void
{
    line("Welcome to the GCD Game!");
}

function showQuestion(int $a, int $b): void
{
    line("Find the greatest common divisor of %d and %d.", $a, $b);
}

function showResult(bool $isCorrect, int $correct): void
{
    if ($isCorrect) {
        line("Correct!");
    } else {
        line("Wrong answer. Correct answer: %d", $correct);
    }
}
