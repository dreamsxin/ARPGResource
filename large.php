<?php

// php merge.php --path=/home/myleft/work/rpg/孔雀坐骑/飞行 --num=48 --width=548 --height=354

$opts = new \Phalcon\Cli\Options('RPG CLI');
$opts->add([
    'type' => \Phalcon\Cli\Options::TYPE_STRING,
    'name' => 'path',
    'shortName' => 'p',
    'required' => true
]);
$opts->add([
    'type' => \Phalcon\Cli\Options::TYPE_INT,
    'name' => 'num',
    'shortName' => 'n',
    'required' => true,
]);
$opts->add([
    'type' => \Phalcon\Cli\Options::TYPE_INT,
    'name' => 'width',
    'shortName' => 'w',
    'required' => true,
]);
$opts->add([
    'type' => \Phalcon\Cli\Options::TYPE_INT,
    'name' => 'height',
    'shortName' => 'h',
    'required' => true,
]);
$vals = $opts->parse();
if (!$vals) {
	return;
}

$path = Phalcon\Arr::get($vals, 'path');
$num = Phalcon\Arr::get($vals, 'num');
$width = Phalcon\Arr::get($vals, 'width');
$height = Phalcon\Arr::get($vals, 'height');

$path = realpath($path);
if (!$path) {
	echo 'Path not exists.'.PHP_EOL;
	return;
}

$canvas = new Imagick;
$canvas->newimage($width*$num, $height*$num, new ImagickPixel('transparent'));
$canvas->setimageformat('png');

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
	$x = ($seq * $width) + $info[0];
	$y = ($seq * $height) + $info[1];
	$canvas->compositeimage($im, Imagick::COMPOSITE_OVER, $x, $y);
	$im->clear();
	$im->destroy();
	unset($im);
}
$canvas->writeimage($file->getPath() . DIRECTORY_SEPARATOR . 'all.png');
