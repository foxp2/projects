<?php

namespace foxp2\projectsBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * categories
 * @ORM\Table(name="categories",indexes={
 *      @ORM\Index(name="categories_parent_id_idx", columns={"parent_id"})
 * })
 * @ORM\Entity(repositoryClass="foxp2\projectsBundle\Entity\CategoriesRepository")
 */
class Categories
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id     
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="foxp2\projectsBundle\Entity\Categories", mappedBy="parentId",cascade={"remove"})  
     * 
     */
    private $children;

    /**
     * @var $parentId
     * 
     * @ORM\ManyToOne(targetEntity="foxp2\projectsBundle\Entity\Categories", inversedBy="children")     
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id",nullable = true)    
     */
    
    private $parentId;
    
    /**
     *
     * @var $level
     * @ORM\Column(name="level", type="text", nullable = true)
     */
    private $level;
    
    /**
     * @var $categories_name
     * @Assert\NotBlank(message = "Le nom de la catégorie est obligatoire")
     * @ORM\Column(name="categories_name", type="string", length=255)     
     */
    private $categoriesName;
    
    /**
     * @var $date_created
     *
     * @ORM\Column(name="date_created", type="datetime")
     */
    private $dateCreated;

    /**
     * @var $date_modified
     *
     * @ORM\Column(name="date_modified", type="datetime", nullable = true)
     */
    private $dateModified;   
    
    /**
     *
     * @var $categoriesTitle
     * @Assert\NotBlank(message = "Le titre de la catégorie est obligatoire")
     * @ORM\Column(name="categories_title", type="string", length=255)     
     */
    
    private $categoriesTitle;

    /**
     *
     * @var $categoriesSubTitle
     * 
     * @ORM\Column(name="categories_sub_title", type="string", length=255, nullable = true)       
     */
    
    private $categoriesSubTitle;
    
    /**
     * @var $categoriesDescription
     * 
     * @ORM\Column(name="categories_description", type="text", nullable = true)
     */    
    private $categoriesDescription;    
    
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();        
        $this->setDateCreated(new DateTime('now'));
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
     * Set level
     *
     * @param string $level
     * @return Categories
     */
    public function setLevel($level)
    {
        $this->level = $level;
    
        return $this;
    }

    /**
     * Get level
     *
     * @return string 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set categoriesName
     *
     * @param string $categoriesName
     * @return Categories
     */
    public function setCategoriesName($categoriesName)
    {
        $this->categoriesName = $categoriesName;
    
        return $this;
    }

    /**
     * Get categoriesName
     *
     * @return string 
     */
    public function getCategoriesName()
    {
        return $this->categoriesName;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Categories
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    
        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set dateModified
     *
     * @param \DateTime $dateModified
     * @return Categories
     */
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;
    
        return $this;
    }

    /**
     * Get dateModified
     *
     * @return \DateTime 
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * Set categoriesTitle
     *
     * @param string $categoriesTitle
     * @return Categories
     */
    public function setCategoriesTitle($categoriesTitle)
    {
        $this->categoriesTitle = $categoriesTitle;
    
        return $this;
    }

    /**
     * Get categoriesTitle
     *
     * @return string 
     */
    public function getCategoriesTitle()
    {
        return $this->categoriesTitle;
    }

    /**
     * Set categoriesSubTitle
     *
     * @param string $categoriesSubTitle
     * @return Categories
     */
    public function setCategoriesSubTitle($categoriesSubTitle)
    {
        $this->categoriesSubTitle = $categoriesSubTitle;
    
        return $this;
    }

    /**
     * Get categoriesSubTitle
     *
     * @return string 
     */
    public function getCategoriesSubTitle()
    {
        return $this->categoriesSubTitle;
    }

    /**
     * Set categoriesDescription
     *
     * @param string $categoriesDescription
     * @return Categories
     */
    public function setCategoriesDescription($categoriesDescription)
    {
        $this->categoriesDescription = $categoriesDescription;
    
        return $this;
    }

    /**
     * Get categoriesDescription
     *
     * @return string 
     */
    public function getCategoriesDescription()
    {
        return $this->categoriesDescription;
    }

    /**
     * Add children
     *
     * @param \foxp2\projectsBundle\Entity\Categories $children
     * @return Categories
     */
    public function addChildren(\foxp2\projectsBundle\Entity\Categories $children)
    {
        $this->children[] = $children;
    
        return $this;
    }

    /**
     * Remove children
     *
     * @param \foxp2\projectsBundle\Entity\Categories $children
     */
    public function removeChildren(\foxp2\projectsBundle\Entity\Categories $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }


    /**
     * Set parentId
     *
     * @param \foxp2\projectsBundle\Entity\Categories $parentId
     * @return Categories
     */
    public function setParentId(\foxp2\projectsBundle\Entity\Categories $parentId = null)
    {
        $this->parentId = $parentId;
    
        return $this;
    }

    /**
     * Get parentId
     *
     * @return \foxp2\projectsBundle\Entity\Categories 
     */
    public function getParentId()
    {
        return $this->parentId;
    }
    
    /**
     * Get id 
     * @return id magic method
     */
    public function __toString()
    {
        return strval($this->id);
    }
}