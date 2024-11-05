<?php
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PersonRepository;

class StatisticsController extends AbstractController
{
	public function __construct(PersonRepository $personRepository)
	{
		$this->personRepository = $personRepository;
	}
	
	#[Route('/statistics', name: 'statistics')]
	public function index(Request $request, EntityManagerInterface $entityManager): Response
	{
		// Získání období pro statistiky
		$year = $request->query->getInt('year', date('Y'));
		$month = $request->query->getInt('month', date('m'));
		$day = $request->query->getInt('day', date('d'));
		
		// SQL dotazy pro získání statistik podle období
		$yearStats = $this->getStatsByYear($entityManager, $year);
		$monthStats = $this->getStatsByMonth($entityManager, $year, $month);
		$dayStats = $this->getStatsByDay($entityManager, $year, $month, $day);
		
		return $this->render('statistics/index.html.twig', [
			'statistics' => $this->combineStatistics($yearStats, $monthStats, $dayStats),
			'selectedYear' => $year,
			'selectedMonth' => $month,
			'selectedDay' => $day,
		]);
	}
	
	private function getStatsByYear(EntityManagerInterface $entityManager,int $year)
	{
		$sql = 'SELECT p.id,
							SUM(CASE WHEN s.assessment = 1 THEN 1 ELSE 0 END) as likes,
							SUM(CASE WHEN s.assessment = 0 THEN 1 ELSE 0 END) as dislikes
						FROM statistic s
						JOIN person p ON s.person_id = p.id
						WHERE strftime("%Y", s.dt) = :year
						GROUP BY p.id';
		$stmt = $entityManager->getConnection()->prepare($sql);
		return $stmt->executeQuery([
			'year' => (string)$year
		])->fetchAllAssociativeIndexed();
	}
	
	private function getStatsByMonth(EntityManagerInterface $entityManager,int $year, int $month)
	{
		$sql = 'SELECT p.id,
							SUM(CASE WHEN s.assessment = 1 THEN 1 ELSE 0 END) as likes,
							SUM(CASE WHEN s.assessment = 0 THEN 1 ELSE 0 END) as dislikes
						FROM statistic s
						JOIN person p ON s.person_id = p.id
						WHERE strftime("%Y", s.dt) = :year AND strftime("%m", s.dt) = :month
						GROUP BY p.id';
		
		$stmt = $entityManager->getConnection()->prepare($sql);
		return $stmt->executeQuery([
			'year' => (string)$year,
			'month' => sprintf("%02d", $month)
		])->fetchAllAssociativeIndexed();
	}
	
	private function getStatsByDay(EntityManagerInterface $entityManager,int $year, int $month, int $day)
	{
		$sql = 'SELECT p.id,
							SUM(CASE WHEN s.assessment = 1 THEN 1 ELSE 0 END) as likes,
							SUM(CASE WHEN s.assessment = 0 THEN 1 ELSE 0 END) as dislikes
						FROM statistic s
						JOIN person p ON s.person_id = p.id
						WHERE strftime("%Y", s.dt) = :year AND strftime("%m", s.dt) = :month AND strftime("%d", s.dt) = :day
						GROUP BY p.id';
		
		$stmt = $entityManager->getConnection()->prepare($sql);
		return $stmt->executeQuery([
			'year' => (string)$year,
			'month' => sprintf("%02d", $month),
			'day' => sprintf("%02d", $day)
		])->fetchAllAssociativeIndexed();
	}
	
	/**
	 * Sestaveni pole statistiky, indexovane person.id obsahujici Person a rocni, mesicni a denni statistiku
	 * @param array $yearly
	 * @param array $monthly
	 * @param array $daily
	 * @return array
	 */
	function combineStatistics(array $yearly, array $monthly, array $daily): array
	{
		$combined = [];
		$empty = ['likes'=>0,'dislikes'=>0];
		$allPersons = $this->personRepository->findAll();
		
		foreach($allPersons as $person){
			$id = $person->getId();
			$combined[$id] = [$person];
			$combined[$id]['yearly'] = [isset($yearly[$id])?$yearly[$id]:$empty];
			$combined[$id]['monthly'] = [isset($monthly[$id])?$monthly[$id]:$empty];
			$combined[$id]['daily'] = [isset($daily[$id])?$daily[$id]:$empty];
		}
		
		return $combined;
	}
}
