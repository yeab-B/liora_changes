<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Thin wrapper around Addis AI (api.addisassistant.com) — Amharic/Afan Oromo
 * translation + text-to-speech. Mirrors OpenAiClient: any missing key, HTTP
 * failure, timeout, or malformed response resolves to null so callers
 * (MotivationService, ChatService) always degrade gracefully to
 * "no audio" instead of breaking the text response.
 */
class AddisAiClient
{
    private const TIMEOUT_SECONDS = 12;

    /**
     * Translate English text to Amharic (or any supported language pair)
     * via POST /api/v1/translate.
     */
    public function translate(string $text, string $from = 'en', string $to = 'am'): ?string
    {
        $apiKey = config('services.addis_ai.key');

        if (empty($apiKey) || trim($text) === '') {
            return null;
        }

        try {
            $response = Http::withHeaders(['x-api-key' => $apiKey])
                ->timeout(self::TIMEOUT_SECONDS)
                ->post(config('services.addis_ai.base_url').'/api/v1/translate', [
                    'text' => $text,
                    'source_language' => $from,
                    'target_language' => $to,
                ]);

            if (! $response->successful()) {
                return null;
            }

            $translation = $response->json('data.translation');

            return is_string($translation) && trim($translation) !== '' ? trim($translation) : null;
        } catch (Throwable $e) {
            Log::warning('Addis AI translate failed; skipping voice generation.', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Generate a speech clip via POST /api/v1/voice/generations and return
     * its signed, short-lived playback URL (or null on any failure).
     */
    public function textToSpeech(string $text, ?string $voiceId = null, ?string $language = null): ?string
    {
        $apiKey = config('services.addis_ai.key');

        if (empty($apiKey) || trim($text) === '') {
            return null;
        }

        try {
            $response = Http::withHeaders(['x-api-key' => $apiKey])
                ->timeout(self::TIMEOUT_SECONDS)
                ->post(config('services.addis_ai.base_url').'/api/v1/voice/generations', [
                    'text' => $text,
                    'voice_id' => $voiceId ?: config('services.addis_ai.voice_id', 'am-hamen'),
                    'language' => $language ?: config('services.addis_ai.language', 'am'),
                    'output_format' => 'mp3_44100',
                    'client_request_id' => (string) Str::uuid(),
                ]);

            if (! $response->successful()) {
                return null;
            }

            $audioUrl = $response->json('data.audio_url');

            return is_string($audioUrl) && trim($audioUrl) !== '' ? $audioUrl : null;
        } catch (Throwable $e) {
            Log::warning('Addis AI text-to-speech failed; returning text-only response.', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Convenience: translate `$text` to Amharic then synthesize it, in one
     * call. Returns null (never throws) if Addis AI is unconfigured or
     * either step fails, so text-only responses are unaffected.
     */
    public function speakInAmharic(string $text): ?string
    {
        $amharicText = $this->translate($text, 'en', 'am');

        if ($amharicText === null) {
            return null;
        }

        return $this->textToSpeech($amharicText);
    }
}
