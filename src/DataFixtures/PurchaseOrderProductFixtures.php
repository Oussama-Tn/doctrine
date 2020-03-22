<?php

namespace App\DataFixtures;

use App\Entity\PurchaseOrderProduct;
use App\Repository\ProductRepository;
use App\Repository\PurchaseOrderRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class PurchaseOrderProductFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var PurchaseOrderRepository
     */
    private $purchaseOrderRepository;

    public function __construct(ProductRepository $productRepository, PurchaseOrderRepository $purchaseOrderRepository)
    {
        $this->productRepository = $productRepository;
        $this->purchaseOrderRepository = $purchaseOrderRepository;
    }

    public function load(ObjectManager $manager)
    {
        $purchaseOrders = $this->purchaseOrderRepository->findAll();

        foreach ($purchaseOrders as $purchaseOrder) {



            $randomProducts = $this->productRepository->findInRandomOrder(rand(2, 3));
            foreach ($randomProducts as $product) {

                $purchaseOrderProduct = new PurchaseOrderProduct();
                $purchaseOrderProduct->setProduct($product);
                $purchaseOrderProduct->setQuantity(rand(1, 10));
                $manager->persist($purchaseOrderProduct);

                $purchaseOrder->addPurchaseOrderProduct($purchaseOrderProduct);

            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProductFixtures::class,
            PurchaseOrderFixtures::class,
        ];
    }
}
