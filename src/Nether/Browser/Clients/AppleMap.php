<?php ##########################################################################
################################################################################

namespace Nether\Browser\Remote;

use Nether\Browser;
use Nether\Common;

################################################################################
################################################################################

class AppleMap {

	static string
	$TokenURL = 'https://maps-api.apple.com/v1/token';

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected string
	$MapKitToken;

	protected string
	$ClientToken;

	protected Browser\Client
	$Client;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	__Construct(string $MapKitToken, ?Browser\Client $Client=NULL) {

		$this->MapKitToken = $MapKitToken;
		$this->Client = $Client ?? new Browser\Client;

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	FetchClientToken():
	static {

		$JSON = $this->Client->FetchAsJSON(static::$TokenURL);

		// ...

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FromMapKitToken(string $MapKitToken):
	static {

		$Output = new static($MapKitToken);
		$Output->FetchClientToken();

		return $Output;
	}

};
