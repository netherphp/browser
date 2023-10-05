<?php

namespace Nether\Browser;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Common;

use CurlHandle;
use Exception;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class Client
extends Common\Prototype {

	const
	ViaFileGetContents = 1,
	ViaCURL            = 2;

	const
	GET    = 'GET',
	POST   = 'POST',
	DELETE = 'DELETE',
	PATCH  = 'PATCH';

	////////

	protected int
	$Via = self::ViaFileGetContents;

	protected string
	$Method = self::GET;

	protected ?string
	$UserAgent = NULL;

	protected ?string
	$URL = NULL;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	OnReady(Common\Prototype\ConstructArgs $Args):
	void {

		$this->PrepareUserAgent();

		return;
	}

	protected function
	PrepareUserAgent():
	void {

		if($this->UserAgent !== NULL)
		return;

		// read the configured default user agent strings if it has
		// not already been defined via argument.

		$this->UserAgent = (
			Library::Get(Key::ConfUserAgent)
			?: Key::DefaultUserAgent
		);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Info('Generate a Stream Context for browser as configured.')]
	public function
	GenerateStreamContext():
	mixed {

		// @todo 2023-10-05 at some point i am going to run into a case
		// where i need it to ignore bad ssl to get a job done. that should
		// be set as a flag on this object which will then define the
		// various context options as multiple are required.

		$Opts = [
			'http' => [ 'method' => $this->Method, 'user_agent' => $this->UserAgent ],
			'ssl'  => [ ]
		];

		$MoreOpts = [

		];

		return stream_context_create($Opts, $MoreOpts);
	}

	#[Common\Meta\Info('Generate a cURL Context for browser as configured.')]
	public function
	GenerateCurlContext():
	CurlHandle {

		// @todo 2023-10-05 at some point i am going to run into a case
		// where i need it to ignore bad ssl to get a job done. that should
		// be set as a flag on this object which will then define the
		// various context options as multiple are required.

		$CTX = curl_init($this->URL);
		curl_setopt($CTX, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($CTX, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($CTX, CURLOPT_USERAGENT, $this->UserAgent);

		return $CTX;
	}

	#[Common\Meta\Info('Fetch and return the data from the remote.')]
	public function
	Fetch():
	?string {

		$Output = match($this->Via) {
			static::ViaCURL
			=> $this->FetchViaCURL(),

			static::ViaFileGetContents
			=> $this->FetchViaFileGetContents(),

			default
			=> NULL
		};

		return $Output;
	}

	#[Common\Meta\Info('Fetch and digest data from the remote as HTML.')]
	public function
	FetchAsHTML():
	Document {

		try {
			$Doc = Document::FromHTML($this->Fetch());
		}

		catch(Exception $Err) {
			throw $Err;
		}

		return $Doc;
	}

	#[Common\Meta\Info('Fetch and digest data from the remote as JSON.')]
	public function
	FetchAsJSON():
	?array {

		$JSON = $this->Fetch();

		if(!$JSON)
		return NULL;

		$Data = json_decode($JSON, TRUE);

		if(!is_array($Data))
		return NULL;

		return $Data;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Info('Fetch via cURL.')]
	public function
	FetchViaCURL():
	?string {

		$CTX = $this->GenerateCurlContext();
		$Data = curl_exec($CTX);
		curl_close($CTX);

		if(curl_errno($CTX) !== 0)
		throw new Error\CertError(curl_error($CTX));

		return $Data;
	}

	#[Common\Meta\Info('Fetch via native file_get_contents.')]
	public function
	FetchViaFileGetContents():
	?string {

		$CTX = $this->GenerateStreamContext();
		$Data = file_get_contents($this->URL, FALSE, $CTX);

		return $Data;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	GetMethod():
	string {

		return $this->Method;
	}

	public function
	GetUserAgent():
	?string {

		return $this->UserAgent;
	}

	public function
	GetURL():
	?string {

		return $this->URL;
	}

	public function
	GetVia():
	int {

		return $this->Via;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	SetMethod(string $Method):
	static {

		$this->Method = $Method;

		return $this;
	}

	public function
	SetURL(string $URL):
	static {

		$this->URL = $URL;

		return $this;
	}

	public function
	SetUserAgent(string $UserAgent):
	static {

		$this->UserAgent = $UserAgent;

		return $this;
	}

	public function
	SetVia(int $Via):
	static {

		$this->Via = $Via;

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FromURL(string $URL):
	static {

		return new static([
			'URL' => $URL
		]);
	}

}
