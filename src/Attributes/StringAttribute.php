<?php

namespace Sprocketbox\Articulate\Attributes;

/**
 * Class StringColumn
 *
 * @package Sprocketbox\Articulate\Attributes
 */
class StringAttribute extends BaseAttribute
{
    /**
     * @param string $value
     *
     * @return string
     */
    public function cast($value): ?string
    {
        return $value;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function parse($value): string
    {
        return (string) $value;
    }
}