<?php

namespace App\Entity;

use App\Entity\User ;
use App\Entity\Comment ;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BlogPostRepository;
use App\Entity\AuthoredEntityInterface;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\PublishedDateEntityInterface;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups ;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert ;

/**
 * @ORM\Entity(repositoryClass=BlogPostRepository::class)
 * @ApiResource(
 *      itemOperations ={
 *          "get"={
 *              "normalization_context"={
 *                  "groups"={"get-blog-post-with-author"}
 *              }
 *          },
 *          "put"={
 *              "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object.getAuthor() == user"
 *          }
 *      },
 *      collectionOperations={
 *          "get"={
 *              "normalization_context"={
 *                  "groups"={"get-blog-post-with-author"}
 *              }
 *          },
 *          "post"={
 *              "access_control"="is_granted('IS_AUTHENTICATED_FULLY')"
 *          }
 *      },
 *      denormalizationContext={
 *          "groups"={"post"}
 *      } 
 * )
 */
class BlogPost implements AuthoredEntityInterface, PublishedDateEntityInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get-blog-post-with-author"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post", "get-blog-post-with-author"})
     * @Assert\NotBlank()
     * @Assert\Length(min=10)
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"get-blog-post-with-author"})
     */
    private $published;

    /**
     * @ORM\Column(type="text")
     * @Groups({"post", "get-blog-post-with-author"})
     * @Assert\NotBlank()
     * @Assert\Length(min=20)
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get-blog-post-with-author"})
     */
    private $author ;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="blogPost")
     * @ApiSubresource()
     */
    private $comments ;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"post", "get-blog-post-with-author"})
     */
    private $slug;

    public function __construct()
    {
        $this->comments = new ArrayCollection( );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPublished(): ?\DateTimeInterface
    {
        return $this->published;
    }

    public function setPublished(\DateTimeInterface $published): PublishedDateEntityInterface
    {
        $this->published = $published;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(UserInterface $author): AuthoredEntityInterface 
    {
        $this->author = $author;

        return $this;
    }
    
    /**
     * @return Collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }


}
