<?php
namespace App\Twig;

use App\Enum\SexEnum;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
	public function getFunctions(): array
	{
		return [
			new TwigFunction('getSexLabel', [$this, 'getSexLabel']),
		];
	}
	
	public function getSexLabel(int $sex): string
	{
		return SexEnum::from($sex)->label();
	}
}
