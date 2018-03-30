<?php

namespace Ollieread\Articulate\Columns\MongoDB;

use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;
use Ollieread\Articulate\Columns\BaseColumn;

class UtcColumn extends BaseColumn
{

    /**
     * @param UTCDateTime $value
     *
     * @return \Carbon\Carbon
     * @throws \InvalidArgumentException
     */
    public function cast($value)
    {
        return Carbon::createFromTimestamp($value->toDateTime()->getTimestamp());
    }

    public function toDatabase($value)
    {
        return new UTCDateTime($value instanceof Carbon ? $value->getTimestamp() * 1000 : $value);
    }
}