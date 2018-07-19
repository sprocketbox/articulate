<?php

namespace Sprocketbox\Articulate\Attributes\MongoDB;

use MongoDB\BSON\ObjectId;
use Sprocketbox\Articulate\Attributes\BaseAttribute;

class ObjectIdColumn extends BaseAttribute
{

    /**
     * @param $value
     *
     * @return \MongoDB\BSON\ObjectId
     * @throws \InvalidArgumentException
     */
    public function cast($value)
    {
        return new ObjectId($value);
    }

    public function parse($value)
    {
        return $value;
    }
}