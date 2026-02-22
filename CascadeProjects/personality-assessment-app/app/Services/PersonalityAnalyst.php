<?php

namespace App\Services;

use OpenAI;
use OpenAI\Client;
use Carbon\Carbon;

class PersonalityAnalyst
{
    private Client $client;

    public function __construct()
    {
        $apiKey = config('services.openai.api_key');
        $organization = config('services.openai.organization_id');

        if (empty($apiKey)) {
            throw new \Exception('OpenAI API key is not configured. Please set OPENAI_API_KEY in your .env file.');
        }

        $this->client = OpenAI::client($apiKey, $organization);
    }

    /**
     * Get the AI instructions for personality analysis
     */
    public function instructions(): string
    {
        return "You are an expert industrial psychologist. Analyze the provided questionnaire
                responses and describe the person's personality using the BIG FIVE (OCEAN)
                framework. Be objective and highlight both strengths and potential blind spots.

                The Big Five traits are:
                - Openness to Experience: Curiosity, creativity, preference for variety and novelty
                - Conscientiousness: Organization, discipline, goal-directed behavior
                - Extraversion: Sociability, assertiveness, positive emotions
                - Agreeableness: Compassion, cooperation, trust in others
                - Neuroticism: Emotional stability, anxiety, moodiness (reverse scored)

                Provide:
                1. Overall personality summary
                2. Big Five trait scores (1-10 scale)
                3. Key strengths
                4. Potential areas for development
                5. Work style preferences";
    }

    /**
     * Analyze questionnaire responses using AI
     */
    public function analyze(array $answers): array
    {
        try {
            $prompt = $this->buildPrompt($answers);

            $response = $this->client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->instructions()
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 1000,
            ]);

            $analysis = $response->choices[0]->message->content;

            return $this->parseAnalysis($analysis);
        } catch (\Exception $e) {
            // Return mock data if API fails
            return [
                'raw_analysis' => "AI analysis temporarily unavailable. Based on your responses, you appear to be thoughtful and self-aware. Please add your OpenAI API key to get detailed analysis.",
                'analyzed_at' => Carbon::now(),
            ];
        }
    }

    /**
     * Build the prompt for AI analysis
     */
    private function buildPrompt(array $answers): string
    {
        $prompt = "Please analyze the following questionnaire responses:\n\n";

        foreach ($answers as $question => $answer) {
            $prompt .= "Q: {$question}\nA: {$answer}\n\n";
        }

        $prompt .= "\nBased on these responses, provide a comprehensive personality analysis.
Please include the following in your response:
Openness: [1-10 score]
Conscientiousness: [1-10 score]
Extraversion: [1-10 score]
Agreeableness: [1-10 score]
Neuroticism: [1-10 score]

Then provide your detailed analysis.";

        return $prompt;
    }

    /**
     * Parse the AI analysis into structured data
     */
    private function parseAnalysis(string $analysis): array
    {
        // For now, return the raw analysis. In a production app, you might want
        // to parse this into structured data
        return [
            'raw_analysis' => $analysis,
            'analyzed_at' => Carbon::now(),
        ];
    }

    /**
     * Get a quick personality assessment based on responses
     */
    public function quickAssessment(array $answers): string
    {
        $prompt = "Based on these questionnaire responses, provide a brief 2-3 sentence personality summary:\n\n";

        foreach ($answers as $question => $answer) {
            $prompt .= "Q: {$question}\nA: {$answer}\n\n";
        }

        $response = $this->client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a personality analyst. Provide brief, insightful personality summaries.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.7,
            'max_tokens' => 200,
        ]);

        return $response->choices[0]->message->content;
    }
}
