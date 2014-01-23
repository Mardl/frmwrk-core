<?php

namespace Core;

use \jamwork\common\Registry;

/**
 * Class SystemMessages
 *
 * @category Core
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class SystemMessages
{

	/**
	 * Messages
	 *
	 * @var array
	 */
	private static $_messages = array();

	/**
	 * Add message
	 *
	 * @param string $content   Message
	 * @param string $category  Category
	 * @param array  $arguments (sprintf) Arguments for message
	 * @param bool   $html      HTML = true, Plaintext = false
	 * @return void
	 * @throws \InvalidArgumentException
	 */
	public static function add($content, $category = 'notice', $arguments = array(), $html = false)
	{
		if (!in_array($category, array('notice', 'warning', 'error', 'success')))
		{
			throw new \InvalidArgumentException('Invalid category');
		}

		try {
			Registry::getInstance()->getEventDispatcher()->triggerEvent(
				'onAddSystemMessage',
				$content,
				array(
					'category' => $category,
					'arguments' => $arguments,
					'html' => $html
				)
			);
		}
		catch (\Exception $e) {

		}

		$hash = md5($category.$content.serialize($arguments).$html);

		self::$_messages[$hash] = array(
			'category' => $category,
			'content' => $content,
			'arguments' => $arguments,
			'html' => $html
		);
	}

	/**
	 * Add notice
	 *
	 * @param string  $content   Message
	 * @param array   $arguments (sprintf) Arguments for message
	 * @param boolean $html      HTML = true, Plaintext = false
	 *
	 * @return void
	 */
	public static function addNotice($content, $arguments = array(), $html = false)
	{
		self::add($content, 'notice', $arguments, $html);
	}

	/**
	 * Add warning
	 *
	 * @param string  $content   Message
	 * @param array   $arguments (sprintf) Arguments for message
	 * @param boolean $html      HTML = true, Plaintext = false
	 *
	 * @return void
	 */
	public static function addWarning($content, $arguments = array(), $html = false)
	{
		self::add($content, 'warning', $arguments, $html);
	}

	/**
	 * Add success
	 *
	 * @param string  $content   Message
	 * @param array   $arguments (sprintf) Arguments for message
	 * @param boolean $html      HTML = true, Plaintext = false
	 *
	 * @return void
	 */
	public static function addSuccess($content, $arguments = array(), $html = false)
	{
		self::add($content, 'success', $arguments, $html);
	}

	/**
	 * Add error
	 *
	 * @param string  $content   Message
	 * @param array   $arguments (sprintf) Arguments for message
	 * @param boolean $html      HTML = true, Plaintext = false
	 *
	 * @return void
	 */
	public static function addError($content, $arguments = array(), $html = false)
	{
		self::add($content, 'error', $arguments, $html);
	}

	/**
	 * Get list of messages
	 *
	 * @return array
	 */
	public static function getList()
	{
		return self::$_messages;
	}

	/**
	 * Clear messages
	 *
	 * @return void
	 */
	public static function clear()
	{
		self::$_messages = array();
	}
}
