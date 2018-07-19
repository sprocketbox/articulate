<?php

namespace Sprocketbox\Articulate\Concerns\MongoDB;

use MongoDB\BSON\ObjectId;

trait HasObjectId
{

    public function getId(): ObjectId
    {
        return $this->get('_id');
    }

    public function setId(ObjectId $value): self
    {
        $this->set('_id', $value);

        return $this;
    }
}