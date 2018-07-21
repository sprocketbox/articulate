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
     * @param array  $data
     *
     * @return int
     */
    public function cast($value, array $data = []): int
    {
        return (int) $value;
    }

    /**
     * @param       $value
     * @param array $data
     *
     * @return int
     */
    public function parse($value, array $data = []): int
    {
        return (int) $value;
    }
}