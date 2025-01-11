<?php ##########################################################################
################################################################################

namespace Nether\Browser\Clients;

use Nether\Browser;
use Nether\Common;

################################################################################
################################################################################

class AppleMap {

	static string
	$TokenVerb = 'GET';

	static string
	$TokenURL = 'https://maps-api.apple.com/v1/token';

	static string
	$GeocodeVerb = 'GET';

	static string
	$GeocodeURL = 'https://maps-api.apple.com/v1/geocode';

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected ?string
	$MapKitToken = NULL;

	protected ?string
	$ClientToken = NULL;

	protected ?int
	$ClientExpireTime = NULL;

	protected Browser\Client
	$Client;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(string $MapKitToken, ?Browser\Client $Client=NULL) {

		$this->MapKitToken = $MapKitToken;
		$this->Client = $Client ?? new Browser\Client;
		$this->Client->SetVia(Browser\Client::ViaCURL);

		return;
	}

	public function
	LookupAddressCoords(string $Address):
	?Common\Units\Vec2 {

		printf('[%s] %s%s', __METHOD__, $Address, PHP_EOL);

		////////

		if($this->NeedsClientToken())
		$this->FetchClientToken();

		////////

		($this->Client)
		->ClearHeaders()
		->SetHeader(
			'Authorization',
			sprintf('Bearer %s', $this->ClientToken)
		);

		$JSON = Common\Datastore::FromArray(
			($this->Client)
			->SetMethod(static::$GeocodeVerb)
			->FetchAsJSON(sprintf(
				'%s?q=%s',
				static::$GeocodeURL,
				urlencode($Address)
			))
		);

		if(!$JSON->HasKey('results'))
		throw new Common\Error\RequiredDataMissing('results', 'array');

		////////

		$Results = Common\Datastore::FromArray($JSON->Get('results'));

		if(!$Results->Count())
		return NULL;

		////////

		$Pick = Common\Datastore::FromArray($Results->Current());

		if(!$Pick->HasKey('coordinate'))
		throw new Common\Error\RequiredDataMissing('coordinate', 'array');

		////////

		$Output = new Common\Units\Vec2(
			$Pick['coordinate']['latitude'],
			$Pick['coordinate']['longitude']
		);

		return $Output;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	NeedsClientToken():
	bool {

		if(!isset($this->ClientToken))
		return TRUE;

		if(time() >= $this->ClientExpireTime)
		return TRUE;

		////////

		return FALSE;
	}

	public function
	FetchClientToken():
	static {

		printf('[%s]%s', __METHOD__, PHP_EOL);

		////////

		($this->Client)
		->ClearHeaders()
		->SetHeader(
			'Authorization',
			sprintf('Bearer %s', $this->MapKitToken)
		);

		$JSON = Common\Datastore::FromArray(
			($this->Client)
			->SetMethod(static::$TokenVerb)
			->FetchAsJSON(static::$TokenURL)
		);

		if(!$JSON->HasKey('accessToken'))
		throw new Common\Error\RequiredDataMissing('accessToken', 'JWT from Apple');

		////////

		// apple is returning 1800 and we will account for some lazy drift.

		$TokenTimeout = ((int)$JSON->Get('expiresInSeconds')) - 30;

		$this->ClientToken = $JSON->Get('accessToken');
		$this->ClientExpireTime = time() + $TokenTimeout;

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FromMapKitToken(string $MapKitToken):
	static {

		$Output = new static($MapKitToken);
		//$Output->FetchClientToken();

		return $Output;
	}

};
