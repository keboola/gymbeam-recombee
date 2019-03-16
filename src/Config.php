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
}
