<?php
namespace App\Repository;

use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PersonRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Person::class);
	}
	
	/**
	 * Najde 10 nejlépe hodnocených osob
	 *
	 * @return Person[] Pole objektů Person (na indexu 0) a 'likes', 'dislikes' a 'rating'
	 */
	public function findTop10Rated(): array
	{
		return $this->createQueryBuilder('p')
		->select('p', 'AVG(s.assessment) as rating',
			'SUM(CASE WHEN s.assessment = 1 THEN 1 ELSE 0 END) as likes',
			'SUM(CASE WHEN s.assessment = 0 THEN 1 ELSE 0 END) as dislikes')
			->leftJoin('p.statistics', 's')
			->groupBy('p.id')
			->orderBy('rating', 'DESC')
			->setMaxResults(10)
			->getQuery()
			->getResult();
	}
}
