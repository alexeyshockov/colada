<?php

namespace Colada;

use Traversable;

interface Iterable extends Traversable
{
	public function isTraversableAgain();
}
