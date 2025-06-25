<?php

if (!function_exists('node')) {
    function node($data = [])
    {
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post('http://127.0.0.1:13714', [
                'json' => $data,
                'timeout' => 30
            ]);

            return $response->getBody()->getContents();
        } catch (\Exception $e) {
            \Log::error('SSR Error: ' . $e->getMessage());
            return json_encode([
                'html' => '',
                'head' => '',
                'state' => null
            ]);
        }
    }
}
