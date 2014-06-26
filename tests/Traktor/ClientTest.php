<?php

use Mockery as m;
use Traktor\Client as Traktor;

class ClientTest extends \PHPUnit_Framework_TestCase
{

    public function tearDown()
    {
        m::close();
    }

    public function testSettingApiKey()
    {
        $t = new Traktor;
        $t->setApiKey('foobar');

        $this->assertSame('foobar', $t->getApiKey());
    }

    public function testGetRequestForSingleObject()
    {
        $response = new stdClass;
        $response->foo = 'bar';

        $r = $this->getResponseMock(200, $response);
        $c = $this->getGuzzleGetClientMock($r);

        $t = new Traktor($c);
        $t->setApiKey('foobar');

        $decoded = $t->get('foo.bar');

        $this->assertSame('bar', $decoded->foo);
    }

    public function testGetRequestForArrayOfObjects()
    {
        $resp1 = new stdClass;
        $resp1->foo = 'bar';

        $resp2 = new stdClass;
        $resp2->foo = 'baz';

        $response = [$resp1, $resp2];

        $r = $this->getResponseMock(200, $response);
        $c = $this->getGuzzleGetClientMock($r);

        $t = new Traktor($c);
        $t->setApiKey('foobar');

        $decoded = $t->get('foo.bar');

        $this->assertSame('bar', $decoded[0]->foo);
        $this->assertSame('baz', $decoded[1]->foo);
    }

    /**
     * @expectedException Traktor\Exception\AuthorizationException
     */
    public function testExceptionOnAuthorizationError()
    {
        $response = new stdClass;
        $response->status = 'failure';
        $response->error = 'authorization mock';

        $r = $this->getResponseMock(401, $response);
        $c = $this->getGuzzleGetClientMock($r);

        $t = new Traktor($c);
        $t->setApiKey('foobar');

        $result = $t->get('foo.bar');
    }

    /**
     * @expectedException Traktor\Exception\AvailabilityException
     */
    public function testExceptionOnAvailabilityError()
    {
        $response = new stdClass;
        $response->status = 'failure';
        $response->error = 'downtime mock';

        $r = $this->getResponseMock(503, $response);
        $c = $this->getGuzzleGetClientMock($r);

        $t = new Traktor($c);
        $t->setApiKey('foobar');

        $result = $t->get('foo.bar');
    }

    /**
     * @expectedException Traktor\Exception\UnknownMethodException
     */
    public function testExceptionOnBadMethodCall()
    {
        $response = new stdClass;
        $response->error = 'bar';

        $r = $this->getResponseMock(404, $response);
        $r->shouldReceive('getBody')->andReturn('mock body');

        $c = $this->getGuzzleGetClientMock($r);

        $t = new Traktor($c);
        $t->setApiKey('foobar');

        $result = $t->get('foo.bar');
    }

    /**
     * @expectedException Traktor\Exception\RequestException
     */
    public function testExceptionOnUnknownError()
    {
        $response = new stdClass;
        $response->foo = 'bar';

        $r = $this->getResponseMock(900, $response);
        $r->shouldReceive('getBody')->andReturn('mock body');

        $c = $this->getGuzzleGetClientMock($r);

        $t = new Traktor($c);
        $t->setApiKey('foobar');

        $result = $t->get('foo.bar');
    }

    /**
     * @expectedException Traktor\Exception\MissingApiKeyException
     */
    public function testExceptionOnMissingApiKey()
    {
        $t = new Traktor;

        $result = $t->get('foo.bar');
    }

    protected function getResponseMock($statusCode, $json)
    {
        $r = m::mock('GuzzleHttp\Message\ResponseInterface');
        $r->shouldReceive('getStatusCode')->andReturn($statusCode);
        $r->shouldReceive('json')->andReturn($json);

        return $r;
    }

    protected function getGuzzleGetClientMock($response)
    {
        $c = m::mock('GuzzleHttp\Client');
        $c->shouldReceive('get')->andReturn($response);

        return $c;
    }
    
}
