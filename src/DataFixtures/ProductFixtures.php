<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Repository\ProductCategoryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var ProductCategoryRepository
     */
    private $productCategoryRepository;

    private $faker;

    public function __construct(ProductCategoryRepository $productCategoryRepository)
    {
        $this->productCategoryRepository = $productCategoryRepository;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $productCategories = $this->productCategoryRepository->findAll();

        foreach ($productCategories as $productCategory) {

            $randomNumber = rand(4, 9);

            for ($i = 1; $i < $randomNumber; $i++) {
                $product = new Product();
                $product->setPrice(rand(200, 2000));
                $product->setIsAvailable($this->faker->boolean);
                $product->setName('Product ' . $productCategory->getId() . '/' . $i);
                $product->setProductCategory($productCategory);
                $manager->persist($product);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProductCategoryFixtures::class,
        ];
    }

}
