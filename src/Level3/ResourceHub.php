<?php
namespace Level3;

use Pimple;
use Level3\Resources\DeleteInterface;
use Level3\Resources\GetInterface;
use Level3\Resources\PostInterface;
use Level3\Resources\PutInterface;


class ResourceHub extends Pimple {
    private $mapper;
    private $baseURI = '/';

    public function setMapper(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getMapper()
    {
        return $this->mapper;
    }

    public function setBaseURI($uri)
    {
        if ( $uri[strlen($uri)-1] != '/' ) $uri .= '/';
        $this->baseURI = $uri;
    }

    public function getBaseURI()
    {
        return $this->baseURI;
    }

    public function getURI($key, $method, array $parameters = array())
    {
        return $this->mapper->getURI(sprintf('%s:%s', $key, $method), $parameters);
    }

    public function boot()
    {
        foreach($this->keys() as $key) {
            $this->map($key);
        }
    }

    private function map($key)
    {
        $generalURI = $this->baseURI . $key;
        $particularURI = $this->baseURI . $key . '/{id}';

        if ($this[$key] instanceOf GetInterface) {
            $this->mapper->mapList($generalURI, sprintf('%s:list', $key));
            $this->mapper->mapGet($particularURI, sprintf('%s:get', $key));
        }

        if ($this[$key] instanceOf PostInterface) {
            $this->mapper->mapPost($particularURI, sprintf('%s:post', $key));
        }

        if ($this[$key] instanceOf PutInterface) {
            $this->mapper->mapPut($generalURI, sprintf('%s:put', $key));
        }

        if ($this[$key] instanceOf DeleteInterface) {
            $this->mapper->mapDelete($particularURI, sprintf('%s:delete', $key));
        }
    }
}