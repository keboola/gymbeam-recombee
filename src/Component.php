<?php

declare(strict_types=1);

namespace Keboola\GymbeamRecombee;

use Keboola\Component\BaseComponent;

class Component extends BaseComponent
{
    public function run(): void
    {
        $inFile = $this->getDataDir() . '/tables/data.csv';
    }

    protected function getConfigClass(): string
    {
        return Config::class;
    }

    protected function getConfigDefinitionClass(): string
    {
        return ConfigDefinition::class;
    }
}
