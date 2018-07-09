<?php

namespace Ollieread\Articulate\Columns;

use Ramsey\Uuid\Uuid;

class UuidColumn extends BaseColumn
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
    public function toDatabase($value)
    {
        return $value->toString();
    }
}