<?php

namespace Sprocketbox\Articulate\Attributes;

use Carbon\Carbon;

/**
 * Class TimestampColumn
 *
 * @package Sprocketbox\Articulate\Attributes
 */
class TimestampAttribute extends BaseAttribute
{
    /**
     * @var string
     */
    protected $format;

    /**
     * TimestampColumn constructor.
     *
     * @param string $attributeName
     * @param string $format
     */
    public function __construct(string $attributeName, string $format = 'Y-m-d H:i:s')
    {
        parent::__construct($attributeName);
        $this->format = $format;
    }

    /**
     * @param string $value
     * @param array  $data
     *
     * @return \Carbon\Carbon
     */
    public function cast($value, array $data = []): ?Carbon
    {
        return $value ? Carbon::createFromFormat($this->format, (string) $value) : null;
    }

    /**
     * @param       $value
     * @param array $data
     *
     * @return string
     */
    public function parse($value, array $data = []): ?string
    {
        /**
         * @var \Carbon\Carbon $value
         */
        return $value ? $value->format($this->format) : $this->getDefault();
    }
}