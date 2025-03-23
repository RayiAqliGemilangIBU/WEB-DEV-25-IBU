// textMaterialServices.js

var textMaterialServices = {
    // Get the list of materials and quizzes
    getMaterialAndQuizList: function() {
        // Material and Quiz data
        var materials = [
            { id: 1, title: 'Material 1: Oxidation of Alcohols' },
            { id: 2, title: 'Material 2: Esterification of Alcohols' }
        ];

        var quizzes = [
            { id: 1, title: 'Quiz 1: Oxidation Reactions' },
            { id: 2, title: 'Quiz 2: Esterification Reactions' }
        ];

        // Populate the material list
        var materialList = $('#materialList');
        materials.forEach(function(material) {
            var listItem = $('<li><a href="#" class="materialItem" data-id="' + material.id + '">' + material.title + '</a></li>');
            materialList.append(listItem);
        });

        // Populate the quiz list
        var quizList = $('#quizList');
        quizzes.forEach(function(quiz) {
            var listItem = $('<li><a href="#" class="quizItem" data-id="' + quiz.id + '">' + quiz.title + '</a></li>');
            quizList.append(listItem);
        });
    },

    // Function to load material content dynamically into #dynamicContent
    loadMaterialContent: function(id) {
        var fileName = '../static/textMaterial/textMaterial' + id + '.json';
        $.getJSON(fileName, function(data) {
            var content = '<h3>' + data.title + '</h3>' + data.content;
            $('#dynamicContent').html(content);
        }).fail(function(xhr, status, error) {
            console.error('Error fetching material:', error);
            $('#dynamicContent').html('<p>Error loading content. Please try again later.</p>');
        });
    },

    // Function to load quiz content dynamically (for future quizzes)
    loadQuizContent: function(id) {
        // Placeholder for quiz content (you can replace this with actual quiz content logic)
        var quizContent = '<h3>Quiz ' + id + ' Content</h3><p>Quiz content will be displayed here.</p>';
        $('#dynamicContent').html(quizContent);
    }
};
