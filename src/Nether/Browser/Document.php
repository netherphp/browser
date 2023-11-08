<?php

namespace Nether\Browser;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common;
use Symfony\Component\DomCrawler;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class Document
extends Common\Prototype {

	protected ?string
	$Source = NULL;

	protected DomCrawler\Crawler
	$API;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	OnReady(Common\Prototype\ConstructArgs $Args):
	void {

		$this->API = new DomCrawler\Crawler($this->Source);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Info('Return the Source HTML as it was given.')]
	public function
	GetSource():
	string {

		return $this->Source;
	}

	#[Common\Meta\Info('Return the HTML re-rendered by the library.')]
	public function
	GetHTML():
	string {

		$HTML = $this->API->OuterHTML();

		return $HTML;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FromFile(?string $Filename):
	?static {

		if(!$Filename)
		throw new Common\Error\RequiredDataMissing('Filename', 'string');

		if(!file_exists($Filename))
		throw new Common\Error\RequiredDataMissing('Filename', 'file');

		////////

		$HTML = file_get_contents($Filename);
		$Output = static::FromHTML($HTML);

		////////

		return $Output;
	}

	static public function
	FromHTML(?string $Source):
	?static {

		if($Source === NULL)
		return NULL;

		$Output = new static([
			'Source' => $Source ?? ''
		]);

		return $Output;
	}

};
