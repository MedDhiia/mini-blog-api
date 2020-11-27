<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\BlogPost;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker ;
use App\Entity\Comment;

class AppFixtures extends Fixture
{   
    private $passwordEncoder ;
    private $faker ;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {

        /**
         * @var UserPasswordEncoderInterface
         */
        $this->passwordEncoder = $passwordEncoder;

        /**
         * @var \Faker\Factory
         */
        $this->faker = Faker\Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
        $this->loadComments($manager);
    }

    public function loadBlogPosts(ObjectManager $manager)
    {
        $user = $this->getReference('user_admin');

        for ($i=0; $i < 100 ; $i++) { 
            $blogPost = new BlogPost();
            $blogPost->setTitle($this->faker->realText(30));
            $blogPost->setPublished($this->faker->dateTimeThisYear());
            $blogPost->setAuthor($user);
            $blogPost->setContent($this->faker->realText());
            $blogPost->setSlug($this->faker->slug);

            $manager->persist($blogPost);
            $this->setReference("blog_post_$i", $blogPost);
        }
    
        $manager->flush();
    }
    
    public function loadComments(ObjectManager $manager)
    {
        $user = $this->getReference('user_admin');

        for ($i=0; $i < 100 ; $i++) { 
            for ($j=0; $j < rand(1, 10); $j++) { 
                $comments = new Comment();
                $comments->setContent($this->faker->realText());
                $comments->setPublished($this->faker->dateTimeThisYear());
                $comments->setAuthor($this->getReference('user_admin'));

                $manager->persist($comments);
            }
        }
    
        $manager->flush();
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
