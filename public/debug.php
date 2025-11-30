<?php
echo "<h1>Отладка структуры файлов</h1>";
echo "<p>__DIR__: " . __DIR__ . "</p>";
echo "<p>Рабочая директория: " . getcwd() . "</p>";

echo "<h2>Содержимое папки:</h2>";
echo "<pre>";
$files = scandir(__DIR__);
foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    echo $file . " - " . (is_dir($path) ? "папка" : "файл") . "\n";
}
echo "</pre>";

echo "<h2>Проверка tpl папки:</h2>";
$tpl_path = __DIR__ . '/tpl';
if (is_dir($tpl_path)) {
    echo "✅ Папка tpl существует<br>";
    $tpl_files = scandir($tpl_path);
    foreach ($tpl_files as $file) {
        echo " - " . $file . "<br>";
    }
} else {
    echo "❌ Папка tpl НЕ существует<br>";
}

echo "<h2>Проверка header.php:</h2>";
$header_path = __DIR__ . '/tpl/header.php';
if (file_exists($header_path)) {
    echo "✅ Файл header.php существует";
} else {
    echo "❌ Файл header.php НЕ существует";
}
?>
