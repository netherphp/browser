<?php

namespace NetherTestSuite\Browser;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Browser;
use Nether\Common;
use PHPUnit\Framework\TestCase;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class ClientTest
extends TestCase {

	/** @test */
	public function
	TestBasic():
	void {

		new Browser\Library(new Common\Datastore);

		$Data = [
			'Via'    => Browser\Client::ViaCURL,
			'Method' => Browser\Client::POST,
			'UA'     => 'Lulz Browser',
			'URL'    => 'https://google.com'
		];

		// check defaults

		$Client = new Browser\Client;
		$this->AssertEquals($Client::ViaFileGetContents, $Client->GetVia());
		$this->AssertEquals($Client::GET, $Client->GetMethod());
		$this->AssertEquals(Browser\Key::DefaultUserAgent, $Client->GetUserAgent());
		$this->AssertNull($Client->GetURL());

		// check defined

		$Client = new Browser\Client($Data);
		$this->AssertEquals($Data['Via'], $Client->GetVia());
		$this->AssertEquals($Data['Method'], $Client->GetMethod());
		$this->AssertEquals($Data['UA'], $Client->GetUserAgent());
		$this->AssertEquals($Data['URL'], $Client->GetURL());

		return;
	}

	/** @test */
	public function
	TestFromURL():
	void {

		$URL = 'https://bing.com';
		$Client = Browser\Client::FromURL($URL);

		$this->AssertEquals($Client::GET, $Client->GetMethod());
		$this->AssertEquals($URL, $Client->GetURL());

		return;
	}

	/** @test */
	public function
	TestBasicRequest():
	void {

		$Google = Browser\Client::FromURL('https://google.com/search?q=test');
		$Data = strtolower($Google->Fetch());

		$this->AssertNotNull($Data);
		$this->AssertTrue(is_string($Data));
		$this->AssertTrue(str_contains($Data, '<html'));
		$this->AssertTrue(str_contains($Data, 'test'));

		return;
	}

	/** @test */
	public function
	TestBasicRequestToReflector():
	void {

		$API = Browser\Client::FromURL('https://tech.live/api/browser');
		$Data = json_decode($API->Fetch(), TRUE);

		$this->AssertEquals(
			Browser\Key::DefaultUserAgent,
			$Data['Payload']['UserAgent']
		);

		$API->SetUserAgent('YOLO');
		$Data = json_decode($API->Fetch(), TRUE);

		$this->AssertEquals(
			'YOLO',
			$Data['Payload']['UserAgent']
		);

		return;
	}

}
