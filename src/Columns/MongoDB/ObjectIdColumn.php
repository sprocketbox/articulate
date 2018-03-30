<?php

namespace Ollieread\Articulate\Columns\MongoDB;

use MongoDB\BSON\ObjectId;
use Ollieread\Articulate\Columns\BaseColumn;

class ObjectIdColumn extends BaseColumn
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

    public function toDatabase($value)
    {
        return $value;
    }
}