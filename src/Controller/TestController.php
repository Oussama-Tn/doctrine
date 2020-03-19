<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/random-select", name="random_select")
     */
    public function selectRandomly(ProductRepository $productRepository)
    {
        $randomProducts = $productRepository->findInRandomOrder(3);

        dd($randomProducts);

        return $this->json([]);
    }

}
