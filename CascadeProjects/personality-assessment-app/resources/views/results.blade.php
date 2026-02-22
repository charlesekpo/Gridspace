<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personality Assessment Results</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Your Personality Assessment Results</h1>
                <p class="text-gray-600">Based on the Big Five (OCEAN) personality model</p>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Big Five Scores Chart -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h2 class="text-xl font-semibold mb-4 text-gray-700">Big Five Personality Scores</h2>
                    <canvas id="personalityChart" width="400" height="300"></canvas>
                </div>

                <!-- Score Details -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h2 class="text-xl font-semibold mb-4 text-gray-700">Score Breakdown</h2>
                    <div class="space-y-3">
                        @if($profile->big_five_scores)
                            @foreach($profile->big_five_scores as $trait => $score)
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $trait) }}:</span>
                                    <div class="flex items-center">
                                        <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $score * 10 }}%"></div>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-600">{{ $score }}/10</span>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Personality Analysis -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Detailed Personality Analysis</h2>
                <div class="bg-blue-50 p-6 rounded-lg">
                    <div class="prose max-w-none">
                        {!! nl2br(e($profile->personality_analysis)) !!}
                    </div>
                </div>
            </div>

            <!-- Trait Descriptions -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Understanding Your Traits</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                        <h3 class="font-semibold text-purple-800 mb-2">Openness to Experience</h3>
                        <p class="text-sm text-gray-700">Curiosity, creativity, and preference for novelty and variety</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <h3 class="font-semibold text-green-800 mb-2">Conscientiousness</h3>
                        <p class="text-sm text-gray-700">Organization, discipline, and goal-directed behavior</p>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <h3 class="font-semibold text-yellow-800 mb-2">Extraversion</h3>
                        <p class="text-sm text-gray-700">Sociability, assertiveness, and positive emotions</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                        <h3 class="font-semibold text-red-800 mb-2">Agreeableness</h3>
                        <p class="text-sm text-gray-700">Compassion, cooperation, and trust in others</p>
                    </div>
                    <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                        <h3 class="font-semibold text-indigo-800 mb-2">Neuroticism</h3>
                        <p class="text-sm text-gray-700">Emotional stability and stress response (lower is better)</p>
                    </div>
                </div>
            </div>

            <!-- Questionnaire Answers -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Your Responses</h2>
                <div class="bg-gray-50 p-6 rounded-lg">
                    <div class="space-y-4">
                        @if($profile->questionnaire_answers)
                            @foreach($profile->questionnaire_answers as $question => $answer)
                                <div class="border-l-4 border-blue-500 pl-4">
                                    <p class="font-medium text-gray-700 mb-1">{{ $question }}</p>
                                    <p class="text-gray-600">{{ $answer }}</p>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('assessment.index') }}" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition duration-200 font-semibold text-center">
                    Take Another Assessment
                </a>
                <button onclick="window.print()" 
                        class="bg-gray-600 text-white px-6 py-3 rounded-md hover:bg-gray-700 transition duration-200 font-semibold">
                    Print Results
                </button>
            </div>

            <!-- Footer Info -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-center text-sm text-gray-500">
                    Assessment completed on: {{ $profile->analyzed_at->format('F j, Y \a\t g:i A') }}
                </p>
                <p class="text-center text-xs text-gray-400 mt-2">
                    This assessment is for informational purposes only and should not be used for clinical diagnosis.
                </p>
            </div>
        </div>
    </div>

    <script>
        // Chart.js configuration
        const ctx = document.getElementById('personalityChart').getContext('2d');
        
        @if($profile->big_five_scores)
            const data = {
                labels: ['Openness', 'Conscientiousness', 'Extraversion', 'Agreeableness', 'Emotional Stability'],
                datasets: [{
                    label: 'Personality Traits',
                    data: [
                        {{ $profile->big_five_scores['openness'] ?? 5 }},
                        {{ $profile->big_five_scores['conscientiousness'] ?? 5 }},
                        {{ $profile->big_five_scores['extraversion'] ?? 5 }},
                        {{ $profile->big_five_scores['agreeableness'] ?? 5 }},
                        {{ 10 - ($profile->big_five_scores['neuroticism'] ?? 5) }} // Reverse neuroticism for emotional stability
                    ],
                    backgroundColor: [
                        'rgba(147, 51, 234, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(250, 204, 21, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(99, 102, 241, 0.8)'
                    ],
                    borderColor: [
                        'rgba(147, 51, 234, 1)',
                        'rgba(34, 197, 94, 1)',
                        'rgba(250, 204, 21, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(99, 102, 241, 1)'
                    ],
                    borderWidth: 2
                }]
            };

            new Chart(ctx, {
                type: 'radar',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            beginAtZero: true,
                            max: 10,
                            ticks: {
                                stepSize: 2
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        @endif
    </script>
</body>
</html>
