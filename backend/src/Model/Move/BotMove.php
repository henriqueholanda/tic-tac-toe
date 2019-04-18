<?php
namespace App\Model\Move;

use App\Exception\InvalidMoveException;

class BotMove implements MoveInterface
{
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
     * @throws InvalidMoveException
     * @return array
     */
    public function makeMove(array $boardState, string $playerUnit = 'X'): array
    {
        foreach ($boardState as $rowNumber => $row) {
            foreach ($row as $columnNumber => $column) {
                if (empty($column)) {
                    return [$rowNumber, $columnNumber, $playerUnit];
                }
            }
        }

        throw new InvalidMoveException('Bot can\'t make a movement.');
    }
}
