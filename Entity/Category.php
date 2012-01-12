<?php

// src/Blogger/BlogBundle/Entity/Comment.php

namespace Blogger\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @ORM\Entity(repositoryClass="Blogger\BlogBundle\Repository\CategoryRepository")
 * @ORM\Table(name="category")
 */
class Category {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @ORM\ManyToMany(targetEntity="Blog", mappedBy="categories")
   */
  protected $blogs;
  
  /**
   * @ORM\Column(type="text")
   */
  protected $name;

    public function __construct()
    {
        $this->blog = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function __toString() {
      return $this->getName();
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param text $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return text 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add blog
     *
     * @param Blogger\BlogBundle\Entity\Blog $blog
     */
    public function addBlog(\Blogger\BlogBundle\Entity\Blog $blog)
    {
        $this->blog[] = $blog;
    }

    /**
     * Get blog
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getBlog()
    {
        return $this->blog;
    }
}