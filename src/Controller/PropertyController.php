<?php

namespace App\Controller;

use App\Entity\Property;
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;
 use App\Repository\PropertyRepository;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Contraints as Assert;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class PropertyController extends AbstractController
{
    public function __construct(PropertyRepository $repository, EntityManagerInterface $em) // ici lui mettait objetManager plutot que EntityManagerInterface
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /** 
     * @Route("/biens", name="property.index")
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $search = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $search);
        $form->handleRequest($request);

        $properties = $paginator->paginate(
            $this->repository->findAllVisibleQuery($search), 
            $request->query->getInt('page', 1),
            12
        );
        return $this->render('property/index.html.twig', [
            'current_menu' => 'properties',
            'properties' => $properties,
            'form' => $form->createView()
        ]);


        // $property= new Property();
        // $property->setTitle('Mon Premier Titre')
        // ->setPrice(200000)
        // ->setRooms(3)
        // ->setBedrooms(3)
        // ->setDescription('Une petite description')
        // ->setSurface(60)
        // ->setFloor(4)
        // ->setHeat(1)
        // ->setCity('Lille')
        // ->setAdress('12 boulevard Gambetta')
        // ->setPostalCode('59000'); 
        // // pour envoyer ça dans la BDD il nous faut un Entity Manager : c'est une classe qui gère les entités et qui gère leur persistance au sein de la BDD. 
        // $em = $this->getDoctrine()->getManager();
        // $em -> persist($property);
        // $em ->flush();


    }

    /**
     * @Route("/biens/{slug}-{id}", name="property.show", requirements={"slug": "[a-z0-9\-]*"})
     * @return Response
     */
    public function show(Property $property, string $slug): Response
    {
        if ($property->getSlug() !== $slug) {
            return $this->redirectToRoute('property.show', [ // la meme en cas d'érreur d'URL on nous dirige sur la bonne route
                'id' => $property->getId(),
                'slug' => $property->getSlug()
            ], 301);
        }
        return $this->render('property/show.html.twig', [
            'property' => $property,
            'current_menu' => 'properties'
        ]);
    }
}
