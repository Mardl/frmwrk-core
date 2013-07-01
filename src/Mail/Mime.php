<?php

namespace Core\Mail;

/**
 * Class Mime
 *
 * Encode files(string) as mime to send attachments via mail
 *
 * @category Core
 * @package  Core\Mail
 * @author   Ionel-Alex Caizer <ionel@dreiwerken.de>
 */
class Mime extends \ArrayObject
{

	/**
	 * MimeType
	 *
	 * @var   string
	 */
	protected $type = 'multipart/alternative';

	/**
	 * Boundary
	 *
	 * @var   string
	 */
	protected $boundary;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->boundary = 'MIME-BOUNDARY-' . sha1(uniqid('', true));
	}

	/**
	 * Convert object to string
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->execute();
	}

	/**
	 * Set mimetype
	 *
	 * @param string $mimetype MimeType
	 * @return string
	 */
	public function setType($mimetype)
	{
		$this->type = $mimetype;

		return $this->type;
	}

	/**
	 * Return mime headers for mail
	 *
	 * @return array
	 */
	public function getHeaders()
	{
		return array(
			'MIME-Version' => '1.0 (PHP Core\Mime)',
			'Content-Type' => $this->type . ';' . " " . 'boundary="' . $this->boundary . '"'
		);
	}

	/**
	 * Add attachment
	 *
	 * @param string $name  Name of attachment
	 * @param string $value Value of attachment
	 * @param string $type  Type of attachment
	 *
	 * @return array
	 */
	public function addPart($name, $value, $type)
	{
		$part = explode(';', $type);

		// Wenn $type == 'text/html' dann füge vorher eine alternative text/plain ein
		if ($part[0] == 'text/html' and $this->type == 'multipart/alternative')
		{
			$dom = new \Core\DOM\Document();
			@ $dom->loadHTML($value);
			$elements = $dom->getElementsByTagName('body');
			if (count($elements) == 1)
			{
				$this->addPart(null, $elements->item(0)->textContent, 'text/plain; charset=UTF-8');
			}
		}

		return $this[] = array(
			'name' => $name,
			'value' => $value,
			'type' => $type
		);
	}

	/**
	 * Return data as string
	 *
	 * @return string
	 */
	public function execute()
	{
		// Line separator (depends from mailserver?)
		$ls = "\n";

		$result = '';
		foreach ($this as $item)
		{
			$result .= '--' . $this->boundary . $ls;
			if ($item['value'] instanceOf Mime)
			{
				$result .= $this->prepareHeaders($item['value']->getHeaders()) . $ls;
			}
			else
			{
				$result .= 'Content-Type: ' . $item['type'] . $ls;
			}

			// Attachment name
			if ($item['name'])
			{
				$result .= "Content-Disposition: attachment;" . $ls . "\tfilename=\"" . $item['name'] . "\"" . $ls;
			}

			// Encoding and data
			if (substr($item['type'], 0, 5) == 'text/' || substr($item['type'], 0, 10) == 'multipart/')
			{
				$result .= "Content-Transfer-Encoding: 8bit" . $ls . $ls;
				$result .= $item['value'] . $ls;
			}
			else
			{
				$result .= "Content-Transfer-Encoding: base64" . $ls . $ls;
				$result .= chunk_split(base64_encode($item['value'])) . $ls;
			}
		}

		$result = rtrim($result) . $ls . '--' . $this->boundary . "--" . $ls;

		return $result;
	}

	/**
	 * Prepare headers
	 *
	 * @param array $headers Headers
	 * @return string
	 */
	private function prepareHeaders(array $headers)
	{
		$result = array();
		unset($headers['MIME-Version']);
		foreach ($headers as $key => $value)
		{
			$result[] = $key . ': ' . $value;
		}

		return implode("\n", $result);
	}
}
