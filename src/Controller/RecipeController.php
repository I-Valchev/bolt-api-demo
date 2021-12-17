<?php

namespace App\Controller;

use App\RecipeImporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/recipes")
 */
class RecipeController extends AbstractController
{

    /** @var RecipeImporter */
    private RecipeImporter $importer;

    public function __construct(RecipeImporter $importer)
    {
        $this->importer = $importer;
    }

    /**
     * @Route("/import/{id}", name="recipes_import")
     */
    public function importRecipe(int $id): Response
    {
        dump("ID: " . $id);
        $this->importer->importRecipe($id);
        dd('OK');
    }
}
