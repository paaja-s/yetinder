<?php
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatisticsController extends AbstractController
{
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
			'yearStats' => $yearStats,
			'monthStats' => $monthStats,
			'dayStats' => $dayStats,
			'selectedYear' => $year,
			'selectedMonth' => $month,
			'selectedDay' => $day,
		]);
	}
	
	private function getStatsByYear(EntityManagerInterface $entityManager,int $year)
	{
		$sql = 'SELECT p.first_name, p.last_name,
							SUM(CASE WHEN s.assessment = 1 THEN 1 ELSE 0 END) as likes,
							SUM(CASE WHEN s.assessment = 0 THEN 1 ELSE 0 END) as dislikes
						FROM statistic s
						JOIN person p ON s.person_id = p.id
						WHERE strftime("%Y", s.dt) = :year
						GROUP BY p.id';
		$stmt = $entityManager->getConnection()->prepare($sql);
		return $stmt->executeQuery([
			'year' => (string)$year
		])->fetchAllAssociative();
	}
	
	private function getStatsByMonth(EntityManagerInterface $entityManager,int $year, int $month)
	{
		$sql = 'SELECT p.first_name, p.last_name,
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
		])->fetchAllAssociative();
	}
	
	private function getStatsByDay(EntityManagerInterface $entityManager,int $year, int $month, int $day)
	{
		$sql = 'SELECT p.first_name, p.last_name,
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
		])->fetchAllAssociative();
	}
}
