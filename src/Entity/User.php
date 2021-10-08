<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User 
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=16)
     * @Assert\NotBlank
     */
    private $username;

    /**
     * Get the id
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Get the username
     */
    public function getUsername() : string
    {
        return $this->username;
    
    }

    /**
     * Set username
     */
    public function setUsername(?string $username) : void
    {
        $this->username = $username;
    }
}