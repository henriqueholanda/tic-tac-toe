<?php
namespace App\Domain\Move;

use App\Domain\Position;
use App\Exception\InvalidMoveException;
use App\Domain\Move\Strategy\MoveToBlock;
use App\Domain\Move\Strategy\MoveToWin;

class BotMove implements MoveInterface
{
    /** @var Randomizer */
    private $randomizer;

    public function __construct()
    {
        $this->randomizer = new Randomizer();
    }

    /**
     * Makes a move using the actual game board state, against the player.
     *
     * $boardState contains a 2D array of the 3x3 board with the 3 possible values:
     * - X and O represents the player or the bot, as defined by $playerUnit
     * - empty string means that the field is not yet taken
     * Example:
     * [['X', 'O', '']
     * ['X', 'O', 'O']
     * [ '', '', '']]
     *
     * Returns an array containing X and Y coordinates for the next move
     * and the unit that should occupy it.
     * Example: [2, 0, 'O'] - upper right corner with O unit
     *
     * @param array $boardState
     * @param string $playerUnit
     *
     * @return array
     */
    public function makeMove(array $boardState, string $playerUnit = 'X'): array
    {
        $availablePositions = $this->getAvailableBoardPositions($boardState);

        if (count($availablePositions) < 1) {
            throw new InvalidMoveException('Bot can\'t make a movement.');
        }

        $winGame = (new MoveToWin($boardState, $playerUnit))->move();
        if ($winGame instanceof Position) {
            return [
                $winGame->getRow(),
                $winGame->getColumn(),
                $playerUnit
            ];
        }

        $blockHuman = (new MoveToBlock($boardState, $playerUnit))->move();
        if ($blockHuman instanceof Position) {
            return [
                $blockHuman->getRow(),
                $blockHuman->getColumn(),
                $playerUnit
            ];
        }

        $randPosition = $this->randomizer->randomizeIntInterval(0, count($availablePositions)-1);
        $move = $availablePositions[$randPosition];
        return [
            key($move),
            current($move),
            $playerUnit
        ];
    }

    /**
     * @param array $boardState
     *
     * @return array
     */
    private function getAvailableBoardPositions(array $boardState) : array
    {
        $available = [];
        foreach ($boardState as $rowNumber => $row) {
            foreach ($row as $columnNumber => $column) {
                if (empty($column)) {
                    $available[][$rowNumber] = $columnNumber;
                }
            }
        }
        return $available;
    }
}
