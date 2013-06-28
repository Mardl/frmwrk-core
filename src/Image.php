<?php

namespace Core;

use Imagick as ImageMagick;

/**
 * Class Image
 * Extension for Imagick
 *
 * Requires PECL::Imagick 3?
 *
 * Install:
 * aptitude install libmagick9-dev
 * pecl install imagick
 *
 * @category Core
 * @package  Core
 * @author   Ionel-Alex Caizer <ionel@dreiwerken.de>
 */
class Image extends ImageMagick
{

	/**
	 * Offset x
	 *
	 * @var int
	 */
	protected $offsetX = 0;

	/**
	 * Offset y
	 *
	 * @var int
	 */
	protected $offsetY = 0;

	/**
	 * Set offset for Imagick::brandImage
	 *
	 * @param int $x X-Position.
	 * @param int $y Y-Position.
	 *
	 * @return void
	 */
	public function setOffset($x, $y)
	{
		if ($x < 0)
		{
			$x = ($this->getImageWidth() + $x);
		}

		if ($y < 0)
		{
			$y = ($this->getImageHeight() + $y);
		}

		$this->offsetX = $x;
		$this->offsetY = $y;
	}

	/**
	 * Get offset x
	 *
	 * @return int
	 */
	public function getOffsetX()
	{
		return $this->offsetX;
	}

	/**
	 * Get offset y
	 *
	 * @return int
	 */
	public function getOffsetY()
	{
		return $this->offsetY;
	}

	/**
	 * Create thumbnail with canvas
	 *
	 * @param int $width
	 * @param int $height
	 * @return void
	 */
	public function thumbnailWithCanvas($width, $height)
	{
		$this->thumbnailImage($width, $height, true);
		$geometry = $this->getImageGeometry();

		$canvas = new self();
		$canvas->newImage($width, $height, '#ffffff', 'jpg');
		$x = ($width - $geometry['width']) / 2;
		$y = ($height - $geometry['height']) / 2;
		$canvas->compositeImage($this, ImageMagick::COMPOSITE_OVER, $x, $y);
		$this->setImage($canvas);
	}

	/**
	 * Resize width to $width
	 *
	 * @param int $width
	 * @return void
	 */
	public function resizeWidthTo($width)
	{
		$this->thumbnailImage($width, 0, false);
	}

	/**
	 * Resize height to $height
	 *
	 * @param integer $height
	 * @return void
	 */
	public function resizeHeightTo($height)
	{
		$this->thumbnailImage(0, $height, false);
	}

	/**
	 * Resize longer side to $length
	 *
	 * @param int $length
	 * @return void
	 */
	public function resizeLongerSideTo($length)
	{
		$this->thumbnailImage($length, $length, true);
	}

	/**
	 * @param \Imagick $watermark Watermark
	 * @return object
	 */
	public function brandImage(ImageMagick $watermark)
	{
		return $this->compositeImage($watermark, ImageMagick::COMPOSITE_DEFAULT, ($this->getOffsetX() - $watermark->getOffsetX()), ($this->getOffsetY() - $watermark->getOffsetY()));
	}
}
