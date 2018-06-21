<?php

namespace Ollieread\Articulate\Columns;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class StringColumn
 *
 * @package Ollieread\Articulate\Columns
 */
class StringColumn extends BaseColumn
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
    public function toDatabase($value): string
    {
        return (string) $value;
    }
}