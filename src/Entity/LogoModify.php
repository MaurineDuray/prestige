<?php 

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class LogoModify{

    #[Assert\Image(mimeTypes:["image/png","image/jpeg","image/jpg","image/gif"], mimeTypesMessage:"Vous devez upload un fichier jpg, jpeg, png ou gif")]
    #[Assert\File(maxSize:"1024k", maxSizeMessage:"La taille du fichier est trop grande")]
    private ?string $newLogo = null;

    public function getNewLogo(): ?string
    {
        return $this->newLogo;
    }

    public function setNewLogo(?string $newLogo): self
    {
        $this->newLogo = $newLogo;

        return $this;
    }
}
