<?php

namespace Ms\Base\Tool;

use Psr\Container\ContainerInterface;

class MiniContainer implements ContainerInterface
{
    public function __construct(
        private array $services = [],
    ){
    }

    public function get(string $id)
    {
        return $this->services[$id] ?? null;
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }
}