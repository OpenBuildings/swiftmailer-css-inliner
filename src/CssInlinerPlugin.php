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
			$this->converter->setUseInlineStylesBlock(true);
		}
	}

	/**
	 * @param Swift_Events_SendEvent $event
	 */
	public function beforeSendPerformed(Swift_Events_SendEvent $event)
	{
		$message = $event->getMessage();

		if ($message->getContentType() === 'text/html') {
			$this->converter->setCSS('');
			$this->converter->setHTML($message->getBody());

			$message->setBody($this->converter->convert());
		}

		foreach ($message->getChildren() as $part) {
			if (strpos($part->getContentType(), 'text/html') === 0) {
				$this->converter->setCSS('');
				$this->converter->setHTML($part->getBody());

				$part->setBody($this->converter->convert());
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
