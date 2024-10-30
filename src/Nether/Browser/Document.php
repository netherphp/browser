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

	public DomCrawler\Crawler
	$API;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	OnReady(Common\Prototype\ConstructArgs $Args):
	void {

		if(!isset($this->API))
		$this->API = new DomCrawler\Crawler($this->Source);

		return;
	}


	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Find(string $Selector):
	Document {

		return new Document([
			'API' => ($this->API)->filter($Selector)
		]);
	}

	public function
	Each(callable $Func):
	void {

		$El = NULL;

		foreach($this->API as $El)
		$Func($El);

		return;
	}

	public function
	Text():
	string {

		return ($this->API)->text();
	}

	public function
	Attr(string $Name):
	string {

		return ($this->API)->attr($Name);
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
