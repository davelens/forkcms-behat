<?php

namespace Faker\Provider;

class BlogPost extends \Faker\Provider\Base
{
	public function title($wordcount = 5)
	{
		$wordcount -= 1; //
		$sentence = $this->generator->sentence($wordcount);
		return substr($sentence, 0, -1);
	}
}
