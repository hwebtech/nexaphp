<?php

namespace core;

use Dompdf\Dompdf;
use Dompdf\Options;

class PDF
{
    /**
     * Render an HTML string into a PDF and stream it to the browser.
     */
    public static function stream(string $html, string $filename = 'document.pdf')
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'serif');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $dompdf->stream($filename, ["Attachment" => false]);
    }

    /**
     * Save HTML as a PDF file on the server.
     */
    public static function save(string $html, string $path)
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        return file_put_contents($path, $dompdf->output());
    }
}
