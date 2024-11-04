<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Statistic
{
	#[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
	private ?int $id = null;
	
	#[ORM\ManyToOne(targetEntity: Person::class)]
	#[ORM\JoinColumn(nullable: false)]
	private Person $person;
	
	#[ORM\Column(type: 'datetime')]
	private \DateTimeInterface $dt;
	
	#[ORM\Column(type: 'integer')]
	private int $assessment;
	
	// Getter and setter methods
	
	public function setPerson(Person $person): self
	{
		$this->person = $person;
		return $this;
	}
	public function setDt(\DateTime $dt): self
	{
		$this->dt = $dt;
		return $this;
	}
	public function setAssessment(int $assessment): self
	{
		$this->assessment = $assessment;
		return $this;
	}
	
}
