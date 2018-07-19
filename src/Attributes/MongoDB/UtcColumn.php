<?php

namespace Sprocketbox\Articulate\Attributes\MongoDB;

use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;
use Sprocketbox\Articulate\Attributes\BaseAttribute;

class UtcColumn extends BaseAttribute
{

    /**
     * @param UTCDateTime $value
     *
     * @return \Carbon\Carbon
     * @throws \InvalidArgumentException
     */
    public function cast($value)
    {
        return $value ? Carbon::createFromTimestamp($value->toDateTime()->getTimestamp()) : null;
    }

    public function parse($value)
    {
        return new UTCDateTime($value instanceof Carbon ? $value->getTimestamp() * 1000 : $value);
    }
}