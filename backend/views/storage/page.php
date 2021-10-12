<?php
$this->title = _e('Storage browser');

echo $this->render('browser', [
    'path_name' => $path_name
]);

if (isset($path) && $path) {
    $script = "storage_browser_path = '{$path}';";
    $this->registerJs($script);
}

$this->registerJs(
    <<<JS
    $(document).ready(function () {
        storageBrowserInit();
    });
JS
);
