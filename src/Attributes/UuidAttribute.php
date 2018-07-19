<?php

namespace Sprocketbox\Articulate\Attributes;

use Ramsey\Uuid\Uuid;

class UuidAttribute extends BaseAttribute
{

    /**
     * @param string $value
     *
     * @return \Ramsey\Uuid\UuidInterface
     */
    public function cast($value)
    {
        return Uuid::fromString($value);
    }

    /**
     * @param \Ramsey\Uuid\UuidInterface $value
     *
     * @return string
     */
    public function parse($value)
    {
        return $value->toString();
    }
}