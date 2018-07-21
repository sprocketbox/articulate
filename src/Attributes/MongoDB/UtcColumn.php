<?php

namespace Sprocketbox\Articulate\Attributes\MongoDB;

use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;
use Sprocketbox\Articulate\Attributes\BaseAttribute;

class UtcColumn extends BaseAttribute
{

    /**
     * @param UTCDateTime $value
     * @param array       $data
     *
     * @return \Carbon\Carbon
     */
    public function cast($value, array $data = [])
    {
        return $value ? Carbon::createFromTimestamp($value->toDateTime()->getTimestamp()) : null;
    }

    public function parse($value, array $data = [])
    {
        return new UTCDateTime($value instanceof Carbon ? $value->getTimestamp() * 1000 : $value);
    }
}