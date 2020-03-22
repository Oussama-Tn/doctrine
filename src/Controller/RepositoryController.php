<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RepositoryController
 * @package App\Controller
 * @Route("/repository")
 */

class RepositoryController extends AbstractController
{

    // https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/query-builder.html
    // https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/dql-doctrine-query-language.html

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="qb")
     */
    public function index()
    {
        return $this->json([]);
    }

    /**
     * @Route("/find-one/{id}", name="repository_find_one")
     */
    public function find(ProductRepository $productRepository, $id = 1)
    {
        // We use the repository
        $product = $productRepository->find($id);

        return $this->json($product, 200, [], ['groups' => 'products:read']);
    }

    /**
     * @Route("/find-all", name="repository_find_all")
     */
    public function findAll(ProductRepository $productRepository)
    {
        // We use the repository
        $products = $productRepository->findAll();

        return $this->json($products, 200, [], ['groups' => 'products:read']);
    }

    /**
     * @Route("/find-one-by/{attribute}/{value}", name="repository_find_one_by")
     */
    public function findOneBy(ProductRepository $productRepository, string $attribute = 'id', string $value = '1')
    {
        // We use the repository
        $product = $productRepository->findOneBy([$attribute => $value]);

        // Important => we can set many criteria
        // example $productRepository->findOneBy(['productCategory' => 3, 'isAvailable' => true])

        return $this->json($product, 200, [], ['groups' => 'products:read']);
    }

    /**
     * @Route("/find-by/{attribute}/{value}", name="repository_find_by")
     */
    public function findBy(ProductRepository $productRepository, string $attribute = 'id', string $value = '1')
    {
        // We use the repository
        $product = $productRepository->findBy([$attribute => $value]);
        // Important => we can set many criteria
        // $productRepository->findBy(['productCategory' => 3, 'isAvailable' => true])

        $data = [
            'Important' => 'We can set many criteria: $productRepository->findBy([\'productCategory\' => 3, \'isAvailable\' => true])',
            'query' => "\$productRepository->findBy([$attribute => $value])",
            'query result' => $product,
        ];

        return $this->json($data, 200, [], ['groups' => 'products:read']);
    }


    /**
     * @Route("/count-result", name="repository_count_result")
     * @param UserRepository $userRepository
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function selectSome(UserRepository $userRepository)
    {
        $result = $userRepository->findAll();

        return $this->json([
            'count' => 'count($result)',
            'result' => count($result)
        ]);
    }

}
