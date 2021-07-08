<?php

declare(strict_types=1);

namespace HackerspaceBielefeld\Website\Service;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class SpaceApiClient
{
    public const API_STATUS_URL = 'https://status.space.bi/status.json';

    public function __construct(
		protected Client $httpClient
	) {
    }

    public function getSpaceStatus(): array {

        $response = $this->httpClient->request(
            'GET',
            self::API_STATUS_URL,
            [
                RequestOptions::TIMEOUT => 7,
                RequestOptions::HTTP_ERRORS => true,
                RequestOptions::HEADERS => [
                    'User-Agent' => 'HSB Website',
                    'Accept' => 'application/json',
				],
            ]
        );

        $responseBody = (string)$response->getBody();
        return json_decode($responseBody, true, 512, JSON_THROW_ON_ERROR);
    }
}
