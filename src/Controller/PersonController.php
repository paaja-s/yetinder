<?php
namespace App\Controller;

use App\Entity\Person;
use App\Enum\SexEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PersonController extends AbstractController
{
	#[Route('/person/new', name: 'person_add')]
	public function addPerson(Request $request, EntityManagerInterface $entityManager): Response
	{
		// Kontrola, zda je formulář odeslán (POST)
		if ($request->isMethod('POST')) {
			// Vytvoření nové instance Person a nastavení hodnot z formuláře
			$person = new Person();
			$person->setFirstName($request->request->get('first_name'));
			$person->setLastName($request->request->get('last_name'));
			$person->setSex((int)$request->request->get('sex'));
			$person->setAddress($request->request->get('address'));
			$person->setHeight($request->request->get('height') ? (int)$request->request->get('height') : null);
			$person->setWeight($request->request->get('weight') ? (int)$request->request->get('weight') : null);
			
			// Uložení do databáze pomocí EntityManager
			$entityManager->persist($person);
			$entityManager->flush();
			
			// Přesměrování na jinou stránku, např. seznam osob
			return $this->redirectToRoute('best_10');
		}
		
		// Pokud není formulář odeslán, zobrazí se prázdný formulář
		return $this->render('person/new.html.twig', ['sexOptions' => SexEnum::cases()]);
	}
}
