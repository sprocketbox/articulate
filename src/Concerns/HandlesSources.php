<?php

namespace Sprocketbox\Articulate\Concerns;

use Sprocketbox\Articulate\Contracts\Source;

trait HandlesSources
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $sources;

    /**
     * @param        $source
     *
     * @return $this
     */
    public function registerSource($source)
    {
        if ($source instanceof \Closure) {
            $source = $source();
        } else if (class_exists($source)) {
            $source = new $source();
        }

        if ($source instanceof Source) {
            $ident = $source->name();

            if ($this->hasSource($ident)) {
                throw new \InvalidArgumentException(sprintf('Source %s already exists', $ident));
            }

            $this->sources->put($ident, $source);

            return $this;
        }

        throw new \RuntimeException(sprintf('Unable to register source %s', \is_string($source) ? $source : \get_class($source)));
    }

    /**
     * @param string $ident
     *
     * @return bool
     */
    public function hasSource(string $ident): bool
    {
        return $this->sources->has($ident);
    }

    /**
     * @param string $ident
     *
     * @return null|\Sprocketbox\Articulate\Contracts\Source
     */
    public function getSource(string $ident): ?Source
    {
        return $this->sources->get($ident, null);
    }
}