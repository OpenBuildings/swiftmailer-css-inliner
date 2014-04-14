<?php

use Openbuildings\Swiftmailer\CssInlinerPlugin;

/**
 * @group css-inliner-plugin
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
	private $email_raw;

	/**
	 * @var string
	 */
	private $email_converted;

	public function test_html_body()
	{
		$message = $this->create_message();

		$message->setContentType('text/html');
		$message->setBody($this->email_raw);

		$this->mailer->send($message);

		$this->assertEquals($this->email_converted, $message->getBody());
	}

	public function test_html_part()
	{
		$message = $this->create_message();

		$message->addPart($this->email_raw, 'text/html');
		$message->addPart('plain part', 'text/plain');

		$this->mailer->send($message);

		$children = $message->getChildren();

		$this->assertEquals($this->email_converted, $children[0]->getBody());
	}

	public function test_default_converter_uses_inline_styles_block()
	{
		$plugin = new CssInlinerPlugin();

		$converter = \PHPUnit_Framework_Assert::readAttribute($plugin, 'converter');

		$this->assertTrue(
			\PHPUnit_Framework_Assert::readAttribute($converter, 'useInlineStylesBlock'),
			'setUseInlineStylesBlock() should be called on default $converter'
		);
	}

	public function test_injected_conveter_is_used_istead_of_default()
	{
		$converterStub = $this
			->getMockBuilder('TijsVerkoyen\CssToInlineStyles\CssToInlineStyles')
			->setMethods(array('convert', 'setUseInlineStylesBlock'))
			->getMock();

		// "our" converter should be used
		$converterStub
			->expects($this->atLeastOnce())
			->method('convert');

		$converterStub
			->expects($this->never())
			->method('setUseInlineStylesBlock');

		$message = $this->create_message();
		$message->setContentType('text/html');

		$mailer = Swift_Mailer::newInstance(Swift_NullTransport::newInstance());
		$mailer->registerPlugin(new CssInlinerPlugin($converterStub));
		$mailer->send($message);
	}

	protected function setUp()
	{
		$dir = __DIR__.'/../fixtures/';

		$this->email_raw = file_get_contents($dir.'email_raw.html');
		$this->email_converted = file_get_contents($dir.'email_converted.html');

		$this->mailer = Swift_Mailer::newInstance(Swift_NullTransport::newInstance());
		$this->mailer->registerPLugin(new CssInlinerPlugin());
	}

	/**
	 * @return Swift_Message
	 */
	private function create_message()
	{
		$message = Swift_Message::newInstance();

		$message->setFrom('test@example.com');
		$message->setTo('test2@example.com');
		$message->setSubject('Test');

		return $message;
	}
}
