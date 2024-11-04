<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Person
{
	#[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
	private ?int $id = null;
	
	#[ORM\Column(type: 'string', length: 100)]
	private string $firstName;
	
	#[ORM\Column(type: 'string', length: 100)]
	private string $lastName;
	
	#[ORM\Column(type: 'integer')]
	private int $sex;
	
	#[ORM\Column(type: 'integer', nullable: true)]
	private ?int $height = null;
	
	#[ORM\Column(type: 'integer', nullable: true)]
	private ?int $weight = null;
	
	#[ORM\Column(type: 'string', length: 100, nullable: true)]
	private ?string $address = null;
	
	#[ORM\OneToMany(mappedBy: 'person', targetEntity: Statistic::class)]
	private Collection $statistics;
	
	public function __construct()
	{
		$this->statistics = new ArrayCollection();
	}
	
	public function getStatistics(): Collection
	{
		return $this->statistics;
	}
	
	public function getId(): int
	{
		return $this->id;
	}
	public function getFirstName(): string
	{
		return $this->firstName;
	}
	public function setFirstName(string $firstName): self
	{
		$this->firstName = $firstName;
		return $this;
	}
	public function getLastName(): string
	{
		return $this->lastName;
	}
	public function setLastName(string $lastName): self
	{
		$this->lastName = $lastName;
		return $this;
	}
	public function getSex(): int
	{
		return $this->sex;
	}
	public function setSex(int $sex): self
	{
		$this->sex = $sex;
		return $this;
	}
	public function getHeight(): ?int
	{
		return $this->height;
	}
	public function setHeight(?int $height): self
	{
		$this->height = $height;
		return $this;
	}
	public function getWeight(): ?int
	{
		return $this->weight;
	}
	public function setWeight(?int $weight): self
	{
		$this->weight = $weight;
		return $this;
	}
	public function getAddress(): ?string
	{
		return $this->address;
	}
	public function setAddress(?string $address): self
	{
		$this->address = $address;
		return $this;
	}
}
