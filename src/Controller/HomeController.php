<?php 
namespace App\Controller;

use App\Repository\PropertyRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController{ // abstracontroller a accès à tous les services de symfony
    
    // /**
    //  * @var Environment
    //  */
    // private $twig;
    
    // public function __construct(Environment $twig)

    // {
    //  $this->twig = $twig; 
    // } tout ca plus :    return new Response($this->twig->render('pages/home.html.twig'));
    // peut être remplacé par la méthode render
    
    
    /**
     * @Route("/", name="home")
     * @param PropertyRepository $repository
     * @return Response
     */
    public function index(PropertyRepository $repository) : Response
    {
        $properties = $repository->findLatest();
        return $this->render('pages/home.html.twig',[
            'properties' => $properties
        ]);
    }
}