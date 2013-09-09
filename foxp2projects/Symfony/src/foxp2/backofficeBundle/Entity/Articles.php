<?php

namespace foxp2\backofficeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;

/**
 * Articles
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="foxp2\backofficeBundle\Entity\ArticlesRepository")
 */
class Articles
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
     * @ORM\ManyToOne(targetEntity="foxp2\backofficeBundle\Entity\Categories")
     * @ORM\JoinColumn(name="categories_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $category;


    /**
     * @var string
     *
     * @ORM\Column(name="article_name", type="string", length=255)
     * @Assert\NotBlank(message = "Le libellé de l'article est obligatoire")
     */
    private $articleName;


    /**
     * @var string
     *
     * @ORM\Column(name="article_title", type="string", length=255)
     * @Assert\NotBlank(message = "Le titre de l'article est obligatoire")
     */
    private $articleTitle;


    /**
     * @var string
     *
     * @ORM\Column(name="article_sub_title", type="string", length=255, nullable=true)
     */
    private $articleSubTitle;


    /**
     * @var string
     *
     * @ORM\Column(name="article_short_description", type="text", nullable = true)
     */
    private $articleShortDescription;


    /**
     * @var string
     *
     * @ORM\Column(name="article_long_description", type="text")
     * @Assert\NotBlank(message = "Le description de l'article est obligatoire")
     */
    private $articleLongDescription;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="article_date_created", type="datetime")
     */
    private $articleDateCreated;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="article_date_modified", type="datetime", nullable = true)
     */
    private $articleDateModified;


    /**
     * @var integer
     *
     * @ORM\Column(name="article_gist_reference", type="integer", nullable = true)
     */
    private $articleGistReference;

    /**
     * Constructor
     */
    public function __construct()
    {        
        $this->setArticleDateCreated(new DateTime('now'));
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
     * Set categoriesId
     *
     * @param integer $categoriesId
     * @return Articles
     */
    public function setCategoriesId($categoriesId)
    {
        $this->categoriesId = $categoriesId;
    
        return $this;
    }


    /**
     * Get categoriesId
     *
     * @return integer 
     */
    public function getCategoriesId()
    {
        return $this->categoriesId;
    }


    /**
     * Set articleName
     *
     * @param string $articleName
     * @return Articles
     */
    public function setArticleName($articleName)
    {
        $this->articleName = $articleName;
    
        return $this;
    }


    /**
     * Get articleName
     *
     * @return string 
     */
    public function getArticleName()
    {
        return $this->articleName;
    }


    /**
     * Set articleTitle
     *
     * @param string $articleTitle
     * @return Articles
     */
    public function setArticleTitle($articleTitle)
    {
        $this->articleTitle = $articleTitle;
    
        return $this;
    }


    /**
     * Get articleTitle
     *
     * @return string 
     */
    public function getArticleTitle()
    {
        return $this->articleTitle;
    }


    /**
     * Set articleSubTitle
     *
     * @param string $articleSubTitle
     * @return Articles
     */
    public function setArticleSubTitle($articleSubTitle)
    {
        $this->articleSubTitle = $articleSubTitle;
    
        return $this;
    }


    /**
     * Get articleSubTitle
     *
     * @return string 
     */
    public function getArticleSubTitle()
    {
        return $this->articleSubTitle;
    }


    /**
     * Set articleShortDescription
     *
     * @param string $articleShortDescription
     * @return Articles
     */
    public function setArticleShortDescription($articleShortDescription)
    {
        $this->articleShortDescription = $articleShortDescription;
    
        return $this;
    }


    /**
     * Get articleShortDescription
     *
     * @return string 
     */
    public function getArticleShortDescription()
    {
        return $this->articleShortDescription;
    }


    /**
     * Set articleLongDescription
     *
     * @param string $articleLongDescription
     * @return Articles
     */
    public function setArticleLongDescription($articleLongDescription)
    {
        $this->articleLongDescription = $articleLongDescription;
    
        return $this;
    }


    /**
     * Get articleLongDescription
     *
     * @return string 
     */
    public function getArticleLongDescription()
    {
        return $this->articleLongDescription;
    }


    /**
     * Set articleDateCreated
     *
     * @param \DateTime $articleDateCreated
     * @return Articles
     */
    public function setArticleDateCreated($articleDateCreated)
    {
        $this->articleDateCreated = $articleDateCreated;
    
        return $this;
    }


    /**
     * Get articleDateCreated
     *
     * @return \DateTime 
     */
    public function getArticleDateCreated()
    {
        return $this->articleDateCreated;
    }


    /**
     * Set articleDateModified
     *
     * @param \DateTime $articleDateModified
     * @return Articles
     */
    public function setArticleDateModified($articleDateModified)
    {
        $this->articleDateModified = $articleDateModified;
    
        return $this;
    }


    /**
     * Get articleDateModified
     *
     * @return \DateTime 
     */
    public function getArticleDateModified()
    {
        return $this->articleDateModified;
    }


    /**
     * Set articleGistReference
     *
     * @param integer $articleGistReference
     * @return Articles
     */
    public function setArticleGistReference($articleGistReference)
    {
        $this->articleGistReference = $articleGistReference;
    
        return $this;
    }


    /**
     * Get articleGistReference
     *
     * @return integer 
     */
    public function getArticleGistReference()
    {
        return $this->articleGistReference;
    }


    /**
     * Set category
     *
     * @param \foxp2\backofficeBundle\Entity\Categories $category
     * @return Articles
     */
    public function setCategory(\foxp2\backofficeBundle\Entity\Categories $category = null)
    {
        $this->category = $category;
    
        return $this;
    }


    /**
     * Get category
     *
     * @return \foxp2\backofficeBundle\Entity\Categories 
     */
    public function getCategory()
    {
        return $this->category;
    }
}