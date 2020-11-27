<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\BlogPost;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $blogPost = new BlogPost();
        $blogPost->setTitle('A first post');
        $blogPost->setPublished(new \DateTime('2020-11-13 08:05:00'));
        $blogPost->setAuthor('Med Dhia Bel Karoui');
        $blogPost->setContent('My First Post');
        $blogPost->setSlug('first-post');
        $manager->persist($blogPost);

        $blogPost = new BlogPost();
        $blogPost->setTitle('A Second post');
        $blogPost->setPublished(new \DateTime('2020-11-13 08:05:00'));
        $blogPost->setAuthor('Med Dhia Bel Karoui');
        $blogPost->setContent('My Second Post');
        $blogPost->setSlug('second-post');
        $manager->persist($blogPost);

        $manager->flush();
    }
}
