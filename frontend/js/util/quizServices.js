// quizServices.js

var quizServices = {
    // Function to fetch and display the list of quizzes
    getQuizList: function() {
        // Quiz data (can be fetched from a JSON file or API)
        var quizzes = [
            { id: 1, title: 'Quiz 1: Oxidation Reactions' },
            { id: 2, title: 'Quiz 2: Esterification Reactions' }
        ];

        // Populate the quiz list
        var quizList = $('#quizList');
        quizzes.forEach(function(quiz) {
            var listItem = $('<li><a href="#" class="quizItem" data-id="' + quiz.id + '">' + quiz.title + '</a></li>');
            quizList.append(listItem);
        });
    },

    // Function to load quiz content dynamically into #dynamicContent
    loadQuizContent: function(id) {
        var fileName = '../static/quiz/quiz' + id + '.json';  // Path to quiz data file
        console.log('Loading quiz from:', fileName);  // Debug log for path
    
        $.getJSON(fileName, function(data) {
            console.log(data);  // Log the loaded data
            var quizHTML = '<h3>' + data.title + '</h3><p>' + data.description + '</p>';
            data.questions.forEach(function(question, index) {
                quizHTML += '<div class="question" id="question-' + index + '">';
                quizHTML += '<p>' + question.questionText + '</p>';
                quizHTML += '<ul>';
                question.options.forEach(function(option) {
                    quizHTML += '<li><label>';
                    quizHTML += '<input type="radio" name="question-' + index + '" value="' + option.id + '" />';
                    quizHTML += option.text;
                    quizHTML += '</label></li>';
                });
                quizHTML += '</ul></div>';
            });
            quizHTML += '<button id="submit-quiz">Submit</button>';
            $('#dynamicContent').html(quizHTML);
    
            // Add event for submit button to calculate score
            $('#submit-quiz').on('click', function() {
                var score = 0;
                data.questions.forEach(function(question, index) {
                    const selectedOption = $('input[name="question-' + index + '"]:checked');
                    if (selectedOption.length && selectedOption.val() === question.correctAnswer) {
                        score++;
                    }
                });
                alert('Your score: ' + score + ' out of ' + data.questions.length);
            });
        }).fail(function(xhr, status, error) {
            console.error('Error fetching quiz:', error);
            $('#dynamicContent').html('<p>Error loading quiz content. Please try again later.</p>');
        });
    }
    
};
