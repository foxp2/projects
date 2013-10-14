<?php

namespace foxp2\backofficeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Gistcomments
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="foxp2\backofficeBundle\Entity\GistcommentsRepository")
 */
class Gistcomments
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
     * @var integer
     *
     * @ORM\Column(name="id_gist", type="integer")
     */
    private $idGist;

    /**
     * @var string
     * 
     * @ORM\Column(name="filename_gist", type="string", length=255)
     */
    private $filenameGist;

    /**
     * @var string
     *
     * @ORM\Column(name="comments", type="text")
     * @Assert\NotBlank(message = "Le commentaire est obligatoire")
     */
    private $comments;


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
     * Set idGist
     *
     * @param integer $idGist
     * @return Gistcomments
     */
    public function setIdGist($idGist)
    {
        $this->idGist = $idGist;
    
        return $this;
    }

    /**
     * Get idGist
     *
     * @return integer 
     */
    public function getIdGist()
    {
        return $this->idGist;
    }

    /**
     * Set filenameGist
     *
     * @param string $filenameGist
     * @return Gistcomments
     */
    public function setFilenameGist($filenameGist)
    {
        $this->filenameGist = $filenameGist;
    
        return $this;
    }

    /**
     * Get filenameGist
     *
     * @return string 
     */
    public function getFilenameGist()
    {
        return $this->filenameGist;
    }

    /**
     * Set comments
     *
     * @param string $comments
     * @return Gistcomments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    
        return $this;
    }

    /**
     * Get comments
     *
     * @return string 
     */
    public function getComments()
    {
        return $this->comments;
    }
}
