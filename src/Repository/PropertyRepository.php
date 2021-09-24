<?php

namespace App\Repository;

use Doctrine\ORM\Query;
use App\Entity\Property;
use App\Entity\PropertySearch;
use App\Controller\PropertyController;
use App\Form\PropertySearchType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }
    
    /**
     * @return Query
     */
    public function findAllVisibleQuery(PropertySearch $search): Query
    {
        $query = $this->findVisibleQuery();

        if ($search->getMaxPrice()){
            $query = $query
            ->andWhere('p.price < :maxprice')
            ->setparameter('maxprice', $search->getMaxPrice());
        }
        if ($search->getMinSurface()){
            $query = $query
            ->andWhere('p.surface >= :minsurface')
            ->setparameter('minsurface', $search->getMinSurface());
        }

        if ($search->getOptions()->count() > 0) {
                $k=0;
                foreach($search->getOptions() as $option){
                    $k++;
                    $query = $query
                        ->andwhere(":option$k MEMBER OF p.options")
                        ->setParameter("option$k", $option);
                }
        }

        return $query->getQuery();
    }   

    /**
     * @return Property[]
     */
    public function findLatest(): array
    {
        return $this->findVisibleQuery() // ici on veut récupérer les property ou le sold est égal à false. 
            ->setMaxResults(4) // ici on dit qu'on veut seuelement 4 résultats 
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Property[] Returns an array of Property objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Property
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    private function findVisibleQuery()
    {
        return $this->createQueryBuilder('p')
            ->where('p.sold=false');
    }

}
