<?php

use Openbuildings\Swiftmailer\CssInlinerPlugin;

/**
 * @group   css-inliner-plugin
 */
class CssInlinerPluginTest extends PHPUnit_Framework_TestCase {

	public $html = <<<HTML
<html>
	<head>
		<style>
			.block {
				width: 100px;
				height: 20px;
			}
			div.block ul li.small {
				margin: 10px;
			}
		</style>
	</head>
	<body>
		<div class="block">
			text

			<ul>
				<li>
					Big list
				</li>
				<li class="small">
					Small list
				</li>
			</ul>
		</div>
	</body>
</html>
HTML;

	public $converted_html = <<<CONVERTED
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html><head><style>
			.block {
				width: 100px;
				height: 20px;
			}
			div.block ul li.small {
				margin: 10px;
			}
		</style></head><body>
		<div class="block" style="height: 20px; width: 100px;">
			text

			<ul><li>
					Big list
				</li>
				<li class="small" style="margin: 10px;">
					Small list
				</li>
			</ul></div>
	</body></html>

CONVERTED;

	public function test_html_body()
	{
		$mailer = Swift_Mailer::newInstance(Swift_NullTransport::newInstance());

		$mailer->registerPLugin(new CssInlinerPlugin());

		$message = Swift_Message::newInstance();

		$message->setFrom('test@example.com');
		$message->setTo('test2@example.com');
		$message->setSubject('Test');
		$message->setContentType('text/html');
		$message->setBody($this->html);

		$mailer->send($message);
		
		$this->assertEquals($this->converted_html, $message->getBody());
	}


	public function test_html_part()
	{
		$mailer = Swift_Mailer::newInstance(Swift_NullTransport::newInstance());

		$mailer->registerPLugin(new CssInlinerPlugin());

		$message = Swift_Message::newInstance();

		$message->setFrom('test@example.com');
		$message->setTo('test2@example.com');
		$message->setSubject('Test');
		$message->addPart($this->html, 'text/html');
		$message->addPart('plain part', 'text/plain');

		$mailer->send($message);

		$children = $message->getChildren();
		
		$this->assertEquals($this->converted_html, $children[0]->getBody());
	}
}