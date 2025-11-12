<?php
ob_clean();
ob_start();

require_once __DIR__ . '/libs/tcpdf/tcpdf.php';
include __DIR__ . '/db.php';


$today = date('Y-m-d');

$sql = "SELECT s.*, 
        g.name AS group_name,
        sub.name AS subject_name,
        t.full_name AS teacher_name,
        c.name AS classroom_name,
        ln.lesson_number,
        ay.year_name
    FROM schedule s
    JOIN `groups` g ON s.group_id = g.id
    JOIN subject sub ON s.subject_id = sub.id
    JOIN teachers t ON s.teacher_id = t.id
    JOIN classroom c ON s.classroom_id = c.id
    JOIN lesson_number ln ON s.lesson_number_id = ln.id
    JOIN academic_years ay ON s.academic_year_id = ay.id
    WHERE DATE(s.updated_at) = '$today'
    ORDER BY s.day_of_week, ln.lesson_number";

$result = $conn->query($sql);

$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Система розкладу');
$pdf->SetTitle('Зміни в розкладі за ' . date('d.m.Y'));
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();
$pdf->SetFont('dejavusans', '', 10);

$html = '<h2>Зміни в розкладі за ' . date('d.m.Y') . '</h2>';

if ($result && $result->num_rows > 0) {
    $html .= '<table border="1" cellpadding="4">
        <thead>
            <tr>
                <th>№ Пари</th>
                <th>Група</th>
                <th>Предмет</th>
                <th>Викладач</th>
                <th>Кабінет</th>
            </tr>
        </thead><tbody>';

    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
            <td>' . htmlspecialchars($row['lesson_number']) . '</td>
            <td>' . htmlspecialchars($row['group_name']) . '</td>
            <td>' . htmlspecialchars($row['subject_name']) . '</td>
            <td>' . htmlspecialchars($row['teacher_name']) . '</td>
            <td>' . htmlspecialchars($row['classroom_name']) . '</td>
            <td>' . date('H:i:s', strtotime($row['updated_at'])) . '</td>
        </tr>';
    }

    $html .= '</tbody></table>';
} else {
    $html .= '<p>Змін за сьогодні не знайдено.</p>';
}

$pdf->writeHTML($html, true, false, true, false, '');

ob_end_clean();
$pdf->Output('zmini_' . date('Ymd') . '.pdf', 'D');
exit;
?>
