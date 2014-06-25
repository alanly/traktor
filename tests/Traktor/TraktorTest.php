<?php

use Mockery as m;
use Traktor\Traktor;

class TraktorTest extends \PHPUnit_Framework_TestCase
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

    public function testGetRequest()
    {
        $response = json_encode(['foo' => 'bar']);

        $r = m::mock('GuzzleHttp\Message\ResponseInterface');
        $r->shouldReceive('json')->once();

        $c = m::mock('GuzzleHttp\Client');
        $c->shouldReceive('get')->once()->andReturn($r);

        $t = new Traktor;
        $t->setApiKey('foobar');

        $decoded = $t->get('foo.bar');

        $this->assertSame('bar', $decoded->foo);
    }
    
}
