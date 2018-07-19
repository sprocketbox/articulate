<?php

namespace Sprocketbox\Articulate\Attributes;

/**
 * Class IntColumn
 *
 * @package Sprocketbox\Articulate\Attributes
 */
class IntAttribute extends BaseAttribute
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
     * @return int
     */
    public function parse($value): int
    {
        return (int) $value;
    }
}