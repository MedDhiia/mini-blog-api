<?php

namespace App\Entity;

use App\Entity\User ;
use App\Entity\BlogPost ;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommentRepository;
use App\Entity\AuthoredEntityInterface;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\PublishedDateEntityInterface;
use Symfony\Component\Serializer\Annotation\Groups ;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert ;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 * @ApiResource(
 *      itemOperations ={
 *          "get",
 *          "put"={
 *              "access_control"="is_granted('ROLE_EDITOR') or (is_granted('ROLE_COMMENTATOR') and object.getAuthor() == user)"
 *          }
 *      },
 *      collectionOperations={
 *          "get",
 *          "post"={
 *              "access_control"="is_granted('ROLE_COMMENTATOR')"
 *          }
 *      },
 *      subresourceOperations={
 *          "api_blog_posts_comments_get_subresource"={
 *              "normalization_context"={
 *                  "groups"={"get-comment-with-author"}
 *              }
 *          }
 *      },
 *      denormalizationContext={
 *          "groups"={"post"}
 *      }
 * )
 */
class Comment implements AuthoredEntityInterface, PublishedDateEntityInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post", "get-comment-with-author"})
     * @Assert\NotBlank()
     * @Assert\Length(max=3000)
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"get-comment-with-author"})
     */
    private $published;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get-comment-with-author"})
     */
    private $author ;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BlogPost", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"post"})
     */
    private $blogPost ;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublished(): ?\DateTimeInterface
    {
        return $this->published;
    }

    public function setPublished(?\DateTimeInterface $published): PublishedDateEntityInterface
    {
        $this->published = $published;

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
     * @return BlogPost
     */
    public function getBlogPost(): BlogPost
    {
        return $this->blogPost;
    }

    public function setBlogPost(BlogPost $blogPost): self 
    {
        $this->blogPost = $blogPost;

        return $this;
    }

}
