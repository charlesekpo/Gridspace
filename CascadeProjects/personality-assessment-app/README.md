# Personality Assessment Application

A Laravel application that uses AI to assess personality traits based on questionnaire responses using the Big Five (OCEAN) model.

## Features

- **AI-Powered Analysis**: Uses OpenAI's API to analyze questionnaire responses
- **Big Five Personality Model**: Assesses Openness, Conscientiousness, Extraversion, Agreeableness, and Neuroticism
- **Interactive Questionnaire**: 10 carefully crafted questions to determine personality traits
- **Visual Results**: Radar chart visualization of personality scores
- **Detailed Analysis**: Comprehensive personality report with strengths and development areas
- **Responsive Design**: Mobile-friendly interface using Tailwind CSS

## The Big Five Model

The application assesses personality based on the OCEAN model:

1. **Openness to Experience**: Curiosity, creativity, preference for variety and novelty
2. **Conscientiousness**: Organization, discipline, goal-directed behavior
3. **Extraversion**: Sociability, assertiveness, positive emotions
4. **Agreeableness**: Compassion, cooperation, trust in others
5. **Neuroticism**: Emotional stability, anxiety, moodiness (reverse scored)

## Installation

### Prerequisites

- PHP 8.2+
- Composer
- SQLite (or other supported database)
- OpenAI API key

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd personality-assessment-app
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure OpenAI API**
   
   Edit your `.env` file and add your OpenAI API key:
   ```env
   OPENAI_API_KEY=your_actual_openai_api_key_here
   OPENAI_ORGANIZATION_ID=your_organization_id_if_applicable
   ```

5. **Database setup**
   ```bash
   php artisan migrate
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```

7. **Access the application**
   
   Open your browser and navigate to `http://localhost:8000`

## Usage

1. **Take the Assessment**: Fill out the 10-question personality questionnaire
2. **Get Results**: Receive an AI-generated personality analysis
3. **View Scores**: See your Big Five trait scores on a radar chart
4. **Read Analysis**: Review detailed personality insights and recommendations

## Application Structure

```
app/
├── Http/Controllers/
│   └── AssessmentController.php    # Main assessment logic
├── Models/
│   ├── Profile.php                 # Profile model for storing results
│   └── User.php                    # User model
└── Services/
    └── PersonalityAnalyst.php      # AI service for personality analysis

resources/views/
├── questionnaire.blade.php         # Assessment form
└── results.blade.php              # Results display

database/migrations/
└── create_profiles_table.php      # Database schema
```

## API Integration

The application uses the OpenAI PHP client to communicate with OpenAI's API. The `PersonalityAnalyst` service:

- Sends questionnaire responses to GPT-3.5-turbo
- Receives structured personality analysis
- Extracts Big Five scores from the response
- Provides insights on strengths and development areas

## Real-World Applications

This type of personality assessment is used in:

- **Pre-Interview Screening**: High-volume recruitment (Unilever, Hilton)
- **Cultural Fit Assessments**: Team compatibility analysis
- **Team Building**: Understanding communication styles
- **Professional Development**: Identifying growth areas
- **Mental Health**: Early detection of burnout indicators

## Security Considerations

- API keys are stored in environment variables
- User data is encrypted in the database
- Input validation on all form submissions
- CSRF protection on all forms

## Customization

### Adding New Questions

1. Update the `questions` array in `AssessmentController::analyze()`
2. Add validation rules for new questions
3. Update the questionnaire form in `questionnaire.blade.php`

### Modifying AI Prompts

Edit the `instructions()` method in `PersonalityAnalyst.php` to customize the AI's analysis approach.

### Changing the Visual Design

Modify the Blade templates in `resources/views/` to customize the UI.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is open-source and available under the [MIT License](LICENSE).

## Disclaimer

This personality assessment is for informational purposes only and should not be used for clinical diagnosis or as the sole basis for employment decisions. Always consult with qualified professionals for personality-related evaluations.

## Support

For issues and questions:
- Create an issue in the repository
- Review the documentation
- Check existing issues for solutions
