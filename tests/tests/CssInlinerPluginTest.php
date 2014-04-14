<?php

use Openbuildings\Swiftmailer\CssInlinerPlugin;

/**
 * @group css-inliner-plugin
 */
class CssInlinerPluginTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var string
	 */
	private $email_raw;

	/**
	 * @var string
	 */
	private $email_converted;

	public function test_html_body()
	{
		$mailer = Swift_Mailer::newInstance(Swift_NullTransport::newInstance());

		$mailer->registerPLugin(new CssInlinerPlugin());

		$message = Swift_Message::newInstance();

		$message->setFrom('test@example.com');
		$message->setTo('test2@example.com');
		$message->setSubject('Test');
		$message->setContentType('text/html');
		$message->setBody($this->email_raw);

		$mailer->send($message);

		$this->assertEquals($this->email_converted, $message->getBody());
	}

	public function test_html_part()
	{
		$mailer = Swift_Mailer::newInstance(Swift_NullTransport::newInstance());

		$mailer->registerPLugin(new CssInlinerPlugin());

		$message = Swift_Message::newInstance();

		$message->setFrom('test@example.com');
		$message->setTo('test2@example.com');
		$message->setSubject('Test');
		$message->addPart($this->email_raw, 'text/html');
		$message->addPart('plain part', 'text/plain');

		$mailer->send($message);

		$children = $message->getChildren();

		$this->assertEquals($this->email_converted, $children[0]->getBody());
	}

	protected function setUp()
	{
		$dir = __DIR__.'/../fixtures/';

		$this->email_raw = file_get_contents($dir.'email_raw.html');
		$this->email_converted = file_get_contents($dir.'email_converted.html');
	}
}
