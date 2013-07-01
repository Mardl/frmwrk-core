<?php

namespace Core;

use jamwork\common\HttpResponse;

/**
 * Class Response
 *
 * @category Core
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class Response extends HttpResponse
{

	/**
	 * Redirect
	 * TODO: Funktion bereits im Jamwork. Wenn alles aktualisiert ist, kann sie gelöscht werden!
	 *
	 * @param string $url    Target url
	 * @param int    $status Status
	 * @return void
	 */
	public function redirect($url, $status = 302)
	{
		$this->setBody('');
		$this->setStatus($status);
		$this->addHeader('Location', $url);
		$this->flush();
		die();

		// für was leite ich von response ab?
		header("Status: $status");
		header("Location: $url");
		die();
	}
}
