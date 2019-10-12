<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageMedecinRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ImageMedecin
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $alt;

    /**
     * @Assert\Image(maxSize="2M",maxSizeMessage="la taille du fichier ne doit pas dépasser 2Mo",mimeTypesMessage="le fichier sélectionné doit être une image")
     */
    private $file;

    private $tempfilename;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url)
    {
        $this->url = $url;

    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(string $alt)
    {
        $this->alt = $alt;

    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file): void
    {
        $this->file = $file;

        if (null !== $this->url){
            $this->tempfilename = $this->url;
            $this->url = null;
            $this->alt = null;
        }

    }

    public function getImageName(){
       return $this->id.'.'.$this->url;
    }
    public function getTempImageName(){
        return $this->id.'.'.$this->tempfilename;
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload(){
        // s'il n'existe pas de fichier
        if (null === $this->file){
            return;
        }

        if (null !== $this->tempfilename){
            $oldFile = $this->getUploadRootDir().'/'.$this->getTempImageName();

            if (file_exists($oldFile)){
                unlink($oldFile);
            }
        }


        //on deplace le fichier d'origine vers le chemin voulu
        $this->file->move($this->getUploadRootDir(),$this->getImageName());


    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function setAltUrl(){
        if (null === $this->file){
            return;
        }

        //on sauvegarde le nom de fichier dans l'url et alt
        $this->url = $this->file->guessExtension();
        $this->alt = $this->file->getClientOriginalName();
    }

    /**
     * @ORM\PreRemove()
     */
    public function stockFileDir(){
        $this->tempfilename = $this->getUploadRootDir().'/'.$this->getImageName();
    }

    /**
     * @ORM\PostRemove()
     */
    public function deleteFile(){
        if (file_exists($this->tempfilename)){
            unlink($this->tempfilename);
        }
    }

    public function getUploadDir()
    {
        return 'uploads/image';
    }

    public function getUploadFile(){
        return $this->getUploadDir().'/'.$this->getImageName();
    }

    private function getUploadRootDir(){
        return __DIR__.'/../../public/'.$this->getUploadDir();
    }


}
