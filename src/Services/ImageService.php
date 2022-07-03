<?php

namespace App\Services ;

use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageService extends AbstractController{

    public function moveImage(UploadedFile $file , Produit $produit ) :void{
        $dossier_upload = $this->getParameter("upload_directory");
        $photo = md5(uniqid()) . "." . $file->guessExtension(); // .jpg
        $file->move( $dossier_upload , $photo  ); 
        $produit->setPhoto($photo);
    }

    public function deleteImage(Produit $produit) :void{
         // suppression du fichier dans le dossier upload
         $dossier_upload = $this->getParameter("upload_directory");
         $photo = $produit->getPhoto();
         $oldPhoto = $dossier_upload . "/" . $photo ;
         if(file_exists($oldPhoto)){
             unlink($oldPhoto); 
         }
         // fin suppression du fichier dans le dossier upload
    }

    public function updateImage(UploadedFile $file ,Produit $produit) :void{
        $this->deleteImage($produit);
        $this->moveImage($file , $produit );
    }

}