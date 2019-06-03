<?php
namespace App\Domain\Move;

class Randomizer
{
    /**
     * @param int $start
     * @param int $end
     *
     * @return int
     */
    public function randomizeIntInterval(int $start, int $end) : int
    {
        return rand($start, $end);
    }
}
