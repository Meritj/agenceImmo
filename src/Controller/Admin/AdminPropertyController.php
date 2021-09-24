<?php

namespace App\Controller\Admin;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Form\OptionType;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class AdminPropertyController extends AbstractController
{
    /**
     * @var PropertyRepository
     */
    private $repository;

    public function __construct(PropertyRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/admin", name="admin.property.index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $properties = $this->repository->findAll();
        return $this->render('admin/property/index.html.twig', compact('properties'));
    }

 /**
  * @Route("/admin/property/create", name="admin.property.new")
  *
  */
    public function new(Request $request)
    {
        $property = new Property();
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {     
            $this->em->persist($property);    // ici avec le persist on va créer la donnée      
            $this->em->flush();
            $this->addFlash('success', 'Bien créé avec succès');

            return $this->redirectToRoute('admin.property.index');
        }
        return $this->render('admin/property/new.html.twig',[
            'property'=>$property,
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/admin/property/{id}", name="admin.property.edit", methods="GET|POST")
     * @param Property $property
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Property $property, Request $request)
    {   
        // $option = new Option();
        // $property->addOption($option); // ici on ajoute les options

        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);
        // ici on va voir si le formulaire est bien rempli 
        if ($form->isSubmitted() && $form->isValid()) {         // si il est bien rempli et valide 
            $this->em->flush();
            $this->addFlash('success', 'Bien modifié avec succès');
            return $this->redirectToRoute('admin.property.index');
        }
        return $this->render('admin/property/edit.html.twig', [
            'property' => $property,
            'form' => $form->createView() // ici on crée une vue du tableau : on va le voir dans le admin edit twig
        ]);
    }

    /**
     * @Route("/admin/property/delete/{id}", name="admin.property.delete", methods="DELETE|POST")
     * @param Property $property
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
     public function delete(Property $property, Request $request) 
    {
        if($this->isCsrfTokenValid('delete' . $property->getId(), $request->get('_token'))){
            $this->em->remove($property);
            $this->em->flush(); 
            $this->addFlash('success', 'Bien supprimé avec succès');
        }   
       return $this->redirectToRoute('admin.property.index');
    }







    // /**
    //  * @Route("/admin/property/{id}", name="admin.property.delete", methods="DELETE")
    //  * @param Property $property
    //  * @return \Symfony\Component\HttpFoundation\RedirectResponse
    //  */
    // public function delete(Property $propert, Request $request){
    //     if ($this->isCsrfTokenValid('delete' . $property->getId()))
    // }
    
    
}
