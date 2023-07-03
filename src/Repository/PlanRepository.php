<?php

namespace App\Repository;

use App\Entity\Plan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Plan>
 *
 * @method Plan|null find($id, $lockMode = null, $lockVersion = null)
 * @method Plan|null findOneBy(array $criteria, array $orderBy = null)
 * @method Plan[]    findAll()
 * @method Plan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanRepository extends ServiceEntityRepository
{
    private $conn;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plan::class);
        $this->conn = $this->getEntityManager()->getConnection();
    }

    public function save(Plan $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Plan $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function searchUser(int $user_id): bool
    {
        $sql = "SELECT * FROM user WHERE user_id={$user_id}";
        $stmt = $this->conn->executeQuery($sql);

        if($stmt->rowCount() > 0) {
            return True;
        }
        return False;
    }

    public function fetchAllPlans(int $user_id): ?array
    {   
        $sql = "SELECT plan.* FROM user_in_plan 
                INNER JOIN plan ON user_in_plan.plan_id=plan.plan_id 
                WHERE user_in_plan.user_id={$user_id}";
        $stmt = $this->conn->executeQuery($sql);

        return $stmt->fetchAllAssociative();
    }

    public function deletePlan(int $id): void
    {
        $sql = "DELETE FROM plan WHERE planId={$id}";
        $stmt = $this->conn->executeQuery($sql);
    }

//    /**
//     * @return Plan[] Returns an array of Plan objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Plan
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
