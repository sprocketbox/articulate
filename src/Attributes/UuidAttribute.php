<?php

namespace Sprocketbox\Articulate\Attributes;

use Ramsey\Uuid\Uuid;

class UuidAttribute extends BaseAttribute
{

    /**
     * @param string $value
     * @param array  $data
     *
     * @return \Ramsey\Uuid\UuidInterface
     */
    public function cast($value, array $data = [])
    {
        return Uuid::fromString($value);
    }

    /**
     * @param \Ramsey\Uuid\UuidInterface $value
     * @param array                      $data
     *
     * @return string
     */
    public function parse($value, array $data = [])
    {
        return $value->toString();
    }
}