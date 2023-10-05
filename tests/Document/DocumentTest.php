<?php

namespace NetherTestSuite\Browser\Document;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Browser;
use Nether\Common;

use PHPUnit\Framework\TestCase;
use Error;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class DocumentTest
extends TestCase {

	/** @test */
	public function
	TestBasic():
	void {

		$Doc = Browser\Document::FromFile(Common\Filesystem\Util::Pathify(
			dirname(__FILE__),
			'HTML', 'basic.html'
		));

		// test the input was given

		$HTML = $Doc->GetSource();
		$this->AssertIsString($HTML);
		$this->AssertTrue(str_contains($HTML, '<html'));
		$this->AssertTrue(str_contains($HTML, '<body'));

		// test it can re-render it out.

		$HTML = $Doc->GetHTML();
		$this->AssertIsString($HTML);
		$this->AssertTrue(str_contains($HTML, '<html'));
		$this->AssertTrue(str_contains($HTML, '<body'));

		return;
	}

}
