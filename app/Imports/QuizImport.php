<?php

namespace App\Imports;

use App\Models\Question;
use App\Models\QuestionAnswer;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class QuizImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $competitionId;

    public function __construct($competitionId)
    {
        $this->competitionId = $competitionId;
    }

    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {
            $currentQuiz = null;
            $currentQuizName = null;

            foreach ($rows as $row) {
                // Get quiz name from row
                $quizName = trim($row['quiz_name']);
                $quizDate = isset($row['date']) ? $row['date'] : null;
                $helpUrl = isset($row['help']) ? trim($row['help']) : null;

                // If quiz name changed or first row, handle quiz creation/retrieval
                if ($quizName !== $currentQuizName && ! empty($quizName)) {
                    // Check if quiz already exists for this competition
                    $currentQuiz = Quiz::where('name', $quizName)
                        ->where('competition_id', $this->competitionId)
                        ->first();

                    // If quiz doesn't exist, create it
                    if (! $currentQuiz) {
                        // Parse the date
                        $parsedDate = now();
                        if ($quizDate) {
                            try {
                                // Check if it's an Excel serial date number
                                if (is_numeric($quizDate)) {
                                    $parsedDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($quizDate);
                                } else {
                                    $parsedDate = \Carbon\Carbon::parse($quizDate);
                                }
                            } catch (\Exception $e) {
                                // If parsing fails, use current date
                                $parsedDate = now();
                            }
                        }

                        $currentQuiz = Quiz::create([
                            'name' => $quizName,
                            'date' => $parsedDate,
                            'competition_id' => $this->competitionId,
                            'help' => $helpUrl,
                        ]);
                    }

                    $currentQuizName = $quizName;
                }

                // Skip if no valid quiz or no question
                if (! $currentQuiz || empty(trim($row['question']))) {
                    continue;
                }

                $questionText = (string) trim($row['question']);

                // Check if question already exists for this quiz
                $existingQuestion = QuizQuestion::where('quiz_id', $currentQuiz->id)
                    ->where('question', $questionText)
                    ->first();

                // Skip if question already exists
                if ($existingQuestion) {
                    continue;
                }

                // Create the question
                $question = QuizQuestion::create([
                    'quiz_id' => $currentQuiz->id,
                    'points' => (int) trim($row['points']),
                    'question' => $questionText,
                ]);

                // Create answers (4 answers expected)
                $answers = [];
                $correctAnswer = (int) trim($row['correct']);

                for ($i = 1; $i <= 4; $i++) {
                    $answerKey = 'answer_'.$i;
                    if (isset($row[$answerKey]) && $row[$answerKey] !== null && trim($row[$answerKey]) !== '') {
                        $answers[] = [
                            'quiz_question_id' => $question->id,
                            'answer' => (string) trim($row[$answerKey]),
                            'is_correct' => ($i === $correctAnswer) ? 1 : 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                if (! empty($answers)) {
                    QuestionAnswer::insert($answers);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function rules(): array
    {
        return [
            'quiz_name' => 'required',
            'date' => 'nullable',
            'question' => 'required',
            'points' => 'required|integer|min:1',
            'answer_1' => 'required',
            'answer_2' => 'required',
            'answer_3' => 'nullable',
            'answer_4' => 'nullable',
            'correct' => 'required|numeric|in:1,2,3,4',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'quiz_name.required' => 'Quiz name is required',
            'date.date' => 'Date must be a valid date format',
            'question.required' => 'Question is required',
            'points.required' => 'Points are required',
            'points.integer' => 'Points must be a number',
            'points.min' => 'Points must be at least 1',
            'answer_1.required' => 'Answer 1 is required',
            'answer_2.required' => 'Answer 2 is required',
            'correct.required' => 'Correct answer is required',
            'correct.in' => 'Correct answer must be 1, 2, 3, or 4',
        ];
    }
}
