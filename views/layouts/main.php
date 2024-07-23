<?php

/** @var \yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use yii\helpers\Url;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-full bg-gray-200">
<head>
    <base href="<?= Url::base(true) ?>">
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">    
    <?php $this->head() ?>    
    <link rel="icon" type="image/png" sizes="128x128" href="/img/icon-vacation.png">
</head>
<body class="font-sans leading-none text-gray-700 antialiased">
<?php $this->beginBody() ?>
<?= $content ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
