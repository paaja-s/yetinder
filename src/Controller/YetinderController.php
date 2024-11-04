<?php
namespace App\Controller;

use App\Entity\Person;
use App\Entity\Statistic;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class YetinderController extends AbstractController
{
	#[Route('/yetinder', name: 'yetinder')]
	public function showRandomPerson(EntityManagerInterface $entityManager): Response
	{
		// Najdeme osobu s nejnovějším hodnocením
		/*$latestRatedPerson = $entityManager->getRepository(Statistic::class)
		->createQueryBuilder('s')
		->select('s.person')
		->orderBy('s.dt', 'DESC')
		->setMaxResults(1)
		->getQuery()
		->getOneOrNullResult();*/
		
		$query = $entityManager->createQuery(
			'SELECT p
            FROM App\Entity\Person p
            JOIN App\Entity\Statistic s WITH s.person = p
            ORDER BY s.dt DESC'
			)
			->setMaxResults(1);
		$latestRatedPerson = $query->getOneOrNullResult();
		
		// Vyloučíme tuto osobu a vybereme náhodnou z ostatních
		$qb = $entityManager->getRepository(Person::class)->createQueryBuilder('p');
		if ($latestRatedPerson) {
			$qb->where('p.id != :latestPerson')
			->setParameter('latestPerson', $latestRatedPerson->getId());
		}
		$persons = $qb->getQuery()->getResult();
		
		$randomPerson = $persons[array_rand($persons)];
		
		return $this->render('yetinder/person.html.twig', [
			'person' => $randomPerson,
		]);
	}
	
	#[Route('/yetinder/rate/{id}/{like}', name: 'yetinder_rate')]
	public function ratePerson(int $id, bool $like, EntityManagerInterface $entityManager): Response
	{
		$person = $entityManager->getRepository(Person::class)->find($id);
		if (!$person) {
			throw $this->createNotFoundException('Osoba nebyla nalezena.');
		}
		
		$statistic = new Statistic();
		$statistic->setPerson($person);
		$statistic->setDt(new \DateTime());
		$statistic->setAssessment($like ? 1 : 0);
		
		$entityManager->persist($statistic);
		$entityManager->flush();
		
		return $this->redirectToRoute('yetinder');
	}
}
