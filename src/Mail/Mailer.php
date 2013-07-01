<?php

namespace Core\Mail;

use Exception, Core\Mail\Mime;

/**
 * Class Mailer
 *
 * @category Core
 * @package  Core\Mail
 * @author   Ionel-Alex Caizer <ionel@dreiwerken.de>
 */
class Mailer
{

	/**
	 * Envelope (for Sendmail)
	 *
	 * @var string
	 */
	protected $envelope;

	/**
	 * To
	 *
	 * @var array
	 */
	protected $to = array();

	/**
	 * ReplyTo
	 *
	 * @var array
	 */
	protected $replyTo = array();

	/**
	 * Subject
	 *
	 * @var string
	 */
	protected $subject;

	/**
	 * Headers
	 *
	 * @var array
	 */
	protected $headers = array();

	/**
	 * Body
	 *
	 * @var string
	 */
	protected $body;

	/**
	 * Set from
	 *
	 * @param string $email    Email
	 * @param string $username Username
	 * @return string
	 */
	public function setFrom($email, $username = null)
	{
		$this->envelope = $email;
		if ($username)
		{
			return $this->addHeader('From', $username . ' <' . $email . '>');
		}
		else
		{
			return $this->addHeader('From', $email);
		}
	}

	/**
	 * Set to (Receiver)
	 *
	 * @param string $email    Email
	 * @param string $username Username
	 * @return array
	 */
	public function setTo($email, $username = null)
	{
		if ($username)
		{
			$this->to = array($username . ' <' . $email . '>');
		}
		else
		{
			$this->to = array($email);
		}

		return $this->to;

	}

	/**
	 * Add to (Receiver)
	 *
	 * @param string $email    Email
	 * @param string $username Username
	 * @return array
	 */
	public function addTo($email, $username = null)
	{
		if ($username)
		{
			$this->to[] = $username . ' <' . $email . '>';
		}
		else
		{
			$this->to[] = $email;
		}

		return $this->to;
	}

	/**
	 * Add reply to
	 *
	 * @param string $email    Email
	 * @param string $username Username
	 * @return array
	 */
	public function addReplyTo($email, $username = null)
	{
		if ($username)
		{
			$this->replyTo[] = $username . ' <' . $email . '>';
		}
		else
		{
			$this->replyTo[] = $email;
		}

		return $this->replyTo;

	}

	/**
	 * @param string $key   key
	 * @param string $value value
	 * @return array
	 * @throws \Exception
	 */
	public function addHeader($key, $value)
	{
		if (strpos($value, "\n") !== false)
		{
			throw new Exception('Header must not contain newlines');
		}
		$this->headers[$key] = $value;

		return $this->headers;
	}

	/**
	 * Add headers
	 *
	 * @param array $headers headers
	 * @return array
	 */
	public function addHeaders(array $headers)
	{
		foreach ($headers as $key => $value)
		{
			$this->addHeader($key, $value);
		}

		return $this->headers;
	}

	/**
	 * Set subject
	 *
	 * @param string $subject Subject
	 * @return string
	 */
	public function setSubject($subject)
	{
		$this->subject = $subject;

		return $this->subject;
	}

	/**
	 * Set body
	 *
	 * @param string $body Body
	 * @return string
	 */
	public function setBody($body)
	{
		if ($body instanceOf MIME)
		{
			$this->addHeaders($body->getHeaders());
		}
		$this->body = $body;

		return $this->body;
	}

	/**
	 * Send email with sendmail
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function send()
	{
		$to = implode(', ', $this->to);
		$headers = $this->prepareHeaders($this->headers);

		// Detect charset
		$subject = $this->subject;
		$encoding = mb_detect_encoding($subject, mb_detect_order(), true);
		if ($encoding != 'ASCII')
		{
			$subject = '=?' . $encoding . '?B?' . base64_encode($this->subject) . '?=';
		}

		$status = mail($to, $subject, $this->body, $headers, $this->prepareSendmailParams());
		if (!$status)
		{
			throw new Exception('Cannot send email');
		}

		return $status;
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
		if (count($this->replyTo) > 0)
		{
			$headers['Reply-To'] = implode(', ', $this->replyTo);
		}
		foreach ($headers as $key => $value)
		{
			$result[] = $key . ': ' . $value;
		}

		return implode("\n", $result);
	}

	/**
	 * Prepare sendmail parameters
	 *
	 * @return string
	 */
	private function prepareSendmailParams()
	{
		$result = array();
		$result[] = '-f ' . $this->envelope;

		return implode(' ', $result);
	}
}
