<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(EntityManagerInterface $entityManager)
    {
        $qb = $entityManager->createQueryBuilder();

        $query = $qb->select('u')
            ->from(User::class, 'u')
            ->leftJoin('u.purchaseOrders', 'po')
            ->addSelect('po')
            ->leftJoin('po.purchaseOrderProducts', 'pop')
            ->addSelect('pop')
            ->leftJoin('pop.product', 'p')
            ->addSelect('p')
            ->leftJoin('p.productCategory', 'pc')
            ->addSelect('pc')
            ->orderBy('u.id', 'ASC')
            ->getQuery();

        $allRows = $query->execute();

        return $this->render('home/index.html.twig', [
            'allRows' => $allRows
        ]);
    }
}
