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
    public function cast($value): int
    {
        return (int) $value;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function toDatabase($value): int
    {
        return (int) $value;
    }
}