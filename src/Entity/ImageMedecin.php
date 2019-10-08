<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageMedecinRepository")
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

    private $file;

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
    }

    public function upload(){
        if (null === $this->file){
            return;
        }

        //on recupere le nom d'origine du fichier
        $name = $this->file->getClientOriginalName();

        //on deplace le fichier d'origine vers le chemin voulu
        $this->file->move($this->getUploadRootDir(),$name);

        //on sauvegarde le nom de fichier dans l'url et alt
        $this->setUrl($name);
        $this->setAlt($name);


    }

    public function getUploadDir()
    {
        return 'uploads/image';
    }

    public function getUploadFile(){
        return $this->getUploadDir().'/'.$this->url;
    }

    private function getUploadRootDir(){
        return __DIR__.'/../../public/'.$this->getUploadDir();
    }


}
