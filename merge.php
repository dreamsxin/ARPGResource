<?php

$opts = new \Phalcon\Cli\Options('RPG CLI');
$opts->add([
    'type' => \Phalcon\Cli\Options::TYPE_STRING,
    'name' => 'path',
    'shortName' => 'p',
    'required' => true
]);
$opts->add([
    'type' => \Phalcon\Cli\Options::TYPE_STRING,
    'name' => 'name',
    'shortName' => 'n',
    'required' => true
]);
$vals = $opts->parse();
if (!$vals) {
	return;
}

$path = Phalcon\Arr::get($vals, 'path');
$name = Phalcon\Arr::get($vals, 'name');

$path = realpath($path);
if (!$path) {
	echo 'Path not exists.'.PHP_EOL;
	return;
}

$dir = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
$iter = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::SELF_FIRST);
foreach ($iter as $key => $file) {
	if ($file->getExtension() != 'png') {
		continue;
	}
	echo $file->getFilename().PHP_EOL;
	$seq = $file->getBasename('.png');
	$infopath = $file->getPath().DIRECTORY_SEPARATOR.$file->getFilename().'.info.txt';
	if (!file_exists($infopath)) {
		continue;
	}
	$infofileobj = new SplFileObject($infopath);
	$infofileobj->setFlags(SplFileObject::READ_CSV);
	$infofileobj->seek(1);
	$info = $infofileobj->current();

	$im = new Imagick($file->getPathname());
	$x = $info[0];
	$y = $info[1];
	$width = $info[2];
	$height = $info[3];

	$canvas = new Imagick;
	$canvas->newimage($width, $height, new ImagickPixel('transparent'), 'png');
	$canvas->compositeimage($im, Imagick::COMPOSITE_OVER, $x, $y);
	$canvas->writeimage($file->getPath() . DIRECTORY_SEPARATOR . $name.$seq.'.png');
	unset($im);
}
