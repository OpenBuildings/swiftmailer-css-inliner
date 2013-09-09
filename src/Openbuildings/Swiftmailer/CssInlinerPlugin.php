<?php

namespace Openbuildings\Swiftmailer;

use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

/**
 * @package    openbuildings\swiftmailer-css-inliner
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class CssInlinerPlugin implements \Swift_Events_SendListener
{
	/**
	 * @param Swift_Events_SendEvent $evt
	 */
	public function beforeSendPerformed(\Swift_Events_SendEvent $evt)
	{
		$message = $evt->getMessage();

		$converter = new CssToInlineStyles();
		$converter->setEncoding($message->getCharset());
		$converter->setUseInlineStylesBlock(TRUE);

		if ($message->getContentType() === 'text/html') 
		{
			$converter->setCSS('');
			$converter->setHTML($message->getBody());

			$message->setBody($converter->convert());
		}

		foreach ($message->getChildren() as $part) 
		{
			if (strpos($part->getContentType(), 'text/html') === 0)
			{
				$converter->setCSS('');
				$converter->setHTML($part->getBody());

				$part->setBody($converter->convert());
			}
		}
	}

	/**
	 * Do nothing
	 *
	 * @param Swift_Events_SendEvent $evt
	 */
	public function sendPerformed(\Swift_Events_SendEvent $evt)
	{
		// Do Nothing
	}
}
