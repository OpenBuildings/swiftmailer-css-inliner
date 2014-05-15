<?php

namespace Openbuildings\Swiftmailer;

use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

/**
 * @package    openbuildings\swiftmailer-css-inliner
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 *
 * @method void setCleanup(bool $on = true)
 * @method void setUseInlineStylesBlock(bool $on = true)
 * @method void setStripOriginalStyleTags(bool $on = true)
 * @method void setExcludeMediaQueries(bool $on = true)
 * @method void setCss(string $css)
 * @method void setEncoding(string $encoding)
 * @method void setHTML(string $html)
 */
class CssInlinerPlugin implements \Swift_Events_SendListener
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
		if ($converter)
		{
			$this->converter = $converter;
		}
		else
		{
			$this->converter = new CssToInlineStyles();
			$this->converter->setUseInlineStylesBlock(TRUE);
		}
	}

	/**
	 * Gives possibility to call CssToInlineStyles setters directly
	 *
	 * @param string $name method name
	 * @param array $arguments method arguments
	 * @return mixed
	 */
	public function __call($name, $arguments)
	{
		if ((substr($name, 0, 3) === 'set') && method_exists($this->converter, $name))
		{
			return call_user_func_array(array($this->converter, $name), $arguments);
		}

		trigger_error(sprintf('Call to undefined function: %s::%s().', get_class($this), $name), E_USER_ERROR);
	}

	/**
	 * @param \Swift_Events_SendEvent $evt
	 */
	public function beforeSendPerformed(\Swift_Events_SendEvent $evt)
	{
		$message = $evt->getMessage();

		$this->converter->setEncoding($message->getCharset());

		if ($message->getContentType() === 'text/html')
		{
			$this->converter->setCSS('');
			$this->converter->setHTML($message->getBody());

			$message->setBody($this->converter->convert());
		}

		foreach ($message->getChildren() as $part)
		{
			if (strpos($part->getContentType(), 'text/html') === 0)
			{
				$this->converter->setCSS('');
				$this->converter->setHTML($part->getBody());

				$part->setBody($this->converter->convert());
			}
		}
	}

	/**
	 * Do nothing
	 *
	 * @param \Swift_Events_SendEvent $evt
	 */
	public function sendPerformed(\Swift_Events_SendEvent $evt)
	{
		// Do Nothing
	}
}
