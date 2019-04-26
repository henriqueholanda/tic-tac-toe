<?php
namespace App\Model\Move;

use App\Entity\Position;
use App\Exception\InvalidMoveException;
use App\Model\Randomizer;

class BotMove implements MoveInterface
{
    /** @var Randomizer */
    private $randomizer;

    public function __construct(Randomizer $randomizer)
    {
        $this->randomizer = $randomizer;
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
     * @throws InvalidMoveException
     * @return array
     */
    public function makeMove(array $boardState, string $playerUnit = 'X'): array
    {
        $position = $this->getAvailablePosition($boardState);
        return [
            $position->getRow(),
            $position->getColumn(),
            $playerUnit
        ];
    }

    /**
     * @param array
     *
     * @throws InvalidMoveException
     * @return Position
     */
    private function getAvailablePosition(array $boardState) : Position
    {
        $availablePositions = $this->getAvailableBoardPositions($boardState);

        if (count($availablePositions) < 1) {
            throw new InvalidMoveException('Bot can\'t make a movement.');
        }

        $randomRow = $this->randomizer->randomizeIndex(array_keys($availablePositions));
        $randomColumn = $this->randomizer->randomizeValue(array_values($availablePositions[$randomRow]));

        return new Position($randomRow, $randomColumn);
    }

    /**
     * @param array
     *
     * @return array
     */
    private function getAvailableBoardPositions(array $boardState) : array
    {
        $available = [];
        foreach ($boardState as $rowNumber => $row) {
            foreach ($row as $columnNumber => $column) {
                if (empty($column)) {
                    $available[$rowNumber][] = $columnNumber;
                }
            }
        }
        return $available;
    }
}
