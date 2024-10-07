<?php

namespace App\Traits;

use Gemini;

trait GeminiClient
{
    // Initialize the Gemini Client
    protected $client;

    // Initialize the Gemini client if it's null
    public function initGeminiClient()
    {
        if (is_null($this->client)) {
            $this->client = Gemini::client(env('GEMINI_KEY'));
        }
    }

    // Make the API request to Gemini with the given prompt
    public function requestFromGemini($prompt)
    {
        $this->initGeminiClient();

        try {
            $response = $this->client->geminiPro()->generateContent($prompt);

            // Check if 'candidates' array exists and has content
            if (isset($response->candidates[0]) && $response->candidates[0] !== null) {
                $responseText = $response->candidates[0]->content->parts[0]->text ?? null;

                // dd($responseText);

                if ($responseText !== null) {
                    // Convert single quotes to double quotes to ensure valid JSON
                    $validJsonString = str_replace("'", '"', $responseText);
                    $jsonResponse = json_decode($validJsonString, true);

                    // If the response is valid, return it, otherwise return a message
                    return is_array($jsonResponse) ? $jsonResponse : ['Something is wrong. Please try again.'];
                }
            }

            // In case of empty or invalid response, return a specific message
            return ['No valid response. Please try again.'];
        } catch (\Exception $e) {
            // Log the exception and return a user-friendly message
            \Log::error('Gemini API error: ' . $e->getMessage());
            return ['There was an error processing your request. Please try again later.'];
        }
    }
}
