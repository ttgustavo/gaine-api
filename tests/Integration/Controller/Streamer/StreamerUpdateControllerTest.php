<?php

declare(strict_types=1);

namespace Test\Integration\Controller\Streamer;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Test\Integration\AppTestCase;

use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;

class StreamerUpdateControllerTest extends AppTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        parent::cleanTables(['auth', 'users', 'streamers']);

        $_ENV['JWT_SECRET'] = 'secret';
    }


    public function testStreamerUpdatedReturnsStatusCode200(): void
    {
        // Arrange
        $jwtToken = $this->authenticate();

        $params = [
            'streamer_code' => 'MYS',
            'streamer_name' => 'MyStreamer'
        ];
        $params_updated = [
            'streamer_code' => 'MYS',
            'streamer_code_updated' => 'UNKWN',
            'streamer_name_updated' => 'UnknownStreamer'
        ];
        $headers = ['Authorization' => 'Bearer ' . $jwtToken];

        // Act
        $this->post('/streamers', $params, $headers);
        $response = $this->put('/streamers', $params_updated, $headers);

        // Assert
        assertThat($response->getStatusCode(), equalTo(StatusCode::STATUS_OK));
    }

    public function testReturnsStatusCode404WhenStreamerNotExists(): void
    {
        // Arrange
        $jwtToken = $this->authenticate();

        $params_updated = [
            'streamer_code' => 'MYS',
            'streamer_code_updated' => 'UNKWN',
            'streamer_name_updated' => 'UnknownStreamer'
        ];
        $headers = ['Authorization' => 'Bearer ' . $jwtToken];

        // Act
        $response = $this->put('/streamers', $params_updated, $headers);

        // Assert
        assertThat($response->getStatusCode(), equalTo(StatusCode::STATUS_NOT_FOUND));
    }

    public function testReturnsStatusCode401WhenNotAuthenticated(): void
    {
        // Arrange
        $params_updated = [
            'streamer_code' => 'MYS',
            'streamer_code_updated' => 'UNKWN',
            'streamer_name_updated' => 'UnknownStreamer'
        ];

        // Act
        $response = $this->put('/streamers', $params_updated);

        // Assert
        assertThat($response->getStatusCode(), equalTo(StatusCode::STATUS_UNAUTHORIZED));
    }

    public function testReturnsStatusCode400WhenParamsAreInvalid(): void
    {
        // Arrange
        $jwtToken = $this->authenticate();

        $params = [
            'streamer_code' => 'MYS',
            'streamer_name' => 'MyStreamer'
        ];
        $params_updated = [
            'streamer_code' => 'MYS',
            'streamer_code_updated' => 'U',
            'streamer_name_updated' => 'UnknownStreamer'
        ];
        $headers = ['Authorization' => 'Bearer ' . $jwtToken];

        // Act
        $this->post('/streamers', $params, $headers);
        $response = $this->put('/streamers', $params_updated, $headers);

        // Assert
        assertThat($response->getStatusCode(), equalTo(StatusCode::STATUS_BAD_REQUEST));
    }

    public function testReturnsStatusCode400WhenMissingParams(): void
    {
        // Arrange
        $jwtToken = $this->authenticate();

        $params = [
            'streamer_code' => 'MYS',
            'streamer_name' => 'MyStreamer'
        ];
        $params_updated = [
            'streamer_code_updated' => 'U',
            'streamer_name_updated' => 'UnknownStreamer'
        ];
        $headers = ['Authorization' => 'Bearer ' . $jwtToken];

        // Act
        $this->post('/streamers', $params, $headers);
        $response = $this->put('/streamers', $params_updated, $headers);

        // Assert
        assertThat($response->getStatusCode(), equalTo(StatusCode::STATUS_BAD_REQUEST));
    }
}
