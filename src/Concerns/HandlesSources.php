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
     * @param string $ident
     * @param        $source
     *
     * @return $this
     */
    public function registerSource(string $ident, $source)
    {
        if ($this->hasSource($ident)) {
            throw new \InvalidArgumentException(sprintf('Source %s already exists', $ident));
        }

        if ($source instanceof \Closure) {
            $source = $source($ident);
        }

        if (class_exists($source)) {
            $source = new $source($ident);
        }

        if ($source instanceof Source) {
            $this->sources->put($ident, $source);

            return $this;
        }

        throw new \RuntimeException(sprintf('Unable to register source %s', $ident));
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