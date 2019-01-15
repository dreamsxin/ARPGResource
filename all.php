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
    'type' => \Phalcon\Cli\Options::TYPE_STRING,
    'name' => 'name',
    'required' => true
]);
$opts->add([
    'type' => \Phalcon\Cli\Options::TYPE_INT,
    'name' => 'num',
    'shortName' => 'n',
    'required' => true,
]);
$vals = $opts->parse();
if (!$vals) {
	return;
}

$path = Phalcon\Arr::get($vals, 'path');
$name = Phalcon\Arr::get($vals, 'name');
$num = Phalcon\Arr::get($vals, 'num');

$path = realpath($path);
if (!$path) {
	echo 'Path not exists.'.PHP_EOL;
	return;
}

$allcanvas = new Imagick;
for($i = 0; $i < 4; $i++) {
	$canvas = new Imagick;
	for ($j = 0; $j < $num / 4; $j++) {
		$filepath = $path.DIRECTORY_SEPARATOR.$name.$j.'.png';
		echo $filepath.PHP_EOL;
		if (!file_exists($filepath)) {
			continue;
		}
		$canvas->readImage($filepath);
	}
	$canvas->resetIterator();
	$im = $canvas->appendImages(false);
	$im->setimageformat('png');
	$allcanvas->addImage($im);
}
$allcanvas->resetIterator();
$im = $allcanvas->appendImages(true);
$im->setimageformat('png');
$im->writeimage($path . DIRECTORY_SEPARATOR . 'all.png');
