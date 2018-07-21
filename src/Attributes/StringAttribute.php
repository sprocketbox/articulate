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
     * @param array  $data
     *
     * @return string
     */
    public function cast($value, array $data = []): ?string
    {
        return $value;
    }

    /**
     * @param       $value
     * @param array $data
     *
     * @return string
     */
    public function parse($value, array $data = []): string
    {
        return (string) $value;
    }
}