<?php

namespace App\Http\Controllers;

use App\DTOs\quizCreateDTO;
use App\Http\Requests\CreateQuizRequest;
use App\Services\CompetitionService;
use App\Services\QuizService;

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

    public function update($id, CreatequizRequest $request)
    {
        $input = new quizCreateDTO(...$request->only(
            'name', 'start_at', 'end_at',
        ));

        $this->quizService->update($id, $input, $request->image);

        return redirect()->route('quizzes.index')->with('success', 'quiz updated successfully');
    }
}
