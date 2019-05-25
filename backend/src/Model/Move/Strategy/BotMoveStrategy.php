<?php
namespace App\Model\Move\Strategy;

class BotMoveStrategy
{
    /**
     * @param array $boardState
     * @param string $playerUnit
     *
     * @return array|null
     */
    protected function getPositionOnRow(array $boardState, string $playerUnit) : ?array
    {
        for ($row = 0; $row < 3; $row++) {
            $positionsOnSameRow = 0;
            $move = [];

            for ($column = 0; $column < 3; $column++) {
                if (!empty($boardState[$row][$column]) && $boardState[$row][$column] == $playerUnit) {
                    $positionsOnSameRow++;
                    continue;
                }
                if (empty($boardState[$row][$column])) {
                    $move = [$row, $column];
                }
            }

            if ($positionsOnSameRow == 2 && (count($move) > 0)) {
                return $move;
            }
        }

        return null;
    }

    /**
     * @param array $boardState
     * @param string $playerUnit
     *
     * @return array|null
     */
    protected function getPositionOnColumn(array $boardState, string $playerUnit) : ?array
    {
        for ($column = 0; $column < 3; $column++) {
            $positionsOnSameColumn = 0;
            $move = [];

            for ($row = 0; $row < 3; $row++) {
                if (!empty($boardState[$row][$column]) && $boardState[$row][$column] == $playerUnit) {
                    $positionsOnSameColumn++;
                    continue;
                }
                if (empty($boardState[$row][$column])) {
                    $move = [$row, $column];
                }
            }

            if ($positionsOnSameColumn == 2 && (count($move) > 0)) {
                return $move;
            }
        }

        return null;
    }

    /**
     * @param array $boardState
     * @param string $playerUnit
     *
     * @return array|null
     */
    protected function getPositionOnLeftDiagonal(array $boardState, string $playerUnit) : ?array
    {
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
        return null;
    }

    /**
     * @param array $boardState
     * @param string $playerUnit
     *
     * @return array|null
     */
    protected function getPositionOnRightDiagonal(array $boardState, string $playerUnit) : ?array
    {
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
        return null;
    }
}
