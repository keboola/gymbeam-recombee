<?php

declare(strict_types=1);

namespace Keboola\GymbeamRecombee;

use Keboola\Component\Config\BaseConfigDefinition;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class ConfigDefinition extends BaseConfigDefinition
{
    protected function getParametersDefinition(): ArrayNodeDefinition
    {
        $parametersNode = parent::getParametersDefinition();
        // @formatter:off
        /** @noinspection NullPointerExceptionInspection */
        $parametersNode
            ->children()
                ->scalarNode('databaseId')
                    ->isRequired()
                ->end()
                ->scalarNode('token')
                    ->isRequired()
                ->end()
                ->integerNode('paralelRequests')
                    ->defaultValue(10)
                ->end()
                ->integerNode('maxRecommendations')
            ->end()
        ;
        // @formatter:on
        return $parametersNode;
    }
}
