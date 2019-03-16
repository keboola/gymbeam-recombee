<?php

declare(strict_types=1);

namespace Keboola\GymbeamRecombee;

use Keboola\Component\BaseComponent;
use Recombee\RecommApi\Client;
use Recombee\RecommApi\Requests as Reqs;

class Component extends BaseComponent
{
    private const INPUT_TABLE = '/in/tables/input.csv';
    private const OUTPUT_TABLE = '/out/tables/output.csv';

    /** @var Client */
    private $client = null;

    public function run(): void
    {
        $inFile = $this->getDataDir() . self::INPUT_TABLE;

        if (!file_exists($inFile)) {
            throw new \Exception("File {$inFile} not found");
        }

        if (($handle = fopen($inFile, "r")) !== false) {
            $row = 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                if ($row++ == 0) {
                    continue;
                }
                $recomms = $this->getRecommendations($data[0], (int) $data[1]);
                $this->writeRecommendationToFile($data[0], $recomms);
            }
            fclose($handle);
        }
    }

    private function writeRecommendationToFile(string $email, array $recomms): void
    {
        $items = ['', '', '', '', '', '', '', '', '', ''];
        for ($i = 0; $i < 9; $i++) {
            if (isset($recomms['recomms'][$i])) {
                $items[$i] = $recomms['recomms'][$i]['id'];
            }
        }
        file_put_contents(
            $this->getDataDir() . self::OUTPUT_TABLE,
            $email . ',' . implode(',', $items) . "\n",
            FILE_APPEND
        );
    }

    private function getRecommendations(string $email, int $count): array
    {
        return $this->getClient()->send(new Reqs\RecommendItemsToUser($email, $count));
    }

    private function getClient(): Client
    {
        if ($this->client == null) {
            $this->client = new Client(
                $this->getComponentConfig()->getDatabaseId(),
                $this->getComponentConfig()->getToken()
            );
        }
        return $this->client;
    }

    public function getComponentConfig(): Config
    {
        /** @var Config $config */
        $config = parent::getConfig();
        return $config;
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
