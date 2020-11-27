<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\BlogPost;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{   
    private $passwordEncoder ;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
    }

    public function loadBlogPosts(ObjectManager $manager)
    {
        $user = $this->getReference('user_admin');

        $blogPost = new BlogPost();
        $blogPost->setTitle('A first post');
        $blogPost->setPublished(new \DateTime('2020-11-13 08:05:00'));
        $blogPost->setAuthor($user);
        $blogPost->setContent('My First Post');
        $blogPost->setSlug('first-post');
        $manager->persist($blogPost);

        $blogPost = new BlogPost();
        $blogPost->setTitle('A Second post');
        $blogPost->setPublished(new \DateTime('2020-11-13 08:05:00'));
        $blogPost->setAuthor($user);
        $blogPost->setContent('My Second Post');
        $blogPost->setSlug('second-post');
        $manager->persist($blogPost);

        $manager->flush();
    }
    
    public function loadComments(ObjectManager $manager)
    {
        # code...
    }
    
    public function loadUsers (ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('admin@blog.tn');
        $user->setName('Bel Karoui Med Dhia');
        $user->setUsername('admin');
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'admin'
        ));

        $this->addReference('user_admin', $user);

        $manager->persist($user);
        $manager->flush($user);
    }
}
