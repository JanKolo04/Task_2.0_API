<?php

namespace App\Repository;

use App\Entity\PlanUsers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlanUsers>
 *
 * @method PlanUsers|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanUsers|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanUsers[]    findAll()
 * @method PlanUsers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanUsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanUsers::class);
        $this->conn = $this->getEntityManager()->getConnection();
    }

    public function save(PlanUsers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PlanUsers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function fetchAllUsers(int $id): ?array
    {
        $sql = "SELECT user.* FROM user_in_plan INNER JOIN 
                user ON user.user_id=user_in_plan.user_id
                WHERE user_in_plan.plan_id={$id}";
        $stmt = $this->conn->executeQuery($sql);

        if($stmt->rowCount() > 0) {
            return $stmt->fetchAllAssociative();
        }
        return null;
    }

    public function findUserInPlan(int $plan_id, int $user_id): ?array
    {
        $sql = "SELECT user.* FROM user INNER JOIN user_in_plan
                ON user.user_id=user_in_plan.user_id 
                WHERE user_in_plan.plan_id={$plan_id} AND 
                user_in_plan.user_id={$user_id}";
        $stmt = $this->conn->executeQuery($sql);

        if($stmt->rowCount() > 0) {
            return $stmt->fetchAllAssociative();
        }
        return null;
    }

    public function findUser($id): ?bool
    {
        $sql = "SELECT user_id FROM user WHERE user_id={$id}";
        $stmt = $this->conn->executeQuery($sql);
        
        if($stmt->rowCount() > 0) {
            return True;
        }
        return null;
    }

//    /**
//     * @return PlanUsers[] Returns an array of PlanUsers objects
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

//    public function findOneBySomeField($value): ?PlanUsers
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
