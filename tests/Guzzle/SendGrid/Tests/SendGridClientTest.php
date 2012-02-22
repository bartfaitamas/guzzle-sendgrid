<?php

namespace Guzzle\SendGrid\Tests;

use Guzzle\SendGrid\SendGridClient;

class SendGridClientTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testGetBounces()
    {
        $client = $this->getServiceBuilder()->get('test.sendgrid');
        $this->setMockResponse($client, 'bounces.get.json');

        $command = $client->getCommand('bounces.get', array('date' => '1',
                                                            'start_date' => new \DateTime('2012-02-10'),
                                                            'type' => 'hard'));
        $this->assertNotNull($command);
        $response = $client->execute($command);
        $this->assertCount(1, $response);
        $this->assertEquals("sendgrid.test.email.missing@gmail.com", $response[0]['email']);

        // testing execution without optional arguments
        $this->setMockResponse($client, 'bounces.get.json');
        $command = $client->getCommand('bounces.get', array('date' => '1'));
        $this->assertNotNull($command);
        $response = $client->execute($command);
        $this->assertCount(1, $response);
        $this->assertEquals("sendgrid.test.email.missing@gmail.com", $response[0]['email']);
    }

    public function testCountBounces()
    {
        $client = $this->getServiceBuilder()->get('test.sendgrid');
        $this->setMockResponse($client, 'bounces.count.json');

        $command = $client->getCommand('bounces.count');
        $response = $client->execute($command);
        $this->assertEquals(array('count' => 1), $response);
    }

    public function testDeleteBounces()
    {
        $client = $this->getServiceBuilder()->get('test.sendgrid');
        $this->setMockResponse($client, 'bounces.delete.json-error-noemail');
        try {
            $command = $client->getCommand('bounces.delete', array('email'=>'tobacco.org.test.invalid@gmail.com'));
            $this->assertNotNull($command);
            $response = $client->execute($command);
            $this->fail("Exception should be thrown on error");
        } catch (\RuntimeException $e) {
            $this->assertEquals("Email does not exist", $e->getMessage());
        }

        $this->setMockResponse($client, 'bounces.delete.json-success');
        $command = $client->getCommand('bounces.delete', array('email'=>'tobacco.org.test.invalid@gmail.com'));
        $this->assertNotNull($command);
        $response = $client->execute($command);
        $this->assertEquals(array('message' => 'success'), $response);

    }

    public function testDeleteSpamReports()
    {
        // testing when required arguments missing
        $client = $this->getServiceBuilder()->get('test.sendgrid');
        $this->setMockResponse($client, 'bounces.delete.json-error-noemail');
        $command = $client->getCommand('spamreports.delete');
        $this->assertNotNull($command);
        try {
            $response = $client->execute($command);
            $this->fail("spamreports should require email argument");
        } catch (\InvalidArgumentException $e) {
        }

        $this->setMockResponse($client, 'bounces.delete.json-error-noemail');
        $command = $client->getCommand('spamreports.delete', array('email'=>'tobacco.org.test.invalid@gmail.com'));
        $this->assertNotNull($command);
        try {
            $response = $client->execute($command);
            $this->fail("Exception should be thrown on error");
        } catch (\RuntimeException $e) {
            $this->assertEquals("Email does not exist", $e->getMessage());
        }

        $this->setMockResponse($client, 'bounces.delete.json-success');
        $command = $client->getCommand('spamreports.delete', array('email'=>'tobacco.org.test.invalid@gmail.com'));
        $this->assertNotNull($command);
        $response = $client->execute($command);
        $this->assertEquals(array('message' => 'success'), $response);

    }

    public function testStatsGet()
    {
        $client = $this->getServiceBuilder()->get('test.sendgrid');
        $this->setMockResponse($client, 'stats.get.json');
        $command = $client->getCommand('stats.get', array('start_date' => new \DateTime('2011-01-01'),
                                                          'end_date' => new \DateTime('2012-02-18')));
        $this->assertNotNull($command);
        $response = $client->execute($command);
        $this->assertCount(49, $response);
    }

}