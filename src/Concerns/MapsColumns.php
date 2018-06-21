<?php

namespace Ollieread\Articulate\Concerns;

use Illuminate\Support\Collection;
use Ollieread\Articulate\Columns;
use Ollieread\Articulate\Contracts\Column;
use RuntimeException;

/**
 * Trait MapsColumns
 *
 * @method Columns\BoolColumn bool(string $attributeName)
 * @method Columns\EntityColumn entity(string $attributeName, string $entityClass, bool $multiple = false)
 * @method Columns\IntColumn int(string $attributeName)
 * @method Columns\JsonColumn json(string $attributeName)
 * @method Columns\StringColumn string(string $attributeName)
 * @method Columns\TimestampColumn timestamp(string $attributeName, string $format = 'Y-m-d H:i:s')
 * @method Columns\FloatColumn float(string $attributeName)
 *
 * The following methods are for MongoDB only
 *
 * @method Columns\MongoDB\ObjectIdColumn objectId(string $attributeName)
 * @method Columns\MongoDB\SubdocumentColumn subdocument(string $attributeName, string $entityClass, bool $multiple = false)
 * @method Columns\MongoDB\UtcColumn utc(string $attributeName)
 *
 * @package Ollieread\Articulate\Concerns
 */
trait MapsColumns
{

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $columns;

    /**
     * @param $name
     * @param $arguments
     *
     * @return \Ollieread\Articulate\Contracts\Column
     * @throws \RuntimeException
     */
    public function __call($name, $arguments)
    {
        $column      = snake_case($name);
        $columnClass = config('articulate.columns.' . $column, null);

        if ($columnClass && class_exists($columnClass)) {
            return $this->newColumn(config('articulate.columns.' . $column), $arguments);
        }

        throw new RuntimeException('Invalid column');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getColumns(): Collection
    {
        return $this->columns;
    }

    /**
     * @param \Ollieread\Articulate\Contracts\Column $type
     *
     * @return \Ollieread\Articulate\Contracts\Column
     */
    public function mapColumn(Column $type): Column
    {
        $this->columns->put($type->getColumnName(), $type);

        return $type;
    }

    /**
     * @param string $column
     *
     * @return null|\Ollieread\Articulate\Contracts\Column
     */
    public function getColumn(string $column): ?Column
    {
        $columnName = $column;

        return $this->columns->first(function (Column $column) use ($columnName) {
            return $column->getColumnName() === $columnName || $column->getAttributeName() === $columnName;
        });
    }

    /**
     * @param string $columnClass
     * @param        $arguments
     *
     * @return \Ollieread\Articulate\Contracts\Column
     * @throws \InvalidArgumentException
     */
    protected function newColumn(string $columnClass, $arguments): Column
    {
        return $this->mapColumn(new $columnClass(...$arguments));
    }

    public function timestamps()
    {
        $this->timestamp('created_at');
        $this->timestamp('updated_at');
    }

    public function utcTimestamps()
    {
        $this->utc('created_at');
        $this->utc('updated_at');
    }
}