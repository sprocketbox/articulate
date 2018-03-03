<?php

namespace Ollieread\Articulate\Columns;

use Carbon\Carbon;

class TimestampColumn extends BaseColumn
{
    /**
     * @var string
     */
    protected $format;

    public function __construct(string $columnName, string $format = 'Y-m-d H:i:s')
    {
        parent::__construct($columnName);
        $this->format = $format;
    }

    public function cast(string $value): Carbon
    {
        return Carbon::createFromFormat($this->format, $value);
    }

    public function toDatabase($value): string
    {
        return $value->format($this->format);
    }
}