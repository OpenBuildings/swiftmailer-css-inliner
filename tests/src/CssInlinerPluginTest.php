<?php

namespace Openbuildings\Swiftmailer\Test;

use Openbuildings\Swiftmailer\CssInlinerPlugin;
use PHPUnit\Framework\TestCase;
use Swift_Mailer;
use Swift_Message;
use Swift_NullTransport;

/**
 * @coversDefaultClass \Openbuildings\Swiftmailer\CssInlinerPlugin
 */
class CssInlinerPluginTest extends TestCase
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

        $this->mailer = new Swift_Mailer(new Swift_NullTransport);
        $this->mailer->registerPLugin(new CssInlinerPlugin());
    }

    /**
     * @return Swift_Message
     */
    private function createMessage()
    {
        $message = new Swift_Message();

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

        $this->assertStringEqualsStringWithoutIndentation($this->emailConverted, $message->getBody());
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

        $this->assertStringEqualsStringWithoutIndentation($this->emailConverted, $children[0]->getBody());
    }

    /**
     * @covers ::__construct
     * @covers ::beforeSendPerformed
     */
    public function testInjectedConverterIsUsedInsteadOfDefault()
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

        $message = $this->createMessage();
        $message->setContentType('text/html');

        $mailer = new Swift_Mailer(new Swift_NullTransport);
        $mailer->registerPlugin(new CssInlinerPlugin($converterStub));
        $mailer->send($message);
    }

    /**
     * This assert is an ugly hack aiming to fix an indent issue when using libxml < 2.9.5.
     *
     * @param string $expected
     * @param string $actual
     * @param string $message
     */
    private function assertStringEqualsStringWithoutIndentation($expected, $actual, $message = '')
    {
        $expected = preg_replace('/^[[:space:]]+|\n/m', '', $expected);
        $actual = preg_replace('/^[[:space:]]+|\n/m', '', $actual);

        $this->assertEquals($expected, $actual, $message);
    }
}
