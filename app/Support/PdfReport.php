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
        if ($pages === []) $pages = [[]];
        $objects = [];
        $reserve = function () use (&$objects): int { $objects[] = ''; return count($objects); };
        $catalog = $reserve();
        $pagesRoot = $reserve();
        $font = $reserve();
        $pageRefs = [];
        foreach ($pages as $index => $chunk) {
            $commands = [];
            $line = function (string $text, int $x, int $y, int $size = 10) use (&$commands): void {
                $safe = iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $text) ?: '';
                $safe = str_replace(['\\', '(', ')', "\r", "\n"], ['\\\\', '\\(', '\\)', ' ', ' '], $safe);
                $commands[] = "BT /F1 {$size} Tf {$x} {$y} Td ({$safe}) Tj ET";
            };
            $line('UANGKU  /  REKAP KEUANGAN', 45, 795, 18);
            $line($data['date']->translatedFormat('F Y').'  -  '.$name, 45, 771, 10);
            $line('Pemasukan: Rp '.number_format($data['income'], 0, ',', '.'), 45, 739, 10);
            $line('Pengeluaran: Rp '.number_format($data['expense'], 0, ',', '.'), 290, 739, 10);
            $line('Tanggal', 45, 705, 10);
            $line('Keterangan', 125, 705, 10);
            $line('Jenis', 360, 705, 10);
            $line('Nominal (Rp)', 445, 705, 10);
            $y = 684;
            foreach ($chunk as $row) {
                $line($row[0], 45, $y, 9);
                $line($row[1], 125, $y, 9);
                $line($row[2], 360, $y, 9);
                $line($row[3], 445, $y, 9);
                $y -= 20;
            }
            if ($chunk === []) $line('Belum ada transaksi pada bulan ini.', 45, $y, 10);
            $line('Halaman '.($index + 1).' / '.count($pages).'  |  Dibuat '.now()->format('d/m/Y H:i'), 45, 38, 9);
            $stream = implode("\n", $commands)."\n";
            $contentRef = $reserve();
            $objects[$contentRef - 1] = "<< /Length ".strlen($stream)." >>\nstream\n{$stream}endstream";
            $pageRef = $reserve();
            $objects[$pageRef - 1] = "<< /Type /Page /Parent {$pagesRoot} 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 {$font} 0 R >> >> /Contents {$contentRef} 0 R >>";
            $pageRefs[] = "{$pageRef} 0 R";
        }
        $objects[$catalog - 1] = "<< /Type /Catalog /Pages {$pagesRoot} 0 R >>";
        $objects[$pagesRoot - 1] = '<< /Type /Pages /Kids ['.implode(' ', $pageRefs).'] /Count '.count($pageRefs).' >>';
        $objects[$font - 1] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $id => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($id + 1)." 0 obj\n{$object}\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= 'xref'."\n0 ".(count($objects) + 1)."\n0000000000 65535 f \n";
        foreach (array_slice($offsets, 1) as $offset) $pdf .= sprintf('%010d 00000 n ', $offset)."\n";
        return $pdf.'trailer'."\n<< /Size ".(count($objects) + 1)." /Root {$catalog} 0 R >>\nstartxref\n{$xref}\n%%EOF";
    }
}
