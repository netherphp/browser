<?php

namespace NetherTestSuite\Browser\Client;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Browser;
use Nether\Common;

use PHPUnit\Framework\TestCase;
use Error;

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
			'Via'       => Browser\Client::ViaCURL,
			'Method'    => Browser\Client::POST,
			'UserAgent' => 'Lulz Browser',
			'URL'       => 'https://google.com'
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
		$this->AssertEquals($Data['UserAgent'], $Client->GetUserAgent());
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
	TestRequestViaFileGetContents():
	void {

		new Browser\Library(new Common\Datastore);
		$Google = Browser\Client::FromURL('https://google.com/search?q=test');
		$Google->SetVia($Google::ViaFileGetContents);
		$Data = $Google->Fetch();

		$this->AssertNotNull($Data);
		$this->AssertTrue(is_string($Data));
		$this->AssertTrue(str_contains($Data, '<html'));
		$this->AssertTrue(str_contains($Data, 'test'));

		return;
	}

	/** @test */
	public function
	TestRequestViaCURL():
	void {

		new Browser\Library(new Common\Datastore);
		$Google = Browser\Client::FromURL('https://google.com/search?q=test');
		$Google->SetVia($Google::ViaCURL);
		$Data = $Google->Fetch();

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

		// provide a way to disable this test via the phpunit.xml in the
		// event i accidentally break the service i built to confirm this
		// is actually working.

		$Outreach = (
			TRUE
			&& defined('NetherBrowserUnitTestOutreachProgram')
			&& constant('NetherBrowserUnitTestOutreachProgram') === TRUE
		);

		if(!$Outreach) {
			$this->MarkTestSkipped('NetherBrowserUnitTestOutreachProgram is Disabled');
			return;
		}

		////////

		// {
		// 	"Error": 0,
		// 	"Message": "OK",
		// 	"Goto": null,
		// 	"Payload": {
		// 		"Date": "2023-10-05 14:09:45 -0500",
		// 		"Time": 1696532985,
		// 		"IP": "...",
		// 		"UserAgent": "..."
		// 	}
		// }

		$API = Browser\Client::FromURL('https://tech.live/api/browser');

		$API->SetVia($API::ViaFileGetContents);
		$Data = $API->FetchAsJSON();
		$this->AssertEquals(
			Browser\Key::DefaultUserAgent,
			$Data['Payload']['UserAgent']
		);

		$API->SetVia($API::ViaCURL);
		$Data = $API->FetchAsJSON();
		$this->AssertEquals(
			Browser\Key::DefaultUserAgent,
			$Data['Payload']['UserAgent']
		);

		$API->SetVia($API::ViaFileGetContents);
		$API->SetUserAgent('YOLO');
		$Data = $API->FetchAsJSON();
		$this->AssertEquals(
			'YOLO',
			$Data['Payload']['UserAgent']
		);

		$API->SetVia($API::ViaCURL);
		$API->SetUserAgent('YOLO');
		$Data = $API->FetchAsJSON();
		$this->AssertEquals(
			'YOLO',
			$Data['Payload']['UserAgent']
		);

		////////

		return;
	}

}
