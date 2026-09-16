<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrustedProxyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Railway (and similar PaaS hosts) terminate HTTPS at their edge and
     * forward plain HTTP internally, signalling the original scheme via
     * X-Forwarded-Proto. Without `trustProxies` configured, Laravel treats
     * every request as insecure HTTP — generated URLs (route()/url() and
     * therefore every form action and redirect) come out as http://, and
     * session cookies never get the Secure flag. This proves the app
     * actually honors that header rather than just asserting it in prose.
     */
    public function test_https_is_detected_via_forwarded_proto_header(): void
    {
        $response = $this->withServerVariables([
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'REMOTE_ADDR' => '10.0.0.1',
        ])->get('/');

        $response->assertOk();
        $this->assertTrue($response->baseRequest->isSecure());
        $this->assertStringStartsWith('https://', route('login'));
    }

    public function test_plain_http_without_the_header_is_still_treated_as_insecure(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $this->assertFalse($response->baseRequest->isSecure());
        $this->assertStringStartsWith('http://', route('login'));
    }
}
