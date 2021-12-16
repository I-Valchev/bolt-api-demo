<?php

namespace App;

use Bolt\Factory\ContentFactory;
use Cocur\Slugify\Slugify;

class RecipeImporter
{

    /** @var ContentFactory */
    private ContentFactory $factory;

    /** @var ApiClient */
    private ApiClient $client;

    public function __construct(ContentFactory $factory, ApiClient $client)
    {
        $this->factory = $factory;
        $this->client = $client;
    }

    public function importRecipe(int $id): void
    {
        $recipe = collect($this->client->fetchRecipe($id));

        $recipe = $recipe->keyBy(function($value, $key) {
           return self::recipeFieldMap()[$key] ?? null;
        })->filter();

        $content = $this->factory->upsert('recipes', ['title' => $recipe['title']]);


        foreach($recipe as $name => $value) {

            if ($content->getDefinition()->get('fields')[$name]['type'] === 'image') {
                $filename = $this->client->downloadImage($value);
                $content->setFieldValue($name, ['filename' => $filename]);
                continue;
            }

            $content->setFieldValue($name, $value);
        }

        $slugify = Slugify::create();
        $content->setFieldValue('slug', $slugify->slugify($recipe['title']));

        $this->factory->save($content);

    }

    private static function recipeFieldMap(): array
    {
        return [
            'idMeal' => 'recipe_id',
            'strMeal' => 'title',
            'strMealThumb' => 'photo',
            'strInstructions' => 'instructions'
        ];
    }
}
