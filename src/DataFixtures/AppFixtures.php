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
            'password' => 'Test_1',
            'roles' => [USER::ROLE_SUPERADMIN],
        ],
        [
            'username' => 'test_2',
            'email' => 'test_2@test.com',
            'name' => 'test_2',
            'password' => 'Test_2',
            'roles' => [USER::ROLE_WRITER],
        ],
        [
            'username' => 'test_3',
            'email' => 'test_3@test.com',
            'name' => 'test_3',
            'password' => 'Test_3',
            'roles' => [USER::ROLE_WRITER],
        ],
        [
            'username' => 'test_4',
            'email' => 'test_4@test.com',
            'name' => 'test_4',
            'password' => 'Test_4',
            'roles' => [USER::ROLE_EDITOR],
        ],
        [
            'username' => 'test_5',
            'email' => 'test_5@test.com',
            'name' => 'test_5',
            'password' => 'Test_5',
            'roles' => [USER::ROLE_COMMENTATOR],
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
            $authorReference = $this->getRandomUserReference($blogPost) ;
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
                $authorReference = $this->getRandomUserReference($comments) ;
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

            $user->setRoles($userFixture['roles']);
            $user->setIsEnable(true);

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
    protected function getRandomUserReference($entity) : User
    {
        $randomUser = self::USERS[rand(0, 4)];

        if ($entity instanceof BlogPost &&
            ! count(
                array_intersect(
                    $randomUser['roles'],
                    [
                        User::ROLE_SUPERADMIN,
                        User::ROLE_ADMIN,
                        User::ROLE_WRITER
                    ]
                )
            )
        ) {
            return $this->getRandomUserReference($entity);
        }

        if ($entity instanceof Comment &&
            ! count(
                array_intersect(
                    $randomUser['roles'],
                    [
                        User::ROLE_SUPERADMIN,
                        User::ROLE_ADMIN,
                        User::ROLE_WRITER,
                        USER::ROLE_COMMENTATOR
                    ]
                )
            )
        ) {
            return $this->getRandomUserReference($entity);
        }

        return $this->getReference('user_'.$randomUser['username']);
    }
}
