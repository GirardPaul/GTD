<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 * @UniqueEntity(
 *     fields={"username"},
 *     message="Cet identifiant existe déjà, Veuillez vous connecter"
 * )
 */
class Users implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Les mots de passe ne sont pas identiques")
     */
    private $passwordVerification;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $roles;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $favoritePokemon;

    /**
     * @ORM\ManyToOne(targetEntity=Genre::class, inversedBy="users")
     */
    private $genre;

    /**
     * @ORM\OneToMany(targetEntity=Posts::class, mappedBy="user")
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity=Comments::class, mappedBy="user")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=Friendship::class, mappedBy="sender")
     */
    private $sender;

    /**
     * @ORM\OneToMany(targetEntity=Friendship::class, mappedBy="target")
     */
    private $target;

    /**
     * @ORM\OneToMany(targetEntity=PostsReactions::class, mappedBy="user")
     */
    private $postsReactions;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->sender = new ArrayCollection();
        $this->target = new ArrayCollection();
        $this->postsReactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPasswordVerification(): ?string
    {
        return $this->passwordVerification;
    }

    public function setPasswordVerification(string $passwordVerification): self
    {
        $this->passwordVerification = $passwordVerification;

        return $this;
    }

    public function getRoles()
    {
        // TODO: Implement getRoles() method.
        return [$this->roles];

    }


    public function setRoles(string $roles): self
    {
        if ($roles === null) {
            $this->roles = "ROLE_USER";
        } else {
            $this->roles = $roles;
        }

        return $this;
    }

    public function getFavoritePokemon(): ?string
    {
        return $this->favoritePokemon;
    }

    public function setFavoritePokemon(string $favoritePokemon): self
    {
        $this->favoritePokemon = $favoritePokemon;

        return $this;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(?Genre $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * @return Collection|Posts[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Posts $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Posts $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Friendship[]
     */
    public function getSender(): Collection
    {
        return $this->sender;
    }

    public function addSender(Friendship $sender): self
    {
        if (!$this->sender->contains($sender)) {
            $this->sender[] = $sender;
            $sender->setSender($this);
        }

        return $this;
    }

    public function removeSender(Friendship $sender): self
    {
        if ($this->sender->removeElement($sender)) {
            // set the owning side to null (unless already changed)
            if ($sender->getSender() === $this) {
                $sender->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Friendship[]
     */
    public function getTarget(): Collection
    {
        return $this->target;
    }

    public function addTarget(Friendship $target): self
    {
        if (!$this->target->contains($target)) {
            $this->target[] = $target;
            $target->setTarget($this);
        }

        return $this;
    }

    public function removeTarget(Friendship $target): self
    {
        if ($this->target->removeElement($target)) {
            // set the owning side to null (unless already changed)
            if ($target->getTarget() === $this) {
                $target->setTarget(null);
            }
        }

        return $this;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return Collection|PostsReactions[]
     */
    public function getPostsReactions(): Collection
    {
        return $this->postsReactions;
    }

    public function addPostsReaction(PostsReactions $postsReaction): self
    {
        if (!$this->postsReactions->contains($postsReaction)) {
            $this->postsReactions[] = $postsReaction;
            $postsReaction->setUser($this);
        }

        return $this;
    }

    public function removePostsReaction(PostsReactions $postsReaction): self
    {
        if ($this->postsReactions->removeElement($postsReaction)) {
            // set the owning side to null (unless already changed)
            if ($postsReaction->getUser() === $this) {
                $postsReaction->setUser(null);
            }
        }

        return $this;
    }
}
