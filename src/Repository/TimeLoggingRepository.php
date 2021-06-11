<?php

namespace App\Repository;

use App\Entity\TimeLogging;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TimeLogging|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimeLogging|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimeLogging[]    findAll()
 * @method TimeLogging[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimeLoggingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeLogging::class);
    }

    public function deleteEntry($id)
    {
        $qb = $this->createQueryBuilder('tt')
            ->delete('App:TimeLogging', 't')
            ->where('t.id = :id')
            ->setParameter('id', $id);
        return $qb->getQuery()->getResult();
    }

    public function stopLogging($id)
    {
        $query = $this->createQueryBuilder('tl')
            ->update('App:TimeLogging', 't')
            ->set('t.enddate', ':now')
            ->set('t.statecode', 0)
            ->where('t.id = :id')
            ->setParameter('now', date_create('now', new \DateTimeZone('Europe/Berlin')))
            ->setParameter('id', $id);
        return $query->getQuery()->getResult();
    }
    
    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getDataForUserReport($userid, bool $daily)
    {
        if ($daily === true) {
            $filter = "DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00')";
        } else {
            $filter = "DATE_ADD(DATE_ADD(LAST_DAY(NOW()), INTERVAL 1 DAY), INTERVAL -1 MONTH)";
        }
        //Native SQL und kein querybuilder, da timestampdiff genutzt werden soll
        $em = $this->getEntityManager();
        $sql = "SELECT u.email, p.name as projectname, (sum(TIMESTAMPDIFF(MINUTE, t.startdate, t.enddate)) / 60) as timelogged FROM time_logging t
                JOIN project p on p.id = t.projectid
                JOIN user u on u.id = t.userid
                WHERE startdate >= $filter and t.userid = :userid
                  and  TIMESTAMPDIFF(MINUTE, t.startdate, t.enddate) > 0 group by t.projectid";
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->executeQuery(['userid' => $userid]);
        return $stmt->fetchAllAssociative();
    }
}
