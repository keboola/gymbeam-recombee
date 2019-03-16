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
        $batch = [];
        if (($handle = fopen($inFile, "r")) !== false) {
            $row = 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                if ($row++ == 0) {
                    continue;
                }
                $batch[] = [$data[0], $data[1]];
                if ($row % $this->getComponentConfig()->getParalelRequests() == 0) {
                    $recomms = $this->getRecommendations($batch);
                    $this->writeRecommendationToFile($batch, $recomms);
                    $batch = [];
                }
            }
            fclose($handle);
        }

        if (count($batch)) {
            $recomms = $this->getRecommendations($batch);
            $this->writeRecommendationToFile($batch, $recomms);
        }
    }

    private function getRecommendations(array $batch): array
    {
        $reqParts = [];
        foreach ($batch as $item) {
            $reqParts[] = new Reqs\RecommendItemsToUser($item[0], $item[1]);
        }
        return $this->getClient()->send(new Reqs\Batch($reqParts));
    }

    private function writeRecommendationToFile(array $batch, array $recomms): void
    {
        foreach ($batch as $key => $item) {
            $items = [];
            for ($i = 0; $i < $this->getComponentConfig()->getMaxRecommendations(); $i++) {
                if (isset($recomms[$key]['json']['recomms'][$i])) {
                    $items[$i] = $recomms[$key]['json']['recomms'][$i]['id'];
                } else {
                    $items[$i] = '';
                }
            }
            file_put_contents(
                $this->getDataDir() . self::OUTPUT_TABLE,
                $item [0] . ',' . implode(',', $items) . "\n",
                FILE_APPEND
            );
        }
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
