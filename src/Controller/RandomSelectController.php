<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/random-select")
 * Class SerializeController
 * @package App\Controller
 */

class RandomSelectController extends AbstractController
{
    /**
     * @Route("/", name="random_select_index")
     */
    public function index()
    {
        $referenceType = UrlGeneratorInterface::ABSOLUTE_URL;

        return $this->json([
            'random_select' => $this->generateUrl('random_select', [], $referenceType),
            'random_select_serialized' => $this->generateUrl('random_select_serialized', [], $referenceType),
            'random_select_auto_serialized' => $this->generateUrl('random_select_auto_serialized', [], $referenceType),
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
