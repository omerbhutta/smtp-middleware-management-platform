<?php
class SuppressionSyncService
{
    private $db;
    private $settings;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->settings = new SystemSetting();
    }

    public function getConfig()
    {
        return [
            'endpoint' => $this->settings->get('suppression_api_endpoint') ?: '',
            'api_key'  => $this->settings->get('suppression_api_key') ?: '',
            'method'   => $this->settings->get('suppression_api_method') ?: 'GET',
        ];
    }

    public function saveConfig($endpoint, $apiKey, $method)
    {
        $this->settings->set('suppression_api_endpoint', trim($endpoint));
        $this->settings->set('suppression_api_key', trim($apiKey));
        $this->settings->set('suppression_api_method', strtoupper($method) === 'POST' ? 'POST' : 'GET');
    }

    public function sync()
    {
        $config = $this->getConfig();
        if (empty($config['endpoint']) || empty($config['api_key'])) {
            return ['status' => false, 'message' => 'Suppression API not configured'];
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $config['endpoint'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => [
                'X-Api-Key: ' . $config['api_key'],
                'Accept: application/json',
            ],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        if ($config['method'] === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['status' => false, 'message' => 'cURL error: ' . $error];
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            return ['status' => false, 'message' => "HTTP {$httpCode}: " . substr($response, 0, 200)];
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['status' => false, 'message' => 'Invalid JSON response'];
        }

        $emails = $this->extractEmails($data);
        if (empty($emails)) {
            return ['status' => false, 'message' => 'No emails found in response'];
        }

        $imported = 0;
        $suppression = new SuppressionCache();
        foreach ($emails as $email) {
            $email = trim(strtolower($email));
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $suppression->add($email, 'Synced from EBM API', 'api');
                $imported++;
            }
        }

        return [
            'status'   => true,
            'message'  => "Synced {$imported} suppressed emails",
            'imported' => $imported,
        ];
    }

    private function extractEmails($data)
    {
        if (is_string($data)) {
            return [$data];
        }

        if (is_array($data)) {
            if (isset($data['email'])) return [$data['email']];
            if (isset($data['emails'])) return (array)$data['emails'];
            if (isset($data['data']) && is_array($data['data'])) {
                return $this->extractFromArray($data['data']);
            }
            if (isset($data['records']) && is_array($data['records'])) {
                return $this->extractFromArray($data['records']);
            }
            if (isset($data['results']) && is_array($data['results'])) {
                return $this->extractFromArray($data['results']);
            }
            $flat = $this->extractFromArray($data);
            if (!empty($flat)) return $flat;
        }

        return [];
    }

    private function extractFromArray($arr)
    {
        $emails = [];
        foreach ($arr as $item) {
            if (is_string($item)) {
                $emails[] = $item;
            } elseif (is_array($item)) {
                foreach (['email', 'Email', 'EMAIL', 'address', 'Address', 'recipient', 'Recipient'] as $key) {
                    if (isset($item[$key]) && filter_var($item[$key], FILTER_VALIDATE_EMAIL)) {
                        $emails[] = $item[$key];
                        break;
                    }
                }
            }
        }
        return $emails;
    }
}
