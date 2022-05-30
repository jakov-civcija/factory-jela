<?php

namespace App\Repository;

use App\Entity\Meals;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Configuration;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Translatable\Query\TreeWalker\TranslationWalker;
use Gedmo\Translatable\TranslatableListener;

/**
 * @extends ServiceEntityRepository<Meals>
 *
 * @method Meals|null find($id, $lockMode = null, $lockVersion = null)
 * @method Meals|null findOneBy(array $criteria, array $orderBy = null)
 * @method Meals[]    findAll()
 * @method Meals[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MealsRepository extends ServiceEntityRepository
{


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meals::class);
    }

    public function add(Meals $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        //TODO: Make sure one tag and one ingredient exists
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Meals $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getFilterQueryViaDoctrine(?int $categoryId, ?bool $hasToHaveCategory, array $tags, $lang, $with, $diffTime,int $page, int $per_page): Query
    {
        $limit = $per_page;
        $offset = ($page - 1) * $limit;

        $qb = $this->createQueryBuilder('m');
        $qb->setFirstResult($offset)
            ->setMaxResults($limit)
            /*
             * Morao sam joinat jer ako ne joinam library za prijevode neće prevesti prilikom lazy loadinga tijekom serijalizacije.
             * Zbog toga sam joinao da eager loadamo i onda se prijevodi popune pravilno
             */
            ->select('m', 'category', 'ingredients', 'tags')
            ->join('m.category', 'category')
            ->join('m.ingredients', 'ingredients')
            ->join('m.tags', 'tags');
        if ($categoryId !== null)
        {
            $qb
                ->andWhere('m.category = :cat')
                ->setParameter('cat', $categoryId);
        }
        if ($hasToHaveCategory !== null)
        {
            if ($hasToHaveCategory)
            {
                $qb->andWhere('m.category IS NOT NULL');
            }else{
                $qb->andWhere('m.category IS NULL');
            }
        }

        if (!empty($tags) )
        {
            /*
             * Ovo su tri načina kako postići zadani filter, gdje meal mora imati svaki zadani tag, opcije 1 i 2 uopće nisu u skladu sa ORM principima,
             * opciju 3 sam pokušao implementirat, ali doctrine jednostavno ne podržava filtriranje po slabom many to many entitetu jer za njega ne postoji klasa u kodu,
             * a doctrine ne dopušta da dodamo RAW SQL condition u $qb objekt.
             *
             *  SELECT id,status,category_id,title,description,deletedAt,created_at,updated_at
                FROM meals m JOIN meals_tags mt ON m.id = mt.meals_id WHERE tags_id IN (64,65)
                GROUP BY id,status,category_id,title,description,deletedAt,created_at,updated_at HAVING COUNT(*) >= 2;

                SELECT *
                FROM meals m WHERE m.id IN (
                    SELECT meals_id FROM meals_tags WHERE tags_id IN (64,65) GROUP BY meals_id HAVING COUNT(*) >= 2
                );

                SELECT *
                FROM meals m WHERE m.id IN (
                    SELECT meals_id FROM meals_tags WHERE tags_id = 65 AND meals_id = m.id
                )
                AND m.id IN (
                    SELECT meals_id FROM meals_tags WHERE tags_id = 64 AND meals_id = m.id
                );


             */

//            $i = 0;

//            foreach ($tags as $tag)
//            {
//                $tagParameter = "tag${i}";
//
//                $sqb = $this->getEntityManager()->createQueryBuilder();
//                $sqb->select('t')->from('meals_tags', 't')->andWhere("t.id = :${tagParameter}")->setParameter($tagParameter, $tag)->andWhere("t.meal_id = m.id");
//
//
//
//                // Your query builder:
//                $qb->andWhere($qb->expr()->exists($sqb->getDQL()));
//
//                $i++;
//            }
        }

        if ($diffTime !== null)
        {
            $currentTime = DateTime::createFromFormat( 'U', $diffTime );

            $qb->andWhere('m.createdAt >= :diffTime OR m.updatedAt >= :diffTime OR m.deletedAt >= :diffTime')
                ->setParameter('diffTime', $currentTime);
        }


//        var_dump($qb->getQuery());
        $query = $qb->getQuery();

        return $query;
    }

    /**
     * @param int|null $categoryId
     * @param bool|null $hasToHaveCategory
     * @param int[] $tags
     * @param $lang
     * @param $with
     * @param $diff_time
     * @param int $page
     * @param int $per_page
     * @return Meals[]
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function fetchByParams(?int $categoryId, ?bool $hasToHaveCategory, array $tags, $lang, $with, $diffTime,int $page, int $per_page): array
    {
        $query = $this->getFilterQueryViaDoctrine($categoryId, $hasToHaveCategory, $tags, $lang, $with, $diffTime, $page, $per_page);

        $query->setHint(
            Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );


//        $query->setHydrationMode(TranslationWalker::HYDRATE_OBJECT_TRANSLATION);
        $query->setHint(Query::HINT_REFRESH, true);


        if($lang !== 'en')
        {
            $query->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, $lang);
        }
        $results = $query->getResult();


        return $results;
    }

//    /**
//     * @return Meals[] Returns an array of Meals objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Meals
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

}
