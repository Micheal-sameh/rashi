<?php

namespace App\Http\Controllers;

use App\DTOs\quizCreateDTO;
use App\Http\Requests\CreateQuizRequest;
use App\Http\Requests\UpdateQuizRequest;
use App\Models\Quiz;
use App\Rules\CheckIsActiveRule;
use App\Services\CompetitionService;
use App\Services\QuizService;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
    public function __construct(
        protected QuizService $quizService,
        protected CompetitionService $competitionService,
    ) {
        //
    }

    public function index()
    {
        $quizzes = $this->quizService->index();

        return view('quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        $competitions = $this->competitionService->dropdown();

        return view('quizzes.create', compact('competitions'));
    }

    public function store(CreateQuizRequest $request)
    {
        $input = new QuizCreateDTO(...$request->only(
            'name', 'date', 'competition_id', 'questions'
        ));
        $this->quizService->store($input);

        return redirect()->route('quizzes.index')->with('success', 'quiz created successfully');

    }

    public function edit($id)
    {
        $quiz = $this->quizService->show($id);

        return view('quizzes.edit', compact('quiz'));
    }

    public function update($id, UpdateQuizRequest $request)
    {
        $input = new quizCreateDTO(...$request->only(
            'name', 'date',
        ));

        $this->quizService->update($id, $input);

        return redirect()->route('quizzes.index')->with('success', 'quiz updated successfully');
    }

    public function dropdown($id)
    {
        $quizzes = $this->quizService->dropdown($id);

        return $quizzes;
    }

    public function delete($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => [new CheckIsActiveRule(new Quiz)],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $this->quizService->delete($id);

        return redirect()->route('quizzes.index')->with('success', 'quiz deleted successfully');
    }
}
