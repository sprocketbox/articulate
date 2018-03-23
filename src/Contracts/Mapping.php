<?php

namespace Ollieread\Articulate\Contracts;

use Illuminate\Support\Collection;

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