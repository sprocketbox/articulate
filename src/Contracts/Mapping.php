<?php

namespace Ollieread\Articulate\Contracts;

use Illuminate\Support\Collection;
use Ollieread\Articulate\Columns;

/**
 * Interface Mapping
 *
 * @method Columns\BoolColumn mapBool(string $attributeName)
 * @method Columns\EntityColumn mapEntity(string $attributeName, string $entityClass, bool $multiple = false)
 * @method Columns\IntColumn mapInt(string $attributeName)
 * @method Columns\JsonColumn mapJson(string $attributeName)
 * @method Columns\StringColumn mapString(string $attributeName)
 * @method Columns\TimestampColumn mapTimestamp(string $attributeName, string $format = 'Y-m-d H:i:s')
 *
 * @package Ollieread\Articulate\Contracts
 */
interface Mapping
{
    /**
     * @return string
     */
    public function getEntity(): string;

    /**
     * @return string
     */
    public function getConnection(): string;

    /**
     * @return string
     */
    public function getTable(): string;

    /**
     * @return string
     */
    public function getKey(): string;

    /**
     * @param string $key
     *
     * @return \Ollieread\Articulate\Contracts\Mapping
     */
    public function setKey(string $key);

    /**
     * @return null|string
     */
    public function getRepository(): ?string;

    /**
     * @param string $repository
     *
     * @return \Ollieread\Articulate\Contracts\Mapping
     */
    public function setRepository(string $repository);

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getColumns(): Collection;

    /**
     * @param \Ollieread\Articulate\Contracts\Column $type
     *
     * @return \Ollieread\Articulate\Contracts\Column
     */
    public function mapColumn(Column $type): Column;

    /**
     * @param string $column
     *
     * @return null|\Ollieread\Articulate\Contracts\Column
     */
    public function getColumn(string $column): ?Column;
}