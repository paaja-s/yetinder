<?php
// src/Controller/BestController.php
namespace App\Controller;

use App\Enum\SexEnum;
use App\Repository\PersonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BestController extends AbstractController
{
	#[Route('/best', name: 'best_10')]
	#[Route('/', name: 'home')]
	public function best10(PersonRepository $personRepository): Response
	{
		$top10 = $personRepository->findTop10Rated();
		
		return $this->render('best/index.html.twig', [
			'top10' => $top10,
			'getSexLabel' => fn(int $sex) => SexEnum::from($sex)->label()
		]);
	}
	
	
}
