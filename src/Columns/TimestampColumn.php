<?php

namespace Ollieread\Articulate\Columns;

use Carbon\Carbon;

/**
 * Class TimestampColumn
 *
 * @package Ollieread\Articulate\Columns
 */
class TimestampColumn extends BaseColumn
{
    /**
     * @var string
     */
    protected $format;

    /**
     * TimestampColumn constructor.
     *
     * @param string $columnName
     * @param string $format
     */
    public function __construct(string $columnName, string $format = 'Y-m-d H:i:s')
    {
        parent::__construct($columnName);
        $this->format = $format;
    }

    /**
     * @param string $value
     *
     * @return \Carbon\Carbon
     * @throws \InvalidArgumentException
     */
    public function cast(string $value): Carbon
    {
        return Carbon::createFromFormat($this->format, $value);
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function toDatabase($value): string
    {
        /**
         * @var \Carbon\Carbon $value
         */
        return $value->format($this->format);
    }
}