<?php
// src/Repository/ClubTeacherRepository.php
namespace App\Repository;

use App\Entity\Club;
use App\Entity\ClubTeacher;
use App\Entity\Grade;
use App\Entity\Member;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClubTeacher|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClubTeacher|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClubTeacher[]    findAll()
 * @method ClubTeacher[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClubTeacherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClubTeacher::class);
    }

    public function getAFATeachers(Club $club): ?array
    {
        $qb = $this->createQueryBuilder('t');

        return $qb->select('t.club_teacher_id AS Id', 't.club_teacher_title AS Title', 't.club_teacher_type AS Type', 'm.member_firstname AS Firstname', 'm.member_name AS Name', 'd.grade_rank AS Grade')
            ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_id', 't.club_teacher_member'))
            ->join(Grade::class, 'd', 'WITH', $qb->expr()->eq('m.member_last_grade', 'd.grade_id'))
            ->where($qb->expr()->IsNotNull('t.club_teacher_member'))
            ->andWhere($qb->expr()->eq('t.club_teacher', $club->getClubId()))
            ->orderBy('Title', 'ASC')
            ->addOrderBy('Firstname', 'ASC')
            ->addOrderBy('Name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    public function getForeignTeachers(Club $club): ?array
    {
        $qb = $this->createQueryBuilder('t');

        return $qb->select('t.club_teacher_id AS Id', 't.club_teacher_title AS Title', 't.club_teacher_type AS Type', 't.club_teacher_firstname AS Firstname', 't.club_teacher_name AS Name', 't.club_teacher_grade AS Grade', 't.club_teacher_grade_title_aikikai AS GradeTitleAikikai', 't.club_teacher_grade_title_adeps AS GradeTitleAdeps')
            ->where($qb->expr()->IsNull('t.club_teacher_member'))
            ->andWhere($qb->expr()->eq('t.club_teacher', $club->getClubId()))
            ->orderBy('Title', 'ASC')
            ->addOrderBy('Grade', 'ASC')
            ->addOrderBy('Firstname', 'ASC')
            ->addOrderBy('Name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
