<?php

namespace Egoist\IndexNow\Interfaces;

interface IndexNowInterface
{
	/**
	 * Set the host for IndexNow service.
	 *
	 * @param string $indexNowHost The host for IndexNow service
	 * @return void
	 */
	public function setIndexNowHost(string $indexNowHost): void;

	/**
	 * Get the host for IndexNow service.
	 *
	 * @return string The host for IndexNow service
	 */
	public function getIndexNowHost(): string;

	/**
	 * Set the host for the request.
	 *
	 * @param string $requestHost The host for the request
	 * @return void
	 */
	public function setRequestHost(string $requestHost): void;

	/**
	 * Get the host for the request.
	 *
	 * @return string The host for the request
	 */
	public function getRequestHost(): string;

	/**
	 * Set the key for IndexNow service.
	 *
	 * @param string $key The key for IndexNow service
	 * @return void
	 */
	public function setKey(string $key): void;

	/**
	 * Get the key for IndexNow service.
	 *
	 * @return string The key for IndexNow service
	 */
	public function getKey(): string;

	/**
	 * Set the key location for IndexNow service.
	 *
	 * @param string|null $keyLocation The key location for IndexNow service
	 * @return void
	 */
	public function setKeyLocation(?string $keyLocation): void;

	/**
	 * Get the key location for IndexNow service.
	 *
	 * @return string|null The key location for IndexNow service
	 */
	public function getKeyLocation(): ?string;

	/**
	 * Submit URL(s) to the IndexNow service for indexing.
	 *
	 * @param string|array $url The URL or array of URLs to be submitted
	 * @return void
	 */
	public function submit(string|array $url): void;
}