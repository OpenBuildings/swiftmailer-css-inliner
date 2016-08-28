<?php

namespace Openbuildings\Swiftmailer;

use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Swift_Events_SendListener;
use Swift_Events_SendEvent;

/**
 * @package    openbuildings\swiftmailer-css-inliner
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class CssInlinerPlugin implements Swift_Events_SendListener
{
	/**
	 * @var CssToInlineStyles
	 */
	private $converter;

	/**
	 * @param CssToInlineStyles $converter
	 */
	public function __construct(CssToInlineStyles $converter = null)
	{
		if ($converter) {
			$this->converter = $converter;
		} else {
			$this->converter = new CssToInlineStyles();
		}
	}

	/**
	 * @param Swift_Events_SendEvent $event
	 */
	public function beforeSendPerformed(Swift_Events_SendEvent $event)
	{
		$message = $event->getMessage();

		if ($message->getContentType() === 'text/html') {
			$message->setBody($this->converter->convert($message->getBody()));
		}

		foreach ($message->getChildren() as $part) {
			if (strpos($part->getContentType(), 'text/html') === 0) {
				$part->setBody($this->converter->convert($part->getBody()));
			}
		}
	}

	/**
	 * Do nothing
	 *
	 * @param Swift_Events_SendEvent $event
	 */
	public function sendPerformed(\Swift_Events_SendEvent $event)
	{
		// Do Nothing
	}
}
