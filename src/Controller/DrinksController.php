<?php

namespace App\Controller;

use App\Entity\Drink;
use App\Repository\DrinkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1/api")
 */
class DrinksController extends AbstractController
{
    /**
     * @var DrinkRepository
     */
    private $drinkRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(DrinkRepository $drinkRepository, EntityManagerInterface $entityManager)
    {
        $this->drinkRepository = $drinkRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/drinks", name="drinks", methods={"GET"})
     * @IsGranted("ROLE_API_READ")
     */
    public function index(): Response
    {
        $drinks = $this->drinkRepository->findAll();

        return new JsonResponse($drinks);
    }

    /**
     * @Route("/drinks/{id}", methods={"GET"}, requirements={"id": "\d+"})
     * @IsGranted("ROLE_API_READ")
     */
    public function getDrink(Drink $drink): JsonResponse
    {
        return new JsonResponse($drink);
    }

    /**
     * @IsGranted("ROLE_API_WRITE")
     * @Route("/drinks", methods={"POST"})
     */
    public function postDrink(): JsonResponse
    {
        $drink = new Drink();

        $request = Request::createFromGlobals();

        $drink->setName($request->get('name'));
        $drink->setDescription($request->get('description'));

        $this->entityManager->persist($drink);
        $this->entityManager->flush();

        return new JsonResponse("OK");
    }

    /**
     * @Route("/drinks/{id}", methods={"DELETE"}, requirements={"id": "\d+"})
     * @IsGranted("ROLE_API_DELETE")
     */
    public function deleteDrink(Drink $drink): JsonResponse
    {
        $this->entityManager->remove($drink);
        $this->entityManager->flush();

        return new JsonResponse("DELETED!!");
    }
}
