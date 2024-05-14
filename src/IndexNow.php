<?php
declare(strict_types=1);

namespace Egoist\IndexNow;

use Egoist\IndexNow\Exceptions\BadRequestException;
use Egoist\IndexNow\Exceptions\ForbiddenException;
use Egoist\IndexNow\Exceptions\IndexNowException;
use Egoist\IndexNow\Exceptions\TooManyRequestsException;
use Egoist\IndexNow\Exceptions\UnprocessableEntityException;
use Egoist\IndexNow\Exceptions\TooManyUrlsException;
use Egoist\IndexNow\Interfaces\IndexNowInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;

class IndexNow implements IndexNowInterface
{
	private string $requestHost;
	private string $indexNowHost;
	private string $key;
	private ?string $keyLocation = null;


	public function setIndexNowHost(string $indexNowHost): void
	{
		$this->indexNowHost = $indexNowHost;
	}

	public function getIndexNowHost(): string
	{
		if (isset($this->indexNowHost)) {
			return $this->indexNowHost;
		}
		$indexNowHost = config('index-now.api_host');
		$this->indexNowHost = 'https://' . $indexNowHost . '/indexnow';
		return $this->indexNowHost;
	}

	public function setRequestHost(string $requestHost): void
	{
		$this->requestHost = $requestHost;
	}

	/**
	 * @throws IndexNowException
	 */
	public function getRequestHost(): string
	{
		if (isset($this->requestHost)) {
			return $this->cleanUrl($this->requestHost);
		}

		$requestHost = config('index-now.app_url');
		$this->requestHost = $this->cleanUrl($requestHost);
		return $this->requestHost;
	}

	public function setKey(string $key): void
	{
		$this->key = $key;
	}

	public function getKey(): string
	{
		if (isset($this->key)) {
			return $this->key;
		}
		$this->key = config('index-now.key');
		return $this->key;
	}

	public function setKeyLocation(?string $keyLocation): void
	{
		$this->keyLocation = $keyLocation;
	}

	public function getKeyLocation(): ?string
	{

		if (isset($this->keyLocation)) {
			return $this->keyLocation;
		}

		$this->keyLocation = config('index-now.key_location');
		return $this->keyLocation;
	}

	public function getQueryParameters(): array
	{
		$queryParameters = ['key' => $this->getKey()];

		$keyLocation = $this->getKeyLocation();
		if (isset($keyLocation) && $keyLocation !== '') {
			$queryParameters['keyLocation'] = $keyLocation;
		}
		return $queryParameters;
	}

	/**
	 * @param string|string[] $url
	 *
	 * @throws IndexNowException
	 */
	public function submit(string|array $url): void
	{
		if (is_array($url)) {
			$this->submitMultipleUrls($url);
		}

		$this->submitSingleUrl($url);
	}


	/**
	 * @param string $url
	 * @return void The corresponding IndexNow exception.
	 * @throws BadRequestException
	 * @throws ForbiddenException
	 * @throws IndexNowException
	 * @throws TooManyRequestsException
	 * @throws UnprocessableEntityException
	 */
	public function submitSingleUrl(string $url): void
	{
		$targetUrl = $this->getIndexNowHost();

		$queryData = $this->getQueryParameters();
		$queryData['url'] = $url;

		$request = new Request('GET', $targetUrl . '?' . http_build_query($queryData));
		$client = new GuzzleClient();

		try {
			$client->send($request);
		} catch (GuzzleClientException $e) {
			throw $this->handleGuzzleException($e);
		} catch (GuzzleException $e) {
			throw new IndexNowException($e->getMessage(), $e->getCode(), $e);
		}
	}


	/**
	 * Submit an array of URLs to the IndexNow API.
	 *
	 * @param array $urls The URLs to submit.
	 * @return void The corresponding IndexNow exception.
	 * @throws BadRequestException
	 * @throws ForbiddenException
	 * @throws IndexNowException
	 * @throws TooManyRequestsException
	 * @throws TooManyUrlsException
	 * @throws UnprocessableEntityException
	 */
	public function submitMultipleUrls(array $urls): void
	{
		$targetUrl = $this->getIndexNowHost();
		$queryData = $this->getQueryParameters();
		$queryData['host'] = $this->getRequestHost();
		$queryData['urlList'] = $this->prepareUrls($urls);

		$request = new Request('POST', $targetUrl, ['Content-Type' => 'application/json'], json_encode($queryData));

		$client = new GuzzleClient();
		try {
			$client->send($request);
		} catch (GuzzleClientException $e) {
			throw $this->handleGuzzleException($e);
		} catch (GuzzleException $e) {
			throw new IndexNowException($e->getMessage(), $e->getCode(), $e);
		}
	}


	/**
	 * @param GuzzleClientException $e The Guzzle client exception.
	 * @throws TooManyRequestsException
	 * @throws ForbiddenException
	 * @throws IndexNowException
	 * @throws UnprocessableEntityException
	 * @throws BadRequestException
	 */
	private function handleGuzzleException(GuzzleClientException $e): IndexNowException
	{
		$statusCode = $e->getResponse()->getStatusCode();
		throw match ($statusCode) {
			400 => new BadRequestException(),
			403 => new ForbiddenException(),
			422 => new UnprocessableEntityException(),
			429 => new TooManyRequestsException(),
			default => new IndexNowException("Unexpected error occurred: HTTP $statusCode", $statusCode, $e),
		};
	}

	/**
	 * Returns the prepared list of URLs.
	 *
	 * @param array $urls The list of URLs to prepare.
	 * @return array The prepared list of URLs.
	 * @throws TooManyUrlsException If the number of URLs exceeds 10,000.
	 */
	public function prepareUrls(array $urls): array
	{
		if (count($urls) > 10000) {
			throw new TooManyUrlsException();
		}

		foreach ($urls as $key => $url) {
			$urls[$key] = $url;
		}

		return $urls;
	}

	/**
	 * @throws IndexNowException
	 */
	private function cleanUrl(string $url): string
	{
		$urlComponents = parse_url($url);
		if ($urlComponents && array_key_exists('host', $urlComponents)) {
			return $urlComponents['host'];
		} else {
			throw new IndexNowException('Invalid URL', 404);
		}
	}
}