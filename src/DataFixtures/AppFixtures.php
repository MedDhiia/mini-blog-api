<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker ;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder ;
    private $faker ;
    const USERS = [
        [
            'username' => 'test_1',
            'email' => 'test_1@test.com',
            'name' => 'test_1',
            'password' => 'Test_1'
        ],
        [
            'username' => 'test_2',
            'email' => 'test_2@test.com',
            'name' => 'test_2',
            'password' => 'Test_2'
        ],
        [
            'username' => 'test_3',
            'email' => 'test_3@test.com',
            'name' => 'test_3',
            'password' => 'Test_3'
        ]
    ];

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
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
        for ($i = 0; $i < 100 ; $i++) {
            $blogPost = new BlogPost();
            $blogPost->setTitle($this->faker->realText(30));
            $blogPost->setPublished($this->faker->dateTimeThisYear());
            $authorReference = $this->getRandomUserReference() ;
            $blogPost->setAuthor($authorReference);
            $blogPost->setContent($this->faker->realText());
            $blogPost->setSlug($this->faker->slug);

            $manager->persist($blogPost);
            $this->setReference("blog_post_$i", $blogPost);
        }

        $manager->flush();
    }

    public function loadComments(ObjectManager $manager)
    {
        for ($i = 0; $i < 100 ; $i++) {
            for ($j = 0; $j < rand(1, 10); $j++) {
                $comments = new Comment();
                $comments->setContent($this->faker->realText());
                $comments->setPublished($this->faker->dateTimeThisYear());
                $authorReference = $this->getRandomUserReference() ;
                $comments->setAuthor($authorReference);
                $comments->setBlogPost($this->getReference("blog_post_$i"));

                $manager->persist($comments);
            }
        }

        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        foreach (self::USERS as $userFixture) {
            $user = new User();
            $user->setEmail($userFixture['email']);
            $user->setName($userFixture['name']);
            $user->setUsername($userFixture['username']);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $userFixture['password']
            ));

            $this->addReference('user_'.$userFixture['username'], $user);
            $manager->persist($user);
        }
        $manager->flush($user);
    }

    /**
     * Select Random user.
     *
     * @return User
     */
    protected function getRandomUserReference() : User
    {
        return $this->getReference('user_'.self::USERS[rand(0, 2)]['username']) ;
    }
}
