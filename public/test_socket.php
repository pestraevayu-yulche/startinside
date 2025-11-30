<?php
echo "<h3>🔧 Test Socket Connection</h3>";

// Проверяем возможность подключения к порту 5432
$socket = @fsockopen('localhost', 5432, $errno, $errstr, 5);

if ($socket) {
    echo "✅ Успех! Можем подключиться к localhost:5432<br>";
    fclose($socket);
} else {
    echo "❌ Не можем подключиться к localhost:5432<br>";
    echo "Ошибка: $errstr (код: $errno)<br>";
}

// Проверяем 127.0.0.1
$socket = @fsockopen('127.0.0.1', 5432, $errno, $errstr, 5);

if ($socket) {
    echo "✅ Успех! Можем подключиться к 127.0.0.1:5432<br>";
    fclose($socket);
} else {
    echo "❌ Не можем подключиться к 127.0.0.1:5432<br>";
    echo "Ошибка: $errstr (код: $errno)<br>";
}
?>
