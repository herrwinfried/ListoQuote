<?php
require '../../auth.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf as PdfWriter;

$offer_id = $_GET['id'] ?? ($_GET['offer_id'] ?? null);
$type = isset($_GET['type']) ? (int)$_GET['type'] : 0;
if ($type === 1) {
    $discount = $_GET['discount'] ?? 15;
    $vat = $_GET['vat'] ?? 20;
}
if (!$offer_id) {
    die("Teklif ID yok.");
}

$offer = $conn->prepare("SELECT * FROM offers_list WHERE id = ?");
$offer->execute([$offer_id]);
$offer = $offer->fetch(PDO::FETCH_ASSOC);
if (!$offer) {
    die("Teklif bulunamadı.");
}

$offer_items = $conn->prepare("SELECT * FROM offers_list_child WHERE offer_id = ?");
$offer_items->execute([$offer_id]);
$offer_items = $offer_items->fetchAll(PDO::FETCH_ASSOC);



$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', $offer['list_name']);
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);

if ($type === 0) {
    $sheet->mergeCells('A1:K1');
    $sheet->getPageMargins()
        ->setTop(0.516)
        ->setLeft(0.252)
        ->setBottom(0.752)
        ->setRight(0.701);

    $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

    $headers = ['Sıra NO', 'Mamul Kodu', 'Mamul İsmi', 'Liste Fiyat', 'Miktar', 'Birim', 'Tutar', 'İskonto', 'KDV', 'KDV Tutar', 'Toplam Tutar'];
    $sheet->fromArray($headers, null, 'A3');
    $sheet->getStyle('A3:K3')->getFont()->setBold(true);


    $row = 4;
    $index = 1;
    foreach ($offer_items as $item) {
        $price = $item['product_price'];
        $item['product_discount'] = $item['product_discount'] ?? 20;
        $item['product_quantity'] = $item['product_quantity'] ?? 1;
        if ($item['product_percentage'] > 0) {
            $price += $price * ($item['product_percentage'] / 100);
        }

        $sheet->setCellValue("A{$row}", $index);
        $sheet->setCellValue("B{$row}", $item['product_code']);
        $sheet->setCellValue("C{$row}", $item['product_name']);
        $sheet->setCellValue("D{$row}", round($price, 2));
        $sheet->setCellValue("E{$row}", $item['product_quantity']);
        $sheet->setCellValue("F{$row}", $item['product_type'] === 'mt' ? 'Mt' : 'Ad');
        $sheet->setCellValue("G{$row}", "=D{$row}*E{$row}");
        $sheet->setCellValue("H{$row}", $item['product_discount']);
        $sheet->setCellValue("I{$row}", $item['product_vat']);
        $sheet->setCellValue("J{$row}", "=(G{$row}-(G{$row}*H{$row}/100))*{$item['product_vat']}/100");
        $sheet->setCellValue("K{$row}", "=G{$row}-(G{$row}*H{$row}/100)");

        $index++;
        $row++;
    }

    $brut_toplam_row = $row + 1;
    $kdv_toplam_row = $row + 2;
    $ara_toplam_row = $row + 3;
    $genel_toplam_row = $row + 4;

    $sheet->setCellValue("H{$brut_toplam_row}", "BRÜT TOPLAM");
    $sheet->setCellValue("K{$brut_toplam_row}", "=SUM(G4:G" . ($row - 1) . ")");

    $sheet->setCellValue("H{$ara_toplam_row}", "ARA TOPLAM");

    $sheet->setCellValue("K{$ara_toplam_row}", "=SUM(K4:K" . ($row - 1) . ")");

    $sheet->setCellValue("H{$kdv_toplam_row}", "KDV TOPLAM");
    $sheet->setCellValue("K{$kdv_toplam_row}", "=SUM(J4:J" . ($row - 1) . ")");

    $sheet->setCellValue("H{$genel_toplam_row}", "GENEL TOPLAM");
    $sheet->setCellValue("K{$genel_toplam_row}", "=K{$ara_toplam_row}+K{$kdv_toplam_row}");

    $sheet->getStyle("A3:K" . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);

        $sheet->getStyle("A3:K" . ($row - 1))->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER);

    $sheet->getColumnDimension('A')->setWidth(5);
    $sheet->getColumnDimension('B')->setWidth(40);
    $sheet->getColumnDimension('C')->setWidth(40);
    $sheet->getColumnDimension('D')->setWidth(10);
    $sheet->getColumnDimension('E')->setWidth(7);
    $sheet->getColumnDimension('F')->setWidth(6);
    $sheet->getColumnDimension('G')->setWidth(10);
    $sheet->getColumnDimension('H')->setWidth(8);
    $sheet->getColumnDimension('I')->setWidth(8);
    $sheet->getColumnDimension('J')->setWidth(10);
    $sheet->getColumnDimension('K')->setWidth(15);
    $sheet->getStyle("A3:K" . ($row - 1))
        ->getAlignment()
        ->setWrapText(true);
    $tlFormat = '#,##0.00₺';
    $sheet->getStyle("D4:D" . ($row - 1))->getNumberFormat()->setFormatCode($tlFormat);
    $sheet->getStyle("G4:G" . ($row - 1))->getNumberFormat()->setFormatCode($tlFormat);
    $sheet->getStyle("J4:J" . ($row - 1))->getNumberFormat()->setFormatCode($tlFormat);
    $sheet->getStyle("K4:K" . ($row - 1))->getNumberFormat()->setFormatCode($tlFormat);
    $sheet->getStyle("K{$brut_toplam_row}")->getNumberFormat()->setFormatCode($tlFormat);
    $sheet->getStyle("K{$kdv_toplam_row}")->getNumberFormat()->setFormatCode($tlFormat);
    $sheet->getStyle("K{$ara_toplam_row}")->getNumberFormat()->setFormatCode($tlFormat);
    $sheet->getStyle("K{$genel_toplam_row}")->getNumberFormat()->setFormatCode($tlFormat);

}
elseif ($type === 1) {
    $sheet->mergeCells('A1:I1');
    $sheet->getPageMargins()
        ->setTop(0.516)
        ->setLeft(0.252)
        ->setBottom(0.752)
        ->setRight(0.701);

    $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);

    $headers = ['Sıra NO', 'Mamul Kodu', 'Mamul İsmi', 'Liste Fiyat', 'Miktar', 'Birim', 'Tutar', 'İskonto', 'Toplam Tutar'];
    $sheet->fromArray($headers, null, 'A3');
    $sheet->getStyle('A3:I3')->getFont()->setBold(true);


    $row = 4;
    $index = 1;
    foreach ($offer_items as $item) {
        $price = $item['product_price'];
        $item['product_discount'] = $discount;
        $item['product_vat'] = $vat;
        $item['product_quantity'] = $item['product_quantity'] ?? 1;
        if ($item['product_percentage'] > 0) {
            $price += $price * ($item['product_percentage'] / 100);
        }

        $sheet->setCellValue("A{$row}", $index);
        $sheet->setCellValue("B{$row}", $item['product_code']);
        $sheet->setCellValue("C{$row}", $item['product_name']);
        $sheet->setCellValue("D{$row}", round($price, 2));
        $sheet->setCellValue("E{$row}", $item['product_quantity']);
        $sheet->setCellValue("F{$row}", $item['product_type'] === 'mt' ? 'Mt' : 'Ad');
        $sheet->setCellValue("G{$row}", "=D{$row}*E{$row}");
        $sheet->setCellValue("H{$row}", $item['product_discount']);
        $sheet->setCellValue("I{$row}", "=G{$row}-(G{$row}*H{$row}/100)");

        $index++;
        $row++;
    }

    $brut_toplam_row = $row + 1;
    $iskonto_toplam_row = $row + 2;
    $ara_toplam_row = $row + 3;
    $kdv_toplam_row = $row + 4;
    $genel_toplam_row = $row + 5;

    $sheet->setCellValue("F{$brut_toplam_row}", "BRÜT TOPLAM");
    $sheet->setCellValue("I{$brut_toplam_row}", "=SUM(G4:G" . ($row - 1) . ")");

    $sheet->setCellValue("F{$iskonto_toplam_row}", "İSKONTO");
    $sheet->setCellValue("I{$iskonto_toplam_row}", $discount);

    $sheet->setCellValue("F{$ara_toplam_row}", "ARA TOPLAM");
    $sheet->setCellValue("I{$ara_toplam_row}", "=SUM(I4:I" . ($row - 1) . ")");

    $sheet->setCellValue("F{$kdv_toplam_row}", "KDV");
    $sheet->setCellValue("G{$kdv_toplam_row}", $vat . "%");
    $sheet->setCellValue("I{$kdv_toplam_row}", "=I{$ara_toplam_row}*G{$kdv_toplam_row}");

    $sheet->setCellValue("F{$genel_toplam_row}", "GENEL TOPLAM");
    $sheet->setCellValue("I{$genel_toplam_row}", "=SUM(I{$ara_toplam_row}+I{$kdv_toplam_row})");

    $sheet->getStyle("A3:I" . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);

    $sheet->getStyle("A3:I" . ($row - 1))->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER);

    $sheet->getColumnDimension('A')->setWidth(5);
    $sheet->getColumnDimension('B')->setWidth(40);
    $sheet->getColumnDimension('C')->setWidth(40);
    $sheet->getColumnDimension('D')->setWidth(10);
    $sheet->getColumnDimension('E')->setWidth(7);
    $sheet->getColumnDimension('F')->setWidth(6);
    $sheet->getColumnDimension('G')->setWidth(10);
    $sheet->getColumnDimension('H')->setWidth(8);
    $sheet->getColumnDimension('I')->setWidth(15);
    $sheet->getColumnDimension('B')->setVisible(false);
    $sheet->getStyle("A3:I" . ($row - 1))
        ->getAlignment()
        ->setWrapText(true);
    $tlFormat = '#,##0.00₺';
    $sheet->getStyle("D4:D" . ($row - 1))->getNumberFormat()->setFormatCode($tlFormat);
    $sheet->getStyle("G4:G" . ($row - 1))->getNumberFormat()->setFormatCode($tlFormat);
    $sheet->getStyle("I4:J" . ($row - 1))->getNumberFormat()->setFormatCode($tlFormat);
    $sheet->getStyle("K{$brut_toplam_row}")->getNumberFormat()->setFormatCode($tlFormat);
    $sheet->getStyle("K{$kdv_toplam_row}")->getNumberFormat()->setFormatCode($tlFormat);
    $sheet->getStyle("K{$ara_toplam_row}")->getNumberFormat()->setFormatCode($tlFormat);
    $sheet->getStyle("K{$genel_toplam_row}")->getNumberFormat()->setFormatCode($tlFormat);

}

$ext = $_GET['ext'] ?? 'xlsx';

switch ($ext) {
    case 'ods':
        header('Content-Type: application/vnd.oasis.opendocument.spreadsheet');
        header('Content-Disposition: attachment;filename="teklif_' . $offer_id . '.ods"');
        $writer = new Ods($spreadsheet);
        break;

    case 'pdf':
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment;filename="teklif_' . $offer_id . '.pdf"');

        \PhpOffice\PhpSpreadsheet\IOFactory::registerWriter('Pdf', PdfWriter::class);
        $writer = new PdfWriter($spreadsheet);
        break;

    case 'xlsx':
    default:
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="teklif_' . $offer_id . '.xlsx"');
        $writer = new Xlsx($spreadsheet);
        break;
}

header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;