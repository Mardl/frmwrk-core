<?php
/**
 * Core\Image-Class
 *
 * PHP version 5.3
 *
 * @category Helper
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace Core;

use Imagick as ImageMagick;

/**
 * Extension for Imagick
 *
 * Requires PECL::Imagick 3?
 *
 * Install:
 * aptitude install libmagick9-dev
 * pecl install imagick
 */
class Image extends ImageMagick
{

	/**
	 * Offset x
	 *
	 * @var   integer
	 */
	protected $offsetX = 0;

	/**
	 * Offset y
	 *
	 * @var   integer
	 */
	protected $offsetY = 0;

	/**
	 * Set offset for Imagick::brandImage
	 *
	 * @param integer $x X-Position.
	 * @param integer $y Y-Position.
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
	 * @return integer
	 */
	public function getOffsetX()
	{
		return $this->offsetX;

	}

	/**
	 * Get offset y
	 *
	 * @return integer
	 */
	public function getOffsetY()
	{
		return $this->offsetY;

	}

	/**
	 * Create thumbnail with canvas
	 *
	 * @param integer $width
	 * @param integer $height
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
	 * @param integer $width
	 */
	public function resizeWidthTo($width)
	{
		$this->thumbnailImage($width, 0, false);
	}

	/**
	 * Resize height to $height
	 *
	 * @param integer $height
	 */
	public function resizeHeightTo($height)
	{
		$this->thumbnailImage(0, $height, false);
	}

	/**
	 * Resize longer side to $length
	 *
	 * @param integer $length
	 */
	public function resizeLongerSideTo($length)
	{
		$this->thumbnailImage($length, $length, true);
	}

	/**
	 * Brand image
	 *
	 * @param ImageMagick $watermark Watermark
	 *
	 * @return object
	 */
	public function brandImage(ImageMagick $watermark)
	{
		return $this->compositeImage($watermark, ImageMagick::COMPOSITE_DEFAULT, ($this->getOffsetX() - $watermark->getOffsetX()), ($this->getOffsetY() - $watermark->getOffsetY()));
	}
}