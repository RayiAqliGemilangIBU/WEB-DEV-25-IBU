var quizServices = {
    currentQuestionIndex: 0,
    correctAnswers: 0,
    startTime: null,

    // Function to fetch and display the list of quizzes
    getQuizList: function() {
        var quizData = [
            { id: 1, title: 'Oxidation Reactions' },
            { id: 2, title: 'Esterification Reactions' }
        ];

        var quizList = $('#quizList');
        quizData.forEach(function(quiz) {
            var listItem = $('<li><a href="#" class="quizItem" data-id="' + quiz.id + '">' + quiz.title + '</a></li>');
            quizList.append(listItem);
        });

        // Handle quiz item click to start the quiz
        $(document).on('click', '.quizItem', function(event) {
            event.preventDefault();
            var quizId = $(this).data('id');
            quizServices.loadQuizContent(quizId);
        });
    },

    // Function to fetch and display quiz content
    loadQuizContent: function(id) {
        var fileName = '../static/quiz/quiz' + id + '.json';  // Path to quiz data file
        console.log('Loading quiz from:', fileName);  // Debug log for path

        $.getJSON(fileName, function(data) {
            var quizHTML = '<h3>' + data.title + '</h3><p>' + data.description + '</p>';
            var question = data.questions[quizServices.currentQuestionIndex];
            quizHTML += '<div class="question" id="question-' + quizServices.currentQuestionIndex + '">';
            quizHTML += '<p>' + question.questionText + '</p>';
            quizHTML += '<ul>';

            // Show options for the current question
            question.options.forEach(function(option) {
                quizHTML += `
                    <li>
                        <label>
                            <input type="radio" name="question-${quizServices.currentQuestionIndex}" value="${option.id}" />
                            ${option.text}
                        </label>
                    </li>`;
            });

            quizHTML += '</ul>';
            quizHTML += '<button id="next-question" style="display: none; background-color: black; color: white; border: none; padding: 5px 15px; border-radius: 5px; cursor: pointer; margin: 2px 0;">Next</button>';  // Initially hidden
            $('#dynamicContent').html(quizHTML);

            // Set timer if it's the first question
            if (quizServices.currentQuestionIndex === 0) {
                quizServices.startTime = Date.now();
            }

            // Handle when the user selects an answer
            $('input[name="question-' + quizServices.currentQuestionIndex + '"]').on('change', function() {
                quizServices.checkAnswer(data.questions);
                $('#next-question').show();  // Show the Next button after selection
            });

            // Handle next question action
            $('#next-question').on('click', function() {
                quizServices.currentQuestionIndex++;
                if (quizServices.currentQuestionIndex < data.questions.length) {
                    quizServices.loadQuizContent(id);
                } else {
                    quizServices.showResults(data.questions.length);
                }
            });
        }).fail(function(xhr, status, error) {
            console.error('Error fetching quiz:', error);
            $('#dynamicContent').html('<p>Error loading quiz content. Please try again later.</p>');
        });
    },

    // Function to check answer and show explanation
    checkAnswer: function(questions) {
        var selectedOption = $('input[name="question-' + quizServices.currentQuestionIndex + '"]:checked');
        var question = questions[quizServices.currentQuestionIndex];
        var correctOption = question.options.find(option => option.correct);
        var selectedAnswerId = selectedOption.val();
        
        // Show correct/incorrect and explanation
        var explanation = `<p><strong>Explanation:</strong> ${question.explanation}</p>`;
        
        // Iterate over each option and mark the answer
        $('input[name="question-' + quizServices.currentQuestionIndex + '"]').each(function() {
            var answerId = $(this).val();
            var label = $(this).parent();
            
            // Show ✅ next to correct answer
            if (answerId === correctOption.id) {
                label.append(" ✅");
            }
            // Show ❌ next to incorrect answer
            else if (answerId === selectedAnswerId && selectedAnswerId !== correctOption.id) {
                label.append(" ❌");
            }
        });

        // Display explanation
        $('#dynamicContent').append(explanation);
    },

    // Function to show the final result
    showResults: function(totalQuestions) {
        var endTime = Date.now();
        var timeSpent = (endTime - quizServices.startTime) / 1000; // time in seconds
        var percentage = (quizServices.correctAnswers / totalQuestions) * 100;

        var resultHTML = `
            <h3>Quiz Finished!</h3>
            <p>You answered ${quizServices.correctAnswers} out of ${totalQuestions} questions correctly.</p>
            <p>Your score: ${percentage.toFixed(2)}%</p>
            <p>Time spent: ${timeSpent.toFixed(2)} seconds</p>
        `;
        $('#dynamicContent').html(resultHTML);
    }
};
