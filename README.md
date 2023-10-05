# Nether\Browser

[![Packagist](https://img.shields.io/packagist/v/netherphp/browser.svg?style=for-the-badge)](https://packagist.org/packages/netherphp/browser)
[![Build Status](https://img.shields.io/github/actions/workflow/status/netherphp/browser/phpunit.yml?style=for-the-badge)](https://github.com/netherphp/browser/actions)
[![codecov](https://img.shields.io/codecov/c/gh/netherphp/browser?style=for-the-badge&token=VQC48XNBS2)](https://codecov.io/gh/netherphp/browser)

Provide a light weight means of asking for remote resources, idealy with the
fewest number of error cases to consider but that has yet to be seen. Able to
fetch a remote resource using PHP's `file_get_contents()` or the `cURL`
extension.

```php
use Nether\Browser;

$Client = Browser\Client::FromURL('https://google.com/search?q=test');

// fetching generic data without any care as to what it may be.

$Text = $Client->Fetch();

// fetching data expecting the remote to be valid json. returns an array on
// success or null on failure.

$Data = $Client->FetchAsJSON(); // array or NULL.

// fetching data expecting the remote to be valid html. returns a Document on
// success or null on failure.

$HTML = $client->FetchAsHTML(); // Browser\Document or NULL.
```

