<?php
// src/Blogger/BlogBundle/DataFixtures/ORM/BlogFixtures.php

namespace Blogger\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Blogger\BlogBundle\Entity\Category;

class CategoryFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load($manager)
    {
        $category1 = new category();
        $category1->setName('general'); 
        $manager->persist($category1);
        $category2 = new category();
        $category2->setName('construction-and-engineering');
        $manager->persist($category2);
        $category3 = new category();
        $category3->setName('residential-developments');
        $manager->persist($category3);
        $category4 = new category();
        $category4->setName('modular-buildings');
        $manager->persist($category4);
        $category5 = new category();
        $category5->setName('furniture-packs');
        $manager->persist($category5);

        $manager->flush();
        
        $this->addReference('category-1', $category1);
        $this->addReference('category-2', $category2);
        $this->addReference('category-3', $category3);
        $this->addReference('category-4', $category4);
        $this->addReference('category-5', $category5);
    }
    
    public function getOrder()
    {
        return 1;
    }

}