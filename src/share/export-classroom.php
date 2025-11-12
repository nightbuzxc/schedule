<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

$group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;
$format = $_GET['format'] ?? 'pdf';

if (!$group_id) {
    die('Не вказано group_id');
}

$mysqli = new mysqli('localhost', 'root', '', 'college_schedule');
$mysqli->set_charset('utf8');

if ($mysqli->connect_error) {
    die('Помилка зʼєднання: ' . $mysqli->connect_error);
}

$groupResult = $mysqli->query("SELECT name FROM `groups` WHERE id = $group_id");
if ($groupResult->num_rows == 0) {
    die('Групу не знайдено');
}
$groupName = $groupResult->fetch_assoc()['name'];

$scheduleResult = $mysqli->query("
    SELECT 
        s.lesson_number_id,
        sub.id AS subject_id,
        s.day_of_week,
        g.name AS group_name,
        sub.name,
        t.full_name AS teachers,
        c.name AS classroom,
        s.lesson_type
    FROM schedule s
    JOIN `groups` g ON s.group_id = g.id
    JOIN subject sub ON s.subject_id = sub.id
    JOIN teachers t ON sub.teacher_id = t.id
    JOIN classroom c ON sub.classroom_id = c.id
    WHERE s.group_id = $group_id
    ORDER BY s.lesson_number_id
");

$scheduleData = [];
while ($row = $scheduleResult->fetch_assoc()) {
    $scheduleData[] = $row;
}

switch ($format) {
    case 'pdf':
        exportPDF($groupName, $scheduleData);
        break;

    case 'excel':
        exportExcel($groupName, $scheduleData);
        break;

    case 'qr':
        exportQR($group_id);
        break;

    default:
        die('Невідомий формат експорту');
}

function exportPDF($groupName, $data) {
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('dejavusans', '', 10);
    $pdf->Cell(0, 10, "Розклад для групи: $groupName", 0, 1, 'C');

    $tbl = '<table border="1" cellpadding="4">
        <tr>
            <th>№ пари</th>
            <th>День</th>
            <th>Тип</th>
            <th>Кабінет</th>
        </tr>';

    foreach ($data as $row) {
    $tbl .= '<tr>
        <td>' . htmlspecialchars($row['lesson_number_id']) . '</td>
        <td>' . htmlspecialchars($row['day_of_week']) . '</td>
        <td>' . htmlspecialchars($row['lesson_type']) . '</td>
        <td>' . htmlspecialchars($row['classroom']) . '</td>
    </tr>';
}


    $tbl .= '</table>';
    $pdf->writeHTML($tbl, true, false, false, false, '');
    $pdf->Output("schedule_$groupName.pdf", 'D');
}

function exportExcel($groupName, $data) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', "Розклад для групи: $groupName");
    $sheet->fromArray(['№ пари', 'День', 'Тип', 'Кабінет'], NULL, 'A2');

    $rowNum = 3;
    $rowNum = 3;
foreach ($data as $row) {
    $sheet->setCellValue("B$rowNum", $row['lesson_number_id']);
    $sheet->setCellValue("A$rowNum", $row['day_of_week']);
    $sheet->setCellValue("C$rowNum", $row['lesson_type']);
    $sheet->setCellValue("D$rowNum", $row['name']);
    $sheet->setCellValue("F$rowNum", $row['classroom']);
    $rowNum++;
}


    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"schedule_$groupName.xlsx\"");
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
}

function exportQR($group_id) {
    $url = "http://yourdomain.com/schedule.php?group_id=$group_id";

    $result = Builder::create()
        ->writer(new PngWriter())
        ->data($url)
        ->size(300)
        ->margin(10)
        ->build();

    header('Content-Type: image/png');
    header("Content-Disposition: attachment; filename=\"qr_group_$group_id.png\"");
    echo $result->getString();
}
