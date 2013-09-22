<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Messages;

use Level3\Resource;
use Level3\Resource\Formatter;

use Teapot\StatusCode;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Response extends SymfonyResponse
{
    protected $resource;
    protected $formatter;

    public function setFormatter(Formatter $formatter)
    {
        $this->formatter = $formatter;
    }

    public function getFormatter()
    {
        return $this->formatter;
    }

    public function setResource(Resource $resource)
    {
        $this->resource = $resource;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function addHeader($header, $value)
    {   
        $this->headers->set($header, $value, false);
    }

    public function setHeader($header, $value)
    {
        $this->headers->set($header, $value, true);
    }

    public function getHeaders($header)
    {
        return $this->headers->get($header, null, false);
    }

    public function getHeader($header)
    {
        return $this->headers->get($header);
    }

    public function getContent()
    {
        if (!$this->formatter instanceOf Formatter) {
            if ($this->resource instanceOf Resource)
                return '';
            }
        }

        $this->resource->setFormatter($this->formatter);
        return $this->resource->format();
    }

    public function sendContent()
    {
        echo $this->getContent();

        return $this;
    }

    public function setContentType($contentType)
    {
        $this->setHeader('Content-Type', $contentType);
    }
}
