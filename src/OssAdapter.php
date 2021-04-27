<?php

namespace Byrnes2014\Flysystem\Oss;

use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;
use League\Flysystem\Config;
use OSS\OssClient;
use OSS\Core\OssException;

class OssAdapter extends AbstractAdapter
{
    use NotSupportingVisibilityTrait;

    protected OssClient $client;

    /**
     * @var string oss bucket
     */
    protected string $bucket;

    public function __construct(OssClient $client, string $bucket, string $prefix = '')
    {
        $this->client = $client;
        $this->bucket = $bucket;
        $this->setPathPrefix($prefix);
    }

    public function write($path, $contents, Config $config): bool
    {
        $location = $this->applyPathPrefix($path);
        try {
            $this->client->putObject($this->bucket, $location, $contents);
        } catch (OssException $e) {
            return false;
        }
        return true;
    }

    public function writeStream($path, $resource, Config $config): bool
    {
        $location = $this->applyPathPrefix($path);
        $contents = stream_get_contents($resource);
        try {
            $this->client->putObject($this->bucket, $location, $contents);
        } catch (OssException $e) {
            return false;
        }
        return true;
    }

    public function update($path, $contents, Config $config)
    {
        return $this->write($path, $contents, $config);
    }

    public function updateStream($path, $resource, Config $config)
    {
        return $this->writeStream($path, $resource, $config);
    }

    public function rename($path, $newpath): bool
    {
        if (!$this->copy($path, $newpath)) {
            return false;
        }
        return $this->delete($path);
    }

    public function copy($path, $newpath): bool
    {
        $object = $this->applyPathPrefix($path);
        $newObject = $this->applyPathPrefix($newpath);
        try {
            $this->client->copyObject($this->bucket, $object, $this->bucket, $newObject);
        } catch (OssException $e) {
            return false;
        }
        return true;
    }

    public function delete($path)
    {
        $location = $this->applyPathPrefix($path);
        try {
            $this->client->deleteObject($this->bucket, $location);
        } catch (OssException $e) {
            return false;
        }
        return $this->has($path);
    }

    public function deleteDir($dirname)
    {
        // TODO: Implement deleteDir() method.
    }

    public function createDir($dirname, Config $config)
    {
        // TODO: Implement createDir() method.
    }


    public function has($path)
    {
        $object = $this->applyPathPrefix($path);
        return $this->client->doesObjectExist($this->bucket, $object);
    }

    public function read($path)
    {
        $location = $this->applyPathPrefix($path);
        try {
            $content = $this->client->getObject($this->bucket, $location);
        } catch (OssException $e) {
            return false;
        }
        return $content;

    }

    public function readStream($path)
    {

    }

    public function listContents($directory = '', $recursive = false)
    {

    }

    public function getMetadata($path)
    {
        $object = $this->applyPathPrefix($path);
        try {
            $objectMeta = $this->client->getObjectMeta($this->bucket, $object);
        } catch (OssException $e) {
            return false;
        }
        return $objectMeta;

    }

    public function getSize($path)
    {
        $object = $this->getMetadata($path);
        $object['size'] = $object['content-length'];
        return $object;
    }

    public function getMimetype($path)
    {
        $object = $this->getMetadata($path);
        $object['mimetype'] = $object['content-type'];
        return $object;
    }

    public function getTimestamp($path)
    {
        $object = $this->getMetadata($path);
        $object['timestamp'] = strtotime($object['last-modified']);
        return $object;
    }

    public function getUrl($path)
    {
        $metaData = $this->getMetadata($path);
        return $metaData['oss-request-url'];
    }

}