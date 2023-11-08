<?php

namespace NetherTestSuite\Browser\Client;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Browser;
use Nether\Common;
use PHPUnit;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class ClientTest
extends PHPUnit\Framework\TestCase {

	#[PHPUnit\Framework\Attributes\Before]
	public function
	SetupLibrary():
	void {

		Browser\Library::Reset();

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[PHPUnit\Framework\Attributes\Test]
	public function
	TestDefaults():
	void {

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

	#[PHPUnit\Framework\Attributes\Test]
	public function
	TestFromURL():
	void {

		$URL = 'https://bing.com';
		$Client = Browser\Client::FromURL($URL);

		$this->AssertEquals($Client::GET, $Client->GetMethod());
		$this->AssertEquals($URL, $Client->GetURL());

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[PHPUnit\Framework\Attributes\Test]
	public function
	TestFetchLocalMissing():
	void {

		$URL = Common\Filesystem\Util::Pathify(
			dirname(__FILE__, 2),
			'Document', 'HTML', 'nope.html'
		);

		////////

		$Client = Browser\Client::FromURL($URL);
		$Client->SetVia($Client::ViaFileGetContents);

		$Data = $Client->Fetch();
		$this->AssertNull($Data);

		$Data = $Client->FetchAsHTML();
		$this->AssertNull($Data);

		$Data = $Client->FetchAsJSON();
		$this->AssertNull($Data);

		////////

		return;
	}

	#[PHPUnit\Framework\Attributes\Test]
	public function
	TestFetchLocalFound():
	void {

		$URL = Common\Filesystem\Util::Pathify(
			dirname(__FILE__, 2),
			'Document', 'HTML', 'basic.html'
		);

		$Client = Browser\Client::FromURL($URL);
		$Client->SetVia($Client::ViaFileGetContents);

		// return a string of the contents which in this case looks like
		// normal html.

		$Data = $Client->Fetch();
		$this->AssertIsString($Data);
		$this->AssertTrue(str_contains($Data, '<html>'));
		$this->AssertTrue(str_contains($Data, 'Hello World'));

		// return a document object of the html and then ask it to cook it
		// it back to a string.

		$Data = $Client->FetchAsHTML();
		$this->AssertInstanceOf(Browser\Document::class, $Data);

		$Data = $Data->GetHTML();
		$this->AssertIsString($Data);
		$this->AssertTrue(str_contains($Data, '<html>'));
		$this->AssertTrue(str_contains($Data, 'Hello World'));

		// fail to parse the html as json such that we get returned an
		// empty set.

		$Data = $Client->FetchAsJSON();
		$this->AssertNull($Data);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[PHPUnit\Framework\Attributes\Test]
	public function
	TestRequestViaFileGetContents():
	void {

		$Google = Browser\Client::FromURL('https://google.com/search?q=test');
		$Google->SetVia($Google::ViaFileGetContents);
		$Data = $Google->Fetch();

		$this->AssertNotNull($Data);
		$this->AssertTrue(is_string($Data));
		$this->AssertTrue(str_contains($Data, '<html'));
		$this->AssertTrue(str_contains($Data, 'test'));

		return;
	}

	#[PHPUnit\Framework\Attributes\Test]
	public function
	TestRequestViaCURL():
	void {

		$Google = Browser\Client::FromURL('https://google.com/search?q=test');
		$Google->SetVia($Google::ViaCURL);
		$Data = $Google->Fetch();

		$this->AssertNotNull($Data);
		$this->AssertTrue(is_string($Data));
		$this->AssertTrue(str_contains($Data, '<html'));
		$this->AssertTrue(str_contains($Data, 'test'));

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[PHPUnit\Framework\Attributes\Test]
	public function
	TestUserAgentOutreachTest():
	void {

		// provide a way to disable this test via the phpunit.xml in the
		// event i accidentally break the service i built to confirm this
		// is actually working.

		// the primary thing i want to test here is that the user agent
		// is getting set properly.

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

		////////

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

		////////

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
