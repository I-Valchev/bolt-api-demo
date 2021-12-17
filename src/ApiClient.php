<?php

namespace App;

use GuzzleHttp\Client;

class ApiClient
{
    /** @var string */
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
        $this->client = new Client();
    }

    public const ENDPOINT_RECIPE_ID = 'www.themealdb.com/api/json/v1/1/lookup.php?i=';

    public function fetchRecipe(int $id): array
    {
        $endpoint = $this->getRecipeEndpoint($id);
        $response = $this->client->get($endpoint);

        if ($response->getStatusCode() !== 200) {
            dd("no. it didn't work. sorry");
        }

        $recipeJson = json_decode($response->getBody()->getContents(), true);

        return current($recipeJson['meals']);
    }

    public function downloadImage(string $url): string
    {
        $filename = uniqid() . '.jpg';

        $this->client->get($url, [
            'sink' => $this->projectDir . '/public/files/' . $filename
        ]);

        return $filename;
    }

    private function getRecipeEndpoint(int $id): string
    {
        return sprintf('%s%d', self::ENDPOINT_RECIPE_ID, $id);
    }
}
