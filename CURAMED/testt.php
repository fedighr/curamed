<?php
require_once __DIR__ . '/vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf([
    'tempDir' => __DIR__ . '/pdf',
]);

$mpdf->WriteHTML('<h1>Hello, mPDF is working!</h1>');
$mpdf->Output();
?>
