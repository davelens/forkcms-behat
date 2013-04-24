<?php

namespace Faker\Provider;

class BackendUser extends \Faker\Provider\Base
{
	public function first_name($wordcount = 5)
	{
		$wordcount -= 1; //
		$name = $this->generator->name($wordcount);
		$explodedName = explode(' ', $name);
		return $explodedName[0];
	}

	public function last_name($wordcount = 5)
	{
		$wordcount -= 1; //
		$name = $this->generator->name($wordcount);
		$explodedName = explode(' ', $name);
		return max($explodedName);
	}

	public function password($length = 8)
	{
		$length = ((int) $length < 4) ? 4 : (int) $length;
		return substr(uniqid(), 0 , $length);
	}
}
