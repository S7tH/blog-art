<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

//call this to Upload files
use Symfony\Component\HttpFoundation\File\UploadedFile;

//use annotation with alias Assert to fixe the valides rules
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Image
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255)
     */
    private $alt;

    /*any doctrine anotation because it isn't this var
    who will be persited by doctrine for our file
    We just stock the name of our file temporarily*/
    /** 
     * @var UploadedFile
     *
     */
    private $file;
   
    /*attribut for save temporarily the name of file
    before deleting of trick with this file.*/
    private $tempFilename;


    public function __construct()
    {
        $this->specimen = false;
    }
  
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Image
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set alt
     *
     * @param string $alt
     *
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;

        //Check if we already have a file for this entity
        if (null !== $this->url)
        {
        //Save the file's name (extension) for delete it later
         $this->tempFilename = $this->url;

        //Reset the values 
        $this->url = null;
        $this->alt = null;

        }
    }
 
    /**
    * @ORM\PrePersist()
    * @ORM\PreUpdate()
    */
    public function preUpload()
    {
        //if ever there isn't any file (require optional), any action 
        if (null === $this->file) 
        {
            return;
        }

        //The id of this file is its name  (the name of url = extension of file here)
        $this->url = $this->file->guessExtension();

        //Generating the attribut alt of the markup <img>, with the name of client's value in his pc.
        $this->alt = $this->file->getClientOriginalName();
    }

    /**
    * @ORM\PostPersist()
    * @ORM\PostUpdate()
    */
    public function upload()
    {
        // if there isn't any file we do anything
        if (null === $this->file)
        {
             return;
        }

        //if we have an old file, we delete it
        if (null !== $this->tempFilename)
        {
            $oldFile = $this->getUploadRootDir().'/'.$this->id.'.'.$this->tempFilename;
            if (file_exists($oldFile))
            {
                unlink($oldFile);
            }
        }

        // we are moving the send file in the repository of our choice.
        $this->file->move(
            $this->getUploadRootDir(), // The repository of destination
            $this->id.'.'.$this->url   // the name of file to create, here « id.extension »
        );
    }

    /**
     * @ORM\PreRemove()
    */
    public function preRemoveUpload()
    {
        //Save temporarily the file's name , because it depends of the id
        $this->tempFilename = $this->getUploadRootDir().'/'.$this->id.'.'.$this->url;
    }

    /**
    * @ORM\PostRemove()
    */
    public function removeUpload()
    {
        // we have not acces to the id in PostRemove, so we use the save name
        if (file_exists($this->tempFilename))
        {
            //Delete the file
            unlink($this->tempFilename);
        }
  }

    public function getUploadDir()
    {
        // we return the relative path to the image for a navigator
        return 'uploads/img';
    }

    protected function getUploadRootDir()
    {
        // we return the relative path to the image for our PHP code
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    //method for write only WebPath for url as var on our Twig view
    public function getWebPath()
    {
        return $this->getUploadDir().'/'.$this->getId().'.'.$this->getUrl();
    }
}
