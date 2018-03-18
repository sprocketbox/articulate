<?php

namespace Ollieread\Articulate\Columns;

/**
 * Class IntColumn
 *
 * @package Ollieread\Articulate\Columns
 */
class IntColumn extends BaseColumn
{
    /**
     * @param string $value
     *
     * @return int
     */
    public function cast(string $value): int
    {
        return (int) $value;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function toDatabase($value): string
    {
        return (int) $value;
    }
}