<?php
namespace App\Enum;

enum SexEnum: int
{
	case Male = 1;
	case Female = 0;
	
	public function label(): string
	{
		return match($this) {
			self::Male => 'Muž',
			self::Female => 'Žena',
		};
	}
}
