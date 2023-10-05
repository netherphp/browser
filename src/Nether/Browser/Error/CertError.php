<?php

namespace Nether\Browser\Error;

use Exception;

class CertError
extends Exception {

	public function
	__Construct(string $Message='Failed to validate SSL Cert') {
		parent::__Construct($Message);
		return;
	}

}
