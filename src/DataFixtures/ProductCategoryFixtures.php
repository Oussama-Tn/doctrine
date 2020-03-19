<?php

namespace App\DataFixtures;

use App\Entity\ProductCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ProductCategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i < 5; $i++) {
            $productCategory = new ProductCategory();
            $productCategory->setName('Prd Categ ' . $i);
            $manager->persist($productCategory);
        }

        $manager->flush();
    }
}
