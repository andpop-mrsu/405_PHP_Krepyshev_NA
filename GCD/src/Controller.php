<?php

namespace WitchBoy666\GCD\Controller;

use function WitchBoy666\GCD\View\showWelcome;
use function WitchBoy666\GCD\View\showQuestion;
use function WitchBoy666\GCD\View\showResult;
use function cli\prompt;
use function cli\line;

function gcd(int $a, int $b): int
{
    while ($b !== 0) {
        $temp = $b;
        $b = $a % $b;
        $a = $temp;
    }
    return $a;
}

function startGame(): void
{
    showWelcome();

    $name = prompt("May I have your name?");
    line("Hello, %s!", $name);

    $a = rand(1, 100);
    $b = rand(1, 100);

    showQuestion($a, $b);

    $answer = prompt("Your answer");
    $correct = gcd($a, $b);

    if ((int)$answer === $correct) {
        showResult(true, $correct);
    } else {
        showResult(false, $correct);
    }
}
