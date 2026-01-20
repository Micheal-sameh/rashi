<?php

namespace App\Http\Controllers;

use App\DTOs\QuestionCreateDTO;
use App\Http\Requests\CreateQuestionRequest;
use App\Http\Requests\QuizQuestionIndexRequest;
use App\Models\QuizQuestion;
use App\Rules\CheckIsActiveRule;
use App\Services\CompetitionService;
use App\Services\QuizQuestionService;
use App\Services\QuizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizQuestionController extends Controller
{
    public function __construct(
        protected QuizQuestionService $quizQuestionService,
        protected QuizService $quizService,
        protected CompetitionService $competitionService
    ) {}

    public function index(QuizQuestionIndexRequest $request)
    {
        $questions = $this->quizQuestionService->index($request);
        $competitions = $this->competitionService->dropdown();

        return view('quiz_questions.index', compact('questions', 'competitions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $quiz_id = $request->quiz_id;

        return view('quiz_questions.create', compact('quiz_id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateQuestionRequest $request)
    {
        $input = new QuestionCreateDTO(...$request->only(
            'question', 'quiz_id', 'points', 'answers', 'correct'
        ));
        $question = $this->quizQuestionService->create($input);

        // Handle image upload
        if ($request->hasFile('question_image')) {
            $question->addMediaFromRequest('question_image')
                ->toMediaCollection('question_image');
        }

        return redirect()->back()->with('success', 'Question created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $question = $this->quizQuestionService->show($id);

        return view('quiz_questions.edit', compact('question'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $input = new QuestionCreateDTO(...$request->only(
            'question', 'quiz_id', 'points', 'answers', 'correct'
        ));
        $question = $this->quizQuestionService->update($id, $input);

        // Handle image removal
        if ($request->has('remove_image')) {
            $question->clearMediaCollection('question_image');
        }

        // Handle new image upload
        if ($request->hasFile('question_image')) {
            $question->clearMediaCollection('question_image');
            $question->addMediaFromRequest('question_image')
                ->toMediaCollection('question_image');
        }

        return redirect()->route('questions.index')->with('success', 'Question updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => [new CheckIsActiveRule(new QuizQuestion)],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $this->quizQuestionService->delete($id);

        return redirect()->back()->with('success', 'Question deleted successfully');
    }
}
