<?php

// step3: 合并每20张到一页
// 扫描目录
$files = scandir(ROOT . '/com_a');
// 给图片分组
$i = $j = 0;
$group = array();
foreach ($files as $item) {
  if ($item === '.' || $item === '..' || strstr($item, '.db')) {
    continue;
  }
  $i++;
  $group[$j][] = $item;
  if ($i % 20 === 0) {
    $j++;
  }
}
$total = count($group);
// 按组拼接图片，A4纸尺寸，4x5的组合方式
foreach ($group as $k => $v) {
  $canvas = new Imagick;
  $canvas->newimage(2480, 3508, 'white');
  $canvas->setimageformat('png');
  $i = $j = 0;
  foreach ($v as $item) {
    $im = new Imagick(ROOT . '/com_a/' . $item);
    // 预留了150的左边距
    $x = 150 + $i * 570;
    // 130的顶边距
    $y = 130 + $j * 661;
    $canvas->compositeimage($im, Imagick::COMPOSITE_OVER, $x, $y);
    // 每4张一行
    if (($i + 1) % 4 === 0) {
      $i = 0;
      $j++;
    } else {
      $i++;
    }
  }
  $canvas->writeimage(ROOT . '/merge_a/' . $k . '.png');
  $c = $k + 1;
  echo "Group {$c}/{$total} done.\n";
}