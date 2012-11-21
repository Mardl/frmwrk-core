<?php
/**
 * Core\Response-Class
 *
 * PHP version 5.3
 *
 * @category Routing
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */

namespace Core;
use jamwork\common\HttpResponse;

/**
 * Response
 *
 * @category Routing
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class Response extends HttpResponse
{

	/**
	 * Redirect
	 *
	 * @param string  $url    Target url
	 * @param integer $status Status
	 * 
	 * @return void
	 */
	public function redirect($url, $status = 302)
	{
		$this->setBody('');
		$this->setStatus($status);
		$this->addHeader('Location',$url);
		$this->flush();
		die();

		/* für was leite ich von response ab? */
		header("Status: $status");
		header("Location: $url");
		die();
	}

}
