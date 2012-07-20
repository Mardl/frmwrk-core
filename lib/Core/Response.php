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
		header("Status: $status");
		header("Location: $url");
		die();
	}

}
