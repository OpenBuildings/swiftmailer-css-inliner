<?php

namespace Openbuildings\Swiftmailer\Test;

use Openbuildings\Swiftmailer\CssInlinerPlugin;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_Assert;
use Swift_Mailer;
use Swift_Message;
use Swift_NullTransport;

/**
 * @coversDefaultClass Openbuildings\Swiftmailer\CssInlinerPlugin
 */
class CssInlinerPluginTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Swift_Mailer
	 */
	private $mailer;

	/**
	 * @var string
	 */
	private $emailRaw;

	/**
	 * @var string
	 */
	private $emailConverted;

	public function setUp()
	{
		$dir = __DIR__.'/../fixtures/';

		$this->emailRaw = file_get_contents($dir.'emailRaw.html');
		$this->emailConverted = file_get_contents($dir.'emailConverted.html');

		$this->mailer = Swift_Mailer::newInstance(Swift_NullTransport::newInstance());
		$this->mailer->registerPLugin(new CssInlinerPlugin());
	}

	/**
	 * @return Swift_Message
	 */
	private function createMessage()
	{
		$message = Swift_Message::newInstance();

		$message->setFrom('test@example.com');
		$message->setTo('test2@example.com');
		$message->setSubject('Test');

		return $message;
	}

	/**
	 * @covers ::beforeSendPerformed
	 * @covers ::sendPerformed
	 */
	public function testHtmlBody()
	{
		$message = $this->createMessage();

		$message->setContentType('text/html');
		$message->setBody($this->emailRaw);

		$this->mailer->send($message);

		$this->assertEquals($this->emailConverted, $message->getBody());
	}

	/**
	 * @covers ::beforeSendPerformed
	 */
	public function testHtmlPart()
	{
		$message = $this->createMessage();

		$message->addPart($this->emailRaw, 'text/html');
		$message->addPart('plain part', 'text/plain');

		$this->mailer->send($message);

		$children = $message->getChildren();

		$this->assertEquals($this->emailConverted, $children[0]->getBody());
	}

	/**
	 * @covers ::__construct
	 */
	public function testDefaultConverterUsesInlineStylesBlock()
	{
		$plugin = new CssInlinerPlugin();

		$converter = PHPUnit_Framework_Assert::readAttribute($plugin, 'converter');

		$this->assertTrue(
			PHPUnit_Framework_Assert::readAttribute(
				$converter,
				'useInlineStylesBlock'
			),
			'setUseInlineStylesBlock() should be called on default $converter'
		);
	}

	/**
	 * @covers ::__construct
	 * @covers ::beforeSendPerformed
	 */
	public function testInjectedConverterIsUsedInsteadOfDefault()
	{
		$converterStub = $this
			->getMockBuilder('TijsVerkoyen\CssToInlineStyles\CssToInlineStyles')
			->setMethods(['convert', 'setUseInlineStylesBlock'])
			->getMock();

		// "our" converter should be used
		$converterStub
			->expects($this->atLeastOnce())
			->method('convert');

		$converterStub
			->expects($this->never())
			->method('setUseInlineStylesBlock');

		$message = $this->createMessage();
		$message->setContentType('text/html');

		$mailer = Swift_Mailer::newInstance(Swift_NullTransport::newInstance());
		$mailer->registerPlugin(new CssInlinerPlugin($converterStub));
		$mailer->send($message);
	}
}
