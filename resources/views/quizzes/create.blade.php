@extends('layouts.sideBar')

@section('content')
<style>
    .custom-container {
        max-width: 90%;
        margin: auto;
    }

    @media (max-width: 768px) {
        .custom-container {
            width: 100%;
            padding: 10px;
        }
    }
</style>

<div class="custom-container">
    <h2 class="mb-4">Create Quiz</h2>

    <form action="{{ route('quizzes.store') }}" method="POST">
        @csrf

        <!-- Quiz Information -->
        <div class="mb-3">
            <label for="name" class="form-label">Quiz Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="competition_id" class="form-label">Competition ID</label>
            <select name="competition_id" class="form-select" required>
                <option value="">choose competition</option>
                @foreach($competitions as $competition)
                <option value="{{ $competition->id }}">{{ $competition->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Questions Section -->
        <div id="questions-section">
            <h4>Questions</h4>

            <div class="question mb-4 border p-3 rounded" id="question-0">
                <label>Question 1</label>
                <input type="text" name="questions[0][question]" class="form-control mb-2" placeholder="Enter question text" required>

                <!-- Points -->
                <div class="mb-2">
                    <label>Points</label>
                    <input type="number" name="questions[0][points]" class="form-control" min="1" required>
                </div>

                @for ($i = 0; $i < 4; $i++)
                    <div class="input-group mb-1">
                        <div class="input-group-text">
                            <input type="radio" name="questions[0][correct]" value="{{ $i }}" required>
                        </div>
                        <input type="text" name="questions[0][answers][{{ $i }}]" class="form-control" placeholder="Answer {{ $i + 1 }}" required>
                    </div>
                @endfor

                <button type="button" class="btn btn-danger btn-sm mt-2" onclick="removeQuestion('question-0')">Remove</button>
            </div>
        </div>

        <!-- Add Question Button -->
        <button type="button" class="btn btn-secondary mb-3" onclick="addQuestion()">Add Another Question</button>

        <br>
        <button type="submit" class="btn btn-primary">Create Quiz</button>
    </form>
</div>

<!-- JavaScript to manage questions -->
<script>
    let questionIndex = 1;

    function addQuestion() {
        const section = document.getElementById('questions-section');
        const questionId = `question-${questionIndex}`;

        let answersHTML = '';
        for (let i = 0; i < 4; i++) {
            answersHTML += `
                <div class="input-group mb-1">
                    <div class="input-group-text">
                        <input type="radio" name="questions[${questionIndex}][correct]" value="${i}" required>
                    </div>
                    <input type="text" name="questions[${questionIndex}][answers][${i}]" class="form-control" placeholder="Answer ${i + 1}" required>
                </div>
            `;
        }

        const questionHTML = `
            <div class="question mb-4 border p-3 rounded" id="${questionId}">
                <label>Question ${questionIndex + 1}</label>
                <input type="text" name="questions[${questionIndex}][question]" class="form-control mb-2" placeholder="Enter question text" required>

                <div class="mb-2">
                    <label>Points</label>
                    <input type="number" name="questions[${questionIndex}][points]" class="form-control" min="1" required>
                </div>

                ${answersHTML}
                <button type="button" class="btn btn-danger btn-sm mt-2" onclick="removeQuestion('${questionId}')">Remove</button>
            </div>
        `;

        section.insertAdjacentHTML('beforeend', questionHTML);
        questionIndex++;
    }

    function removeQuestion(id) {
        const questionDiv = document.getElementById(id);
        if (questionDiv) {
            questionDiv.remove();
        }
    }
</script>
@endsection
