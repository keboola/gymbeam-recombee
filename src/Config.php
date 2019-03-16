<?php

declare(strict_types=1);

namespace Keboola\GymbeamRecombee;

use Keboola\Component\Config\BaseConfig;

class Config extends BaseConfig
{
    public function getToken() : string
    {
        return $this->getValue(['parameters', 'token']);
    }

    public function getDatabaseId() : string
    {
        return $this->getValue(['parameters', 'databaseId']);
    }

    public function getParalelRequests(): int
    {
        return $this->getValue(['parameters', 'paralelRequests']);
    }

    public function getMaxRecommendations(): int
    {
        return $this->getValue(['parameters', 'maxRecommendations']);
    }
}
