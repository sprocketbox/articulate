<?php

namespace Sprocketbox\Articulate\Contracts;

interface Attributeable
{
    /**
     * @param string $attribute
     * @param        $value
     */
    public function set(string $attribute, $value): void;

    public function setAttribute(string $attribute, $value);
    /**
     * @param string $attribute
     *
     * @return mixed
     */
    public function get(string $attribute);

    public function getAll(): array;

    public function getAttribute(string $attribute);

    public function getAttributes(): array;
    /**
     * @param null|string $column
     *
     * @return bool
     */
    public function isDirty(?string $column = null): bool;
    /**
     *
     */
    public function clean(): void;
}