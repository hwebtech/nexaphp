<?php

namespace core;

use League\Csv\Reader;
use League\Csv\Writer;

class CSV
{
    /**
     * Parse a CSV file and return its records as an array.
     */
    public static function parse(string $path, bool $hasHeader = true): array
    {
        $csv = Reader::from($path, 'r');
        if ($hasHeader) {
            $csv->setHeaderOffset(0);
        }

        $records = [];
        foreach ($csv as $record) {
            $records[] = $record;
        }

        return $records;
    }

    /**
     * Create and download a CSV from an array of data.
     */
    public static function download(array $header, array $records, string $filename = 'export.csv')
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $writer = Writer::from('php://output', 'w');
        $writer->insertOne($header);
        $writer->insertAll($records);
        exit;
    }

    /**
     * Create and save a CSV file to the server.
     */
    public static function save(string $path, array $header, array $records)
    {
        $writer = Writer::from($path, 'w+');
        $writer->insertOne($header);
        $writer->insertAll($records);
        return true;
    }
}
