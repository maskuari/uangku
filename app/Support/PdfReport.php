<?php

namespace App\Support;

use Illuminate\Support\Str;

class PdfReport
{
    public function render(string $name, array $data): string
    {
        $rows = [];
        foreach ($data['transactions'] as $item) {
            $rows[] = [
                $item->occurred_on->format('d/m/Y'),
                Str::limit($item->title, 27),
                $item->type === 'income' ? 'Masuk' : 'Keluar',
                number_format($item->amount, 0, ',', '.'),
            ];
        }

        $pages = array_chunk($rows, 30);
        if ($pages === []) {
            $pages = [[]];
        }

        $objects = [];
        $reserve = function () use (&$objects): int {
            $objects[] = '';

            return count($objects);
        };
        $catalog = $reserve();
        $pagesRoot = $reserve();
        $font = $reserve();
        $boldFont = $reserve();
        $pageRefs = [];

        foreach ($pages as $index => $chunk) {
            $commands = [];
            $text = function (string $value, int $x, int $y, int $size = 10, bool $bold = false, string $color = '0.10 0.13 0.26') use (&$commands): void {
                $safe = iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $value) ?: '';
                $safe = str_replace(['\\', '(', ')', "\r", "\n"], ['\\\\', '\\(', '\\)', ' ', ' '], $safe);
                $fontName = $bold ? 'F2' : 'F1';
                $commands[] = "{$color} rg BT /{$fontName} {$size} Tf {$x} {$y} Td ({$safe}) Tj ET";
            };
            $fill = function (int $x, int $y, int $width, int $height, string $color) use (&$commands): void {
                $commands[] = "{$color} rg {$x} {$y} {$width} {$height} re f";
            };
            $stroke = function (int $x1, int $y1, int $x2, int $y2, string $color = '0.85 0.87 0.93') use (&$commands): void {
                $commands[] = "{$color} RG 0.7 w {$x1} {$y1} m {$x2} {$y2} l S";
            };

            $text('uangku.', 45, 795, 21, true, '0.35 0.25 0.92');
            $text('REKAP KEUANGAN', 402, 799, 10, true, '0.44 0.48 0.60');
            $stroke(45, 779, 550, 779);
            $text($data['date']->translatedFormat('F Y'), 45, 754, 13, true);
            $text($name, 45, 737, 9, false, '0.44 0.48 0.60');

            $fill(45, 680, 243, 44, '0.94 0.93 1.00');
            $fill(299, 680, 251, 44, '1.00 0.94 0.95');
            $text('TOTAL PEMASUKAN', 57, 706, 8, true, '0.35 0.25 0.92');
            $text('Rp '.number_format($data['income'], 0, ',', '.'), 57, 688, 13, true);
            $text('TOTAL PENGELUARAN', 311, 706, 8, true, '0.78 0.22 0.32');
            $text('Rp '.number_format($data['expense'], 0, ',', '.'), 311, 688, 13, true);

            $tableTop = 644;
            $rowHeight = 19;
            $fill(45, $tableTop, 505, 27, '0.35 0.25 0.92');
            $text('TANGGAL', 52, 653, 8, true, '1 1 1');
            $text('KETERANGAN', 135, 653, 8, true, '1 1 1');
            $text('JENIS', 364, 653, 8, true, '1 1 1');
            $text('NOMINAL (RP)', 440, 653, 8, true, '1 1 1');

            foreach ($chunk as $rowIndex => $row) {
                $bottom = $tableTop - ($rowIndex + 1) * $rowHeight;
                if ($rowIndex % 2 === 1) {
                    $fill(45, $bottom, 505, $rowHeight, '0.97 0.97 0.99');
                }
                $baseline = $bottom + 6;
                $text($row[0], 52, $baseline, 9);
                $text($row[1], 135, $baseline, 9);
                $text($row[2], 364, $baseline, 9, true, $row[2] === 'Masuk' ? '0.06 0.57 0.43' : '0.82 0.24 0.32');
                $text($row[3], 440, $baseline, 9, true);
            }

            $tableBottom = $tableTop - max(count($chunk), 1) * $rowHeight;
            if ($chunk === []) {
                $text('Belum ada transaksi pada bulan ini.', 135, $tableBottom + 6, 9, false, '0.44 0.48 0.60');
            }

            $stroke(45, $tableTop, 550, $tableTop);
            for ($rowIndex = 1; $rowIndex <= max(count($chunk), 1); $rowIndex++) {
                $y = $tableTop - $rowIndex * $rowHeight;
                $stroke(45, $y, 550, $y);
            }
            foreach ([45, 128, 356, 432, 550] as $x) {
                $stroke($x, $tableTop, $x, $tableBottom);
            }

            $stroke(45, 55, 550, 55);
            $text('Halaman '.($index + 1).' / '.count($pages).'  |  Dibuat '.now()->format('d/m/Y H:i'), 45, 39, 8, false, '0.44 0.48 0.60');
            $text('uangku.', 505, 39, 9, true, '0.35 0.25 0.92');

            $stream = implode("\n", $commands)."\n";
            $contentRef = $reserve();
            $objects[$contentRef - 1] = "<< /Length ".strlen($stream)." >>\nstream\n{$stream}endstream";
            $pageRef = $reserve();
            $objects[$pageRef - 1] = "<< /Type /Page /Parent {$pagesRoot} 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 {$font} 0 R /F2 {$boldFont} 0 R >> >> /Contents {$contentRef} 0 R >>";
            $pageRefs[] = "{$pageRef} 0 R";
        }

        $objects[$catalog - 1] = "<< /Type /Catalog /Pages {$pagesRoot} 0 R >>";
        $objects[$pagesRoot - 1] = '<< /Type /Pages /Kids ['.implode(' ', $pageRefs).'] /Count '.count($pageRefs).' >>';
        $objects[$font - 1] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        $objects[$boldFont - 1] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>';

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $id => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($id + 1)." 0 obj\n{$object}\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= 'xref'."\n0 ".(count($objects) + 1)."\n0000000000 65535 f \n";
        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= sprintf('%010d 00000 n ', $offset)."\n";
        }

        return $pdf.'trailer'."\n<< /Size ".(count($objects) + 1)." /Root {$catalog} 0 R >>\nstartxref\n{$xref}\n%%EOF";
    }
}
