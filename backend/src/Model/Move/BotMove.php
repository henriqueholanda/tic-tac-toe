<?php
namespace App\Model\Move;

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
     * @return array
     */
    public function makeMove(array $boardState, string $playerUnit = 'X'): array
    {
        $availablePositions = $this->getAvailableBoardPositions($boardState);

        if (count($availablePositions) < 1) {
            throw new InvalidMoveException('Bot can\'t make a movement.');
        }

        $winGame= $this->moveToWinGame($boardState, $playerUnit);
        if (count($winGame) > 0) {
            return [
                $winGame[0],
                $winGame[1],
                $playerUnit
            ];
        }

        $blockHuman = $this->moveToBlockHuman($boardState, $playerUnit);
        if (count($blockHuman) > 0) {
            return [
                $blockHuman[0],
                $blockHuman[1],
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
     * @param string $playerUnit
     *
     * @return array
     */
    private function moveToBlockHuman(array $boardState, string $playerUnit) : array
    {
        $block = $this->getBlockPositionOnRow($boardState, $playerUnit);
        if (count($block) > 0) {
            return $block;
        }

        $block = $this->getBlockPositionOnColumn($boardState, $playerUnit);
        if (count($block) > 0) {
            return $block;
        }

        $block = $this->getBlockPositionOnDiagonals($boardState, $playerUnit);
        if (count($block) > 0) {
            return $block;
        }

        return [];
    }

    /**
     * @param array $boardState
     * @param string $playerUnit
     *
     * @return array
     */
    private function moveToWinGame(array $boardState, string $playerUnit) : array
    {
        $block = $this->getWinPositionOnRow($boardState, $playerUnit);
        if (count($block) > 0) {
            return $block;
        }

        $block = $this->getWinPositionOnColumn($boardState, $playerUnit);
        if (count($block) > 0) {
            return $block;
        }

        $block = $this->getWinPositionOnDiagonals($boardState, $playerUnit);
        if (count($block) > 0) {
            return $block;
        }

        return [];
    }

    /**
     * @param array $boardState
     * @param string $playerUnit
     *
     * @return array
     */
    private function getBlockPositionOnRow(array $boardState, string $playerUnit) : array
    {
        $positionsOnSameRow = 0;
        $position = [];
        for ($i = 0; $i < 3; $i++) {
            if (!empty($boardState[0][$i]) && $boardState[0][$i] != $playerUnit) {
                $positionsOnSameRow++;
                continue;
            }
            if (empty($boardState[0][$i])) {
                $position = [0, $i];
            }
        }

        if ($positionsOnSameRow == 2 && (count($position) > 0)) {
            return $position;
        }

        $positionsOnSameRow = 0;
        $position = [];
        for ($i = 0; $i < 3; $i++) {
            if (!empty($boardState[1][$i]) && $boardState[1][$i] != $playerUnit) {
                $positionsOnSameRow++;
                continue;
            }
            if (empty($boardState[1][$i])) {
                $position = [1, $i];
            }
        }

        if ($positionsOnSameRow == 2 && (count($position) > 0)) {
            return $position;
        }

        $positionsOnSameRow = 0;
        $position = [];
        for ($i = 0; $i < 3; $i++) {
            if (!empty($boardState[2][$i]) && $boardState[2][$i] != $playerUnit) {
                $positionsOnSameRow++;
                continue;
            }
            if (empty($boardState[2][$i])) {
                $position = [2, $i];
            }
        }

        if ($positionsOnSameRow == 2 && (count($position) > 0)) {
            return $position;
        }

        return [];
    }

    /**
     * @param array $boardState
     * @param string $playerUnit
     *
     * @return array
     */
    private function getWinPositionOnRow(array $boardState, string $playerUnit) : array
    {
        $positionsOnSameRow = 0;
        $position = [];
        for ($i = 0; $i < 3; $i++) {
            if (!empty($boardState[0][$i]) && $boardState[0][$i] == $playerUnit) {
                $positionsOnSameRow++;
                continue;
            }
            if (empty($boardState[0][$i])) {
                $position = [0, $i];
            }
        }

        if ($positionsOnSameRow == 2 && (count($position) > 0)) {
            return $position;
        }

        $positionsOnSameRow = 0;
        $position = [];
        for ($i = 0; $i < 3; $i++) {
            if (!empty($boardState[1][$i]) && $boardState[1][$i] == $playerUnit) {
                $positionsOnSameRow++;
                continue;
            }
            if (empty($boardState[1][$i])) {
                $position = [1, $i];
            }
        }

        if ($positionsOnSameRow == 2 && (count($position) > 0)) {
            return $position;
        }

        $positionsOnSameRow = 0;
        $position = [];
        for ($i = 0; $i < 3; $i++) {
            if (!empty($boardState[2][$i]) && $boardState[2][$i] == $playerUnit) {
                $positionsOnSameRow++;
                continue;
            }
            if (empty($boardState[2][$i])) {
                $position = [2, $i];
            }
        }

        if ($positionsOnSameRow == 2 && (count($position) > 0)) {
            return $position;
        }

        return [];
    }

    /**
     * @param array $boardState
     * @param string $playerUnit
     *
     * @return array
     */
    private function getBlockPositionOnColumn(array $boardState, string $playerUnit) : array
    {
        $positionsOnSameColumn = 0;
        $position = [];
        for ($i = 0; $i < 3; $i++) {
            if (!empty($boardState[$i][0]) && $boardState[$i][0] != $playerUnit) {
                $positionsOnSameColumn++;
                continue;
            }
            if (empty($boardState[$i][0])) {
                $position = [$i, 0];
            }
        }

        if ($positionsOnSameColumn == 2 && (count($position) > 0)) {
            return $position;
        }

        $positionsOnSameColumn = 0;
        $position = [];
        for ($i = 0; $i < 3; $i++) {
            if (!empty($boardState[$i][1]) && $boardState[$i][1] != $playerUnit) {
                $positionsOnSameColumn++;
                continue;
            }
            if (empty($boardState[$i][1])) {
                $position = [$i, 1];
            }
        }

        if ($positionsOnSameColumn == 2 && (count($position) > 0)) {
            return $position;
        }

        $positionsOnSameColumn = 0;
        $position = [];
        for ($i = 0; $i < 3; $i++) {
            if (!empty($boardState[$i][2]) && $boardState[$i][2] != $playerUnit) {
                $positionsOnSameColumn++;
                continue;
            }
            if (empty($boardState[$i][2])) {
                $position = [$i, 2];
            }
        }

        if ($positionsOnSameColumn == 2 && (count($position) > 0)) {
            return $position;
        }

        return [];
    }

    /**
     * @param array $boardState
     * @param string $playerUnit
     *
     * @return array
     */
    private function getWinPositionOnColumn(array $boardState, string $playerUnit) : array
    {
        $positionsOnSameColumn = 0;
        $position = [];
        for ($i = 0; $i < 3; $i++) {
            if (!empty($boardState[$i][0]) && $boardState[$i][0] == $playerUnit) {
                $positionsOnSameColumn++;
                continue;
            }
            if (empty($boardState[$i][0])) {
                $position = [$i, 0];
            }
        }

        if ($positionsOnSameColumn == 2 && (count($position) > 0)) {
            return $position;
        }

        $positionsOnSameColumn = 0;
        $position = [];
        for ($i = 0; $i < 3; $i++) {
            if (!empty($boardState[$i][1]) && $boardState[$i][1] == $playerUnit) {
                $positionsOnSameColumn++;
                continue;
            }
            if (empty($boardState[$i][1])) {
                $position = [$i, 1];
            }
        }

        if ($positionsOnSameColumn == 2 && (count($position) > 0)) {
            return $position;
        }

        $positionsOnSameColumn = 0;
        $position = [];
        for ($i = 0; $i < 3; $i++) {
            if (!empty($boardState[$i][2]) && $boardState[$i][2] == $playerUnit) {
                $positionsOnSameColumn++;
                continue;
            }
            if (empty($boardState[$i][2])) {
                $position = [$i, 2];
            }
        }

        if ($positionsOnSameColumn == 2 && (count($position) > 0)) {
            return $position;
        }

        return [];
    }

    /**
     * @param array $boardState
     * @param string $playerUnit
     *
     * @return array
     */
    private function getBlockPositionOnDiagonals(array $boardState, string $playerUnit) : array
    {
        // Left Diagonals
        if (!empty($boardState[0][0]) && $boardState[0][0] != $playerUnit
            && !empty($boardState[1][1]) && $boardState[1][1] != $playerUnit
            && empty($boardState[2][2])
        ) {
            return [2, 2];
        }

        if (!empty($boardState[0][0]) && $boardState[0][0] != $playerUnit
            && !empty($boardState[2][2]) && $boardState[2][2] != $playerUnit
            && empty($boardState[1][1])
        ) {
            return [1, 1];
        }

        if (!empty($boardState[1][1]) && $boardState[1][1] != $playerUnit
            && !empty($boardState[2][2]) && $boardState[2][2] != $playerUnit
            && empty($boardState[0][0])
        ) {
            return [0, 0];
        }

        // Right Diagonals
        if (!empty($boardState[0][2]) && $boardState[0][2] != $playerUnit
            && !empty($boardState[1][1]) && $boardState[1][1] != $playerUnit
            && empty($boardState[2][0])
        ) {
            return [2, 0];
        }

        if (!empty($boardState[0][2]) && $boardState[0][2] != $playerUnit
            && !empty($boardState[2][0]) && $boardState[2][0] != $playerUnit
            && empty($boardState[1][1])
        ) {
            return [1, 1];
        }

        if (!empty($boardState[1][1]) && $boardState[1][1] != $playerUnit
            && !empty($boardState[2][0]) && $boardState[2][0] != $playerUnit
            && empty($boardState[0][2])
        ) {
            return [0, 2];
        }
        return [];
    }

    /**
     * @param array $boardState
     * @param string $playerUnit
     *
     * @return array
     */
    private function getWinPositionOnDiagonals(array $boardState, string $playerUnit) : array
    {
        // Left Diagonals
        if (!empty($boardState[0][0]) && $boardState[0][0] == $playerUnit
            && !empty($boardState[1][1]) && $boardState[1][1] == $playerUnit
            && empty($boardState[2][2])
        ) {
            return [2, 2];
        }

        if (!empty($boardState[0][0]) && $boardState[0][0] == $playerUnit
            && !empty($boardState[2][2]) && $boardState[2][2] == $playerUnit
            && empty($boardState[1][1])
        ) {
            return [1, 1];
        }

        if (!empty($boardState[1][1]) && $boardState[1][1] == $playerUnit
            && !empty($boardState[2][2]) && $boardState[2][2] == $playerUnit
            && empty($boardState[0][0])
        ) {
            return [0, 0];
        }

        // Right Diagonals
        if (!empty($boardState[0][2]) && $boardState[0][2] == $playerUnit
            && !empty($boardState[1][1]) && $boardState[1][1] == $playerUnit
            && empty($boardState[2][0])
        ) {
            return [2, 0];
        }

        if (!empty($boardState[0][2]) && $boardState[0][2] == $playerUnit
            && !empty($boardState[2][0]) && $boardState[2][0] == $playerUnit
            && empty($boardState[1][1])
        ) {
            return [1, 1];
        }

        if (!empty($boardState[1][1]) && $boardState[1][1] == $playerUnit
            && !empty($boardState[2][0]) && $boardState[2][0] == $playerUnit
            && empty($boardState[0][2])
        ) {
            return [0, 2];
        }
        return [];
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
