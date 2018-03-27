<?php

namespace Ollieread\Articulate\Concerns;

use Illuminate\Support\Collection;
use Ollieread\Articulate\Columns;
use Ollieread\Articulate\Contracts\Column;
use RuntimeException;

/**
 * Trait MapsColumns
 *
 * @method Columns\BoolColumn mapBool(string $attributeName)
 * @method Columns\EntityColumn mapEntity(string $attributeName, string $entityClass, bool $multiple = false)
 * @method Columns\IntColumn mapInt(string $attributeName)
 * @method Columns\JsonColumn mapJson(string $attributeName)
 * @method Columns\StringColumn mapString(string $attributeName)
 * @method Columns\TimestampColumn mapTimestamp(string $attributeName, string $format = 'Y-m-d H:i:s')
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
        if (strpos($name, 'map') === 0) {
            $column = snake_case(substr($name, 3));
            $columnClass = config('articulate.columns.'.$column, null);

            if ($columnClass && class_exists($columnClass)) {
                return $this->newColumn(config('articulate.columns.' . $column), $arguments);
            }
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

        return $this->columns->first(function (Column $column) use($columnName) {
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
        try {
            /** @noinspection PhpParamsInspection */
            return $this->mapColumn((new \ReflectionClass($columnClass))->newInstanceArgs($arguments));
        } catch(\ReflectionException $e) {
            throw new \InvalidArgumentException('Invalid column type', 0, $e);
        }
    }
}