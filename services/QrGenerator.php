<?php

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Encoding\Encoding;

class QrGenerator
{
    private $writer;

    public function __construct()
    {
        $this->writer = new PngWriter();
    }

    private function generate($dataString, $size = 300, $ecLevel = 'M')
    {
        if ($size < 100 || $size > 1000) {
            throw new Exception("El tamaño debe de ser entre 100 y 1000.", 400);
        }

        $ecMap = [
            'L' => ErrorCorrectionLevel::Low,
            'M' => ErrorCorrectionLevel::Medium,
            'Q' => ErrorCorrectionLevel::Quartile,
            'H' => ErrorCorrectionLevel::High,
        ];

        $errorCorrection = $ecMap[strtoupper($ecLevel)] ?? ErrorCorrectionLevel::Medium;

        $qrCode = new QrCode(
            data: $dataString,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: $errorCorrection,
            size: $size,
            margin: 10
        );

        $result = $this->writer->write($qrCode);

        return [
            'mime_type' => $result->getMimeType(),
            'base64' => base64_encode($result->getString())
        ];
    }

    public function generarTexto($text, $size = 300, $ecLevel = 'M')
    {
        if (empty(trim($text))) throw new Exception("El texto es requerido.", 400);
        return $this->generate($text, $size, $ecLevel);
    }

    public function generarURL($url, $size = 300, $ecLevel = 'M')
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) throw new Exception("URL no válida.", 400);
        return $this->generate($url, $size, $ecLevel);
    }

    public function generarWifi($ssid, $password, $encryption = 'WPA', $size = 300, $ecLevel = 'M')
    {
        if (empty(trim($ssid))) throw new Exception("El SSID es requerido.", 400);
        $encryption = strtoupper($encryption);
        if (!in_array($encryption, ['WPA', 'WEP', 'NOPASS'])) {
            throw new Exception("Encriptación inválida (WPA, WEP, NOPASS).", 400);
        }

        $wifiString = sprintf('WIFI:S:%s;T:%s;P:%s;;',
            addcslashes($ssid, ';,:'),
            $encryption === 'NOPASS' ? '' : $encryption,
            addcslashes($password, ';,:')
        );
        return $this->generate($wifiString, $size, $ecLevel);
    }

    public function generarGeo($lat, $lon, $size = 300, $ecLevel = 'M')
    {
        if (!is_numeric($lat) || $lat < -90 || $lat > 90) throw new Exception("Latitud inválida.", 400);
        if (!is_numeric($lon) || $lon < -180 || $lon > 180) throw new Exception("Longitud inválida.", 400);
        return $this->generate(sprintf('geo:%s,%s', $lat, $lon), $size, $ecLevel);
    }
}
?>