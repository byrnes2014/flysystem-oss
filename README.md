<h1 align="center"> flysystem-oss </h1>

<p align="center"> .</p>

## Installing

```shell
$ composer require byrnes2014/flysystem-oss -vvv
```

## Usage

```php
    use Byrnes2014\Flysystem\Oss\QssAdapter;
```

## Api

```php

 $flysystem->write('file.md', 'contents');

 $flysystem->write('file.md', 'http://httpbin.org/robots.txt', ['mime' => 'application/redirect302']);
 
 $flysystem->writeStream('file.md', fopen('path/to/your/local/file.jpg', 'r'));

 $flysystem->update('file.md', 'new contents');

 $flysystem->updateStream('file.md', fopen('path/to/your/local/file.jpg', 'r'));

 $flysystem->rename('foo.md', 'bar.md');

 $flysystem->copy('foo.md', 'foo2.md');

 $flysystem->delete('file.md');

 $flysystem->has('file.md');

string|false $flysystem->read('file.md');

array $flysystem->listContents();

array $flysystem->getMetadata('file.md');

int $flysystem->getSize('file.md');

string $flysystem->getAdapter()->getUrl('file.md'); 

string $flysystem->getMimetype('file.md');

int $flysystem->getTimestamp('file.md');
```

## License

MIT