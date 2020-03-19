<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index()
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    /**
     * @Route("/random-select-auto-serialized", name="random_select_auto_serialized")
     */
    public function selectRandomlyAutoSerilized(ProductRepository $productRepository)
    {
        $randomProducts = $productRepository->findInRandomOrder(3);

        return $this->json($randomProducts, 200, [], ['groups' => 'products:read']);
    }

    /**
     * @Route("/random-select", name="random_select")
     */
    public function selectRandomly(NormalizerInterface $normalizer, ProductRepository $productRepository)
    {
        $randomProducts = $productRepository->findInRandomOrder(3);

        // Transform object to array
        $productsArr = $normalizer->normalize(
            $randomProducts,
            null,
            ['groups' => 'products:read']
        );

        return $this->json($productsArr);
    }

    /**
     * @Route("/random-select-serialized", name="random_select_serialized")
     */
    public function selectRandomlySerialized(SerializerInterface $serializer, ProductRepository $productRepository)
    {
        $randomProducts = $productRepository->findInRandomOrder(3);

        // Transform object to json
        $productsJson = $serializer->serialize(
            $randomProducts,
            'json',
            ['groups' => 'products:read']
        );

        return Response::create($productsJson, 200);
    }
}
