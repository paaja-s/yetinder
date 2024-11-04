<?php
namespace App\Repository;

use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class StatisticRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Person::class);
	}
	
	public function findTop10Rated(): array
	{
		$conn = $this->getEntityManager()->getConnection();
		$sql = '
			SELECT person_id, AVG(assessment) as rating
			FROM statistic
			GROUP BY person_id
			ORDER BY rating DESC
			LIMIT 10;
			';
		$stmt = $conn->prepare($sql);
		return $stmt->executeQuery()->fetchAllAssociative();
	}
}
