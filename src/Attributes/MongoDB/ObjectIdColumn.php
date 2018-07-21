<?php

namespace Sprocketbox\Articulate\Attributes\MongoDB;

use MongoDB\BSON\ObjectId;
use Sprocketbox\Articulate\Attributes\BaseAttribute;

class ObjectIdColumn extends BaseAttribute
{

    /**
     * @param       $value
     * @param array $data
     *
     * @return \MongoDB\BSON\ObjectId
     */
    public function cast($value, array $data = [])
    {
        return new ObjectId($value);
    }

    public function parse($value, array $data = [])
    {
        return $value;
    }
}