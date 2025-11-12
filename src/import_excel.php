<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$mysqli = new mysqli("localhost", "root", "", "college_schedule");
$mysqli->set_charset("utf8");

if (isset($_FILES['excelFile']) && $_FILES['excelFile']['error'] === UPLOAD_ERR_OK) {
    $fileTmp = $_FILES['excelFile']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($fileTmp);
    } catch (Exception $e) {
        die("Не вдалося відкрити файл Excel: " . $e->getMessage());
    }

    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();

$rows = $worksheet->toArray();

$importedSubjects = 0;

for ($i = 19; $i <= 32; $i++) {
    if (!isset($rows[$i])) {
        continue;
    }
    $subjectName = trim((string)($rows[$i][1] ?? ''));

    if (empty($subjectName) || mb_strlen($subjectName) < 3) {
        continue;
    }

    $stmt = $mysqli->prepare("INSERT IGNORE INTO subject (name) VALUES (?)");
    $stmt->bind_param("s", $subjectName);
    if ($stmt->execute()) {
        $importedSubjects++;
    }
    $stmt->close();
}

echo "✅ Імпорт завершено успішно! Додано $importedSubjects предметів.";

} else {
    echo "❌ Помилка при завантаженні файлу.";
}
?>