<?php

require_once '../services/QrGenerator.php';

class QrResource
{
    private $generator;

    public function __construct()
    {
        $this->generator = new QrGenerator();
    }

    private function getPayload()
    {
        return json_decode(file_get_contents("php://input"));
    }

    private function sendResponse($statusCode, $data)
    {
        header("Content-Type: application/json");
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    public function generarTexto()
    {
        $data = $this->getPayload();
        if (!empty($data->content->text)) {
            try {
                $size = $data->size ?? 300;
                $ec = $data->ec_level ?? 'M';
                $result = $this->generator->generarTexto($data->content->text, $size, $ec);
                $this->sendResponse(200, ["status" => "success", "data" => $result]);
            } catch (Throwable $e) {
                $this->sendResponse(500, ["message" => "Error interno", "error" => $e->getMessage()]);
            }
        } else {
            $this->sendResponse(400, ["message" => "Se requiere content.text"]);
        }
    }

    public function generarURL()
    {
        $data = $this->getPayload();
        if (!empty($data->content->url)) {
            try {
                $size = $data->size ?? 300;
                $ec = $data->ec_level ?? 'M';
                $result = $this->generator->generarURL($data->content->url, $size, $ec);
                $this->sendResponse(200, ["status" => "success", "data" => $result]);
            } catch (Throwable $e) {
                $this->sendResponse(500, ["message" => "Error interno", "error" => $e->getMessage()]);
            }
        } else {
            $this->sendResponse(400, ["message" => "Se requiere content.url"]);
        }
    }

    public function generarWifi()
    {
        $data = $this->getPayload();
        if (!empty($data->content->ssid)) {
            try {
                $size = $data->size ?? 300;
                $ec = $data->ec_level ?? 'M';
                $password = $data->content->password ?? '';
                $encryption = $data->content->encryption ?? 'WPA';

                $result = $this->generator->generarWifi($data->content->ssid, $password, $encryption, $size, $ec);
                $this->sendResponse(200, ["status" => "success", "data" => $result]);
            } catch (Throwable $e) {
                $this->sendResponse(500, ["message" => "Error interno", "error" => $e->getMessage()]);
            }
        } else {
            $this->sendResponse(400, ["message" => "Se requiere content.ssid"]);
        }
    }

    public function generarGeo()
    {
        $data = $this->getPayload();
        if (isset($data->content->latitude) && isset($data->content->longitude)) {
            try {
                $size = $data->size ?? 300;
                $ec = $data->ec_level ?? 'M';

                $result = $this->generator->generarGeo($data->content->latitude, $data->content->longitude, $size, $ec);
                $this->sendResponse(200, ["status" => "success", "data" => $result]);
            } catch (Throwable $e) {
                $this->sendResponse(500, ["message" => "Error interno", "error" => $e->getMessage()]);
            }
        } else {
            $this->sendResponse(400, ["message" => "Se requiere content.latitude y content.longitude"]);
        }
    }
}
?>