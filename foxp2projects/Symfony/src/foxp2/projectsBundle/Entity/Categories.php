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
    
    
    public function __construct() 
    {
        
        $this->children = new ArrayCollection();
        $this->setDateCreated(new DateTime('now'));
        
         
    }
    /**
     * Get Id
     *
     * @return $integer
     */    
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getCategoriesChildren() {
        
        return $this->children;
        
    }
    
    /**
     * Get Level
     * 
     * 
     * @return text level
     */
    public function getLevel() 
    {
        return $this->level;
    }
    /**
     * Get name
     *
     * @return string 
     */
    public function getCategoriesName()
    {
        return $this->categoriesName;
    }
    
    /**
     * Set categoryName
     *
     * @param string $categoryName
     * @return categories
     */
    public function setCategoriesName($categoryName)
    {
        $this->categoriesName = $categoryName;
    
        return $this;
    }
    /**
     * Set parentId
     *
     * @param $parentId
     * @return categories
     */
    public function setParentId($parentId)
    {

        $this->parentId = $parentId;        
        
        return $this; 
        
    }

    /**
     * Get parentId
     *
     * @return integer $parentId
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set dateCreated
     *
     * @param datetime $dateCreated
     * @return categories
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    
        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return datetime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }
    
    /**
     * Set dateModified
     *
     * @param datetime $dateModified
     * @return categories
     */
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;
    
        return $this;
    }

    /**
     * Get dateModified
     *
     * @return datetime
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }
    
    /**
     * Set title
     * 
     * @param string $title
     * return categoriesTitle
     */
    public function setcategoriesTitle($title)
    {
        $this->categoriesTitle = $title;
        
        return $this;
    }
    
    /**
     * Get Title
     * 
     * @return categoriesTitle
     */
    public function getcategoriesTitle()
    {
        return $this->categoriesTitle;
    }
    
    /**
     * Set subtitle
     * 
     * @param string $subtitle
     * return categoriesSubTitle
     */
    public function setcategoriesSubTitle($subtitle)
    {
        $this->categoriesSubTitle = $subtitle;
        
        return $this;
    }
    
    /**
     * Get subTitle
     * 
     * @return categoriesSubTitle
     */
    public function getcategoriesSubTitle()
    {
        return $this->categoriesSubTitle;
    }
    
    /**
     * Set description
     * 
     * @param text $description
     * @return categories
     */
    public function setcategoriesDescription($description)
    {
        $this->categoriesDescription = $description;
        
        return $this;
    }
    
    /**
     * Get description
     * 
     * @return categories description
     */
    public function getcategoriesDescription()
    {
        return $this->categoriesDescription;
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