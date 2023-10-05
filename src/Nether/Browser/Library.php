<?php

namespace Nether\Browser;

use Nether\Common;

class Library
extends Common\Library {

	public function
	OnLoad(...$Argv):
	void {

		(static::$Config)
		->Define(Key::ConfUserAgent, Key::DefaultUserAgent);

		return;
	}

}
