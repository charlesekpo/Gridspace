<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PersonalityAnalyst;
use App\Models\Profile;
use App\Models\User;

class AssessmentController extends Controller
{
    public function __construct(private PersonalityAnalyst $analyst)
    {
    }

    /**
     * Show the questionnaire form
     */
    public function index()
    {
        return view('questionnaire');
    }

    /**
     * Process the questionnaire and analyze responses
     */
    public function analyze(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'q1' => 'required|string',
            'q2' => 'required|string',
            'q3' => 'required|string',
            'q4' => 'required|string',
            'q5' => 'required|string',
            'q6' => 'required|string',
            'q7' => 'required|string',
            'q8' => 'required|string',
            'q9' => 'required|string',
            'q10' => 'required|string',
        ]);

        // Create or find user
        $user = User::firstOrCreate([
            'email' => $validated['email']
        ], [
            'name' => $validated['name'],
            'password' => bcrypt(str()->random(20)), // Random password for demo
        ]);

        // Prepare questionnaire answers
        $questions = [
            'q1' => 'How do you prefer to spend your free time?',
            'q2' => 'Describe your approach to planning and organization.',
            'q3' => 'How do you handle social situations at work?',
            'q4' => 'How do you typically respond to disagreements with colleagues?',
            'q5' => 'What motivates you most in your work?',
            'q6' => 'How do you deal with unexpected changes or challenges?',
            'q7' => 'Describe your ideal work environment.',
            'q8' => 'How do you approach learning new skills?',
            'q9' => 'What role do you prefer in team projects?',
            'q10' => 'How do you handle stress and pressure?',
        ];

        $answers = [];
        foreach ($questions as $key => $question) {
            $answers[$question] = $validated[$key];
        }

        // Get AI analysis
        $analysis = $this->analyst->analyze($answers);

        // Create profile
        $profile = Profile::create([
            'user_id' => $user->id,
            'questionnaire_answers' => $answers,
            'personality_analysis' => $analysis['raw_analysis'],
            'big_five_scores' => $this->extractBigFiveScores($analysis['raw_analysis']),
            'analyzed_at' => $analysis['analyzed_at'],
        ]);

        return redirect()->route('assessment.results', ['profile' => $profile->id])
            ->with('success', 'Personality assessment completed successfully!');
    }

    /**
     * Show the assessment results
     */
    public function results(Profile $profile)
    {
        return view('results', ['profile' => $profile]);
    }

    /**
     * Extract Big Five scores from AI analysis (simplified version)
     */
    private function extractBigFiveScores(string $analysis): array
    {
        // In a production app, you might want to use more sophisticated parsing
        // or ask the AI to return structured JSON
        return [
            'openness' => $this->extractScore($analysis, 'openness'),
            'conscientiousness' => $this->extractScore($analysis, 'conscientiousness'),
            'extraversion' => $this->extractScore($analysis, 'extraversion'),
            'agreeableness' => $this->extractScore($analysis, 'agreeableness'),
            'neuroticism' => $this->extractScore($analysis, 'neuroticism'),
        ];
    }

    /**
     * Extract individual trait score from analysis
     */
    private function extractScore(string $analysis, string $trait): int
    {
        // Look for patterns like "Openness: 7" or "Openness: [7]"
        if (preg_match("/{$trait}.*?(\d+)/i", $analysis, $matches)) {
            $score = (int) $matches[1];
            // Ensure score is within 1-10 range
            return max(1, min(10, $score));
        }

        // Default score if not found
        return 5;
    }
}
