<?php

namespace Sprocketbox\Articulate\Contracts;

interface Attribute
{
    public function cast($value, array $data = []);

    public function parse($value, array $data = []);

    public function getColumnName(): string;

    public function getName(): string;

    public function getDefault();

    public function generate(array $attributes);

    public function isImmutable(): bool;

    public function isDynamic(): bool;

    public function isComponent(): bool;

    public function getComponent(): ?string;

    public function belongsTo(string $class): bool;
}