// frontend/controllers/quizManagementController.js

const QuizManagementController = {
    // State variables
    currentMaterialId: null,
    currentMaterialTitle: null,
    currentQuiz: null, // Will store the quiz object { quiz_id, title, description, material_id }
    questions: [], // Will store an array of question objects
    isEditingQuestion: false, // Flag to indicate if the question form is for editing
    editingQuestionId: null, // ID of the question being edited

    // DOM Elements (cached for performance)
    elements: {
        pageContainer: null,
        materialTitleDisplay: null,
        quizDetailsSection: null,
        quizInfoDisplay: null,
        editQuizDetailsForm: null,
        quizTitleInput: null,
        quizDescriptionInput: null,
        saveQuizDetailsBtn: null,
        cancelEditQuizDetailsBtn: null,
        editQuizDetailsTriggerBtn: null,
        noQuizMessage: null,
        createQuizBtn: null,
        questionsContainer: null,
        addNewQuestionBtn: null,
        questionFormSection: null,
        questionFormTitle: null,
        questionForm: null,
        questionIdInput: null,
        questionTextInput: null,
        optionsContainer: null,
        addOptionBtn: null,
        explanationTextInput: null,
        saveQuestionBtn: null,
        cancelQuestionFormBtn: null,
        questionList: null,
        loadingQuestionsMessage: null,
        noQuestionsMessage: null
    },

    // Initialization
    init: function(materialId, materialTitle) {
        console.log("QuizManagementController init for material:", materialId, materialTitle);
        this.currentMaterialId = materialId;
        this.currentMaterialTitle = materialTitle;

        this.cacheDOMElements();
        this.resetPage();
        this.bindEvents();

        if (this.currentMaterialTitle) {
            this.elements.materialTitleDisplay.querySelector('span').textContent = this.currentMaterialTitle;
        }

        this.loadQuizData();
    },

    cacheDOMElements: function() {
        this.elements.pageContainer = $('#quiz-management-page');
        this.elements.materialTitleDisplay = $('#material-title-display');
        
        this.elements.quizDetailsSection = $('#quiz-details-section');
        this.elements.quizInfoDisplay = $('#quiz-info-display');
        this.elements.editQuizDetailsForm = $('#edit-quiz-details-form');
        this.elements.quizTitleInput = $('#quiz-title-input');
        this.elements.quizDescriptionInput = $('#quiz-description-input');
        this.elements.saveQuizDetailsBtn = $('#save-quiz-details-btn');
        this.elements.cancelEditQuizDetailsBtn = $('#cancel-edit-quiz-details-btn');
        this.elements.editQuizDetailsTriggerBtn = $('#edit-quiz-details-trigger-btn');

        this.elements.noQuizMessage = $('#no-quiz-message');
        this.elements.createQuizBtn = $('#create-quiz-btn');
        
        this.elements.questionsContainer = $('#questions-container');
        this.elements.addNewQuestionBtn = $('#add-new-question-btn');
        
        this.elements.questionFormSection = $('#question-form-section');
        this.elements.questionFormTitle = $('#question-form-title');
        this.elements.questionForm = $('#question-form');
        this.elements.questionIdInput = $('#question-id-input');
        this.elements.questionTextInput = $('#question-text-input');
        this.elements.optionsContainer = $('#options-container'); // jQuery object
        this.elements.addOptionBtn = $('#add-option-btn');
        this.elements.explanationTextInput = $('#explanation-text-input');
        this.elements.saveQuestionBtn = $('#save-question-btn');
        this.elements.cancelQuestionFormBtn = $('#cancel-question-form-btn');
        
        this.elements.questionList = $('#question-list');
        this.elements.loadingQuestionsMessage = $('#loading-questions-message');
        this.elements.noQuestionsMessage = $('#no-questions-message');
    },

    resetPage: function() {
        this.currentQuiz = null;
        this.questions = [];
        this.isEditingQuestion = false;
        this.editingQuestionId = null;

        this.elements.quizDetailsSection.hide();
        this.elements.editQuizDetailsForm.hide();
        this.elements.editQuizDetailsTriggerBtn.hide();
        this.elements.noQuizMessage.hide();
        this.elements.questionsContainer.hide();
        this.elements.questionFormSection.hide();
        this.elements.loadingQuestionsMessage.hide();
        this.elements.noQuestionsMessage.hide();
        this.elements.questionList.empty();
        this.elements.quizInfoDisplay.html('<p class="text-gray-600 dark:text-gray-300">Loading quiz details...</p>');
        this.elements.questionForm[0].reset(); // Reset form fields
        this.elements.optionsContainer.empty(); // Clear any dynamically added options
        this.addOptionInput(); // Add initial two option inputs
        this.addOptionInput();
    },

    bindEvents: function() {
        const self = this; // To maintain 'this' context in event handlers

        this.elements.createQuizBtn.off('click').on('click', function() {
            self.handleCreateQuiz();
        });

        this.elements.editQuizDetailsTriggerBtn.off('click').on('click', function() {
            self.showEditQuizDetailsForm();
        });

        this.elements.editQuizDetailsForm.off('submit').on('submit', function(e) {
            e.preventDefault();
            self.handleSaveQuizDetails();
        });
        
        this.elements.cancelEditQuizDetailsBtn.off('click').on('click', function() {
            self.hideEditQuizDetailsForm();
        });

        this.elements.addNewQuestionBtn.off('click').on('click', function() {
            self.showQuestionFormForAdd();
        });

        this.elements.addOptionBtn.off('click').on('click', function() {
            if (self.elements.optionsContainer.children('.option-entry').length < 5) {
                self.addOptionInput();
            } else {
                ToastrUtil.showWarning('Maximum 5 options allowed.');
            }
        });

        // Event delegation for remove option buttons (since they are dynamically added)
        this.elements.optionsContainer.off('click', '.remove-option-btn').on('click', '.remove-option-btn', function() {
            if (self.elements.optionsContainer.children('.option-entry').length > 2) {
                $(this).closest('.option-entry').remove();
                self.updateOptionIndices();
            } else {
                ToastrUtil.showWarning('Minimum 2 options required.');
            }
        });
        
        this.elements.questionForm.off('submit').on('submit', function(e) {
            e.preventDefault();
            self.handleSaveQuestion();
        });

        this.elements.cancelQuestionFormBtn.off('click').on('click', function() {
            self.hideQuestionForm();
        });

        // Event delegation for edit/delete question buttons
        this.elements.questionList.off('click', '.edit-question-btn').on('click', '.edit-question-btn', function() {
            const questionId = $(this).data('question-id');
            self.showQuestionFormForEdit(questionId);
        });

        this.elements.questionList.off('click', '.delete-question-btn').on('click', '.delete-question-btn', function() {
            const questionId = $(this).data('question-id');
            self.handleDeleteQuestion(questionId);
        });
    },

    loadQuizData: function() {
        const self = this;
        if (!this.currentMaterialId) {
            console.error("Material ID is not set.");
            // Optionally display an error message to the user
            ToastrUtil.showError('Error: Material ID is missing. Cannot load quiz data.');
            return;
        }

        // Simulating QuizService call
        QuizService.getQuizByMaterialId(this.currentMaterialId,
            function(response) { // successCallback
                if (response && response.success && response.quiz) {
                    self.currentQuiz = response.quiz;
                    self.displayQuizDetails();
                    self.loadQuestions();
                } else {
                    // No quiz found for this material
                    self.currentQuiz = null;
                    self.displayNoQuizView();
                }
            },
            function(error) { // errorCallback
                console.error("Error loading quiz data:", error);
                ToastrUtil.showError('Failed to load quiz data. Please try again.');
                self.displayNoQuizView(); // Or a generic error view
            }
        );
    },

    displayQuizDetails: function() {
        if (this.currentQuiz) {
            this.elements.quizInfoDisplay.html(`
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white">${this.currentQuiz.title || 'Quiz Title Not Set'}</h3>
                <p class="text-gray-600 dark:text-gray-400 mt-1">${this.currentQuiz.description || 'No description provided.'}</p>
            `);
            this.elements.quizTitleInput.val(this.currentQuiz.title || '');
            this.elements.quizDescriptionInput.val(this.currentQuiz.description || '');
            this.elements.quizDetailsSection.show();
            this.elements.editQuizDetailsTriggerBtn.show();
            this.elements.editQuizDetailsForm.hide(); // Hide form initially
            this.elements.noQuizMessage.hide();
            this.elements.questionsContainer.show();
        }
    },

    displayNoQuizView: function() {
        this.elements.quizDetailsSection.hide();
        this.elements.questionsContainer.hide();
        this.elements.noQuizMessage.show();
    },

    handleCreateQuiz: function() {
        const self = this;
        const defaultQuizTitle = `Quiz for: ${this.currentMaterialTitle || 'this Material'}`;
        const quizData = {
            title: defaultQuizTitle,
            description: "",
            material_id: this.currentMaterialId
        };

        QuizService.createQuiz(this.currentMaterialId, quizData,
            function(response) {
                if (response && response.success && response.quiz) {
                    self.currentQuiz = response.quiz;
                    ToastrUtil.showSuccess('Quiz created successfully!');
                    self.displayQuizDetails();
                    self.loadQuestions(); // Load (empty) questions for the new quiz
                } else {
                    ToastrUtil.showError(response.message || 'Failed to create quiz.');
                }
            },
            function(error) {
                console.error("Error creating quiz:", error);
                ToastrUtil.showError('An error occurred while creating the quiz.');
            }
        );
    },

    showEditQuizDetailsForm: function() {
        this.elements.quizInfoDisplay.hide();
        this.elements.editQuizDetailsTriggerBtn.hide();
        this.elements.editQuizDetailsForm.show();
        this.elements.quizTitleInput.val(this.currentQuiz.title || '');
        this.elements.quizDescriptionInput.val(this.currentQuiz.description || '');
    },

    hideEditQuizDetailsForm: function() {
        this.elements.editQuizDetailsForm.hide();
        this.elements.quizInfoDisplay.show();
        this.elements.editQuizDetailsTriggerBtn.show();
    },

    handleSaveQuizDetails: function() {
        const self = this;
        const updatedQuizData = {
            title: this.elements.quizTitleInput.val().trim(),
            description: this.elements.quizDescriptionInput.val().trim()
        };

        if (!updatedQuizData.title) {
            ToastrUtil.showWarning('Quiz title cannot be empty.');
            return;
        }

        if (!this.currentQuiz || !this.currentQuiz.quiz_id) {
            ToastrUtil.showError('Cannot save details: Quiz ID is missing.');
            return;
        }

        QuizService.updateQuiz(this.currentQuiz.quiz_id, updatedQuizData,
            function(response) {
                if (response && response.success && response.quiz) {
                    self.currentQuiz = response.quiz; // Update with potentially updated data from backend
                    ToastrUtil.showSuccess('Quiz details updated successfully!');
                    self.displayQuizDetails(); // Re-render with new details
                    self.hideEditQuizDetailsForm();
                } else {
                    ToastrUtil.showError(response.message || 'Failed to update quiz details.');
                }
            },
            function(error) {
                console.error("Error updating quiz details:", error);
                ToastrUtil.showError('An error occurred while updating quiz details.');
            }
        );
    },

    loadQuestions: function() {
        const self = this;
        if (!this.currentQuiz || !this.currentQuiz.quiz_id) {
            console.warn("No current quiz or quiz_id to load questions for.");
            this.elements.noQuestionsMessage.show();
            this.elements.loadingQuestionsMessage.hide();
            this.elements.questionList.empty();
            return;
        }

        this.elements.loadingQuestionsMessage.show();
        this.elements.noQuestionsMessage.hide();
        this.elements.questionList.empty();

        QuizService.getQuestionsByQuizId(this.currentQuiz.quiz_id,
            function(response) {
                self.elements.loadingQuestionsMessage.hide();
                if (response && response.success && response.questions) {
                    self.questions = response.questions;
                    if (self.questions.length > 0) {
                        self.renderQuestionList();
                    } else {
                        self.elements.noQuestionsMessage.show();
                    }
                } else {
                    self.questions = [];
                    self.elements.noQuestionsMessage.show();
                    // ToastrUtil.showWarning(response.message || 'No questions found or failed to load.');
                }
            },
            function(error) {
                self.elements.loadingQuestionsMessage.hide();
                console.error("Error loading questions:", error);
                ToastrUtil.showError('Failed to load questions.');
                self.elements.noQuestionsMessage.show();
            }
        );
    },

    renderQuestionList: function() {
        const self = this;
        this.elements.questionList.empty();
        if (this.questions.length === 0) {
            this.elements.noQuestionsMessage.show();
            return;
        }
        this.elements.noQuestionsMessage.hide();

        this.questions.forEach((question, index) => {
            let optionsHtml = '<ul class="mt-3 list-disc list-inside text-gray-700 dark:text-gray-300 space-y-1">';
            if (question.options && question.options.length > 0) {
                question.options.forEach(opt => {
                    optionsHtml += `<li class="${opt.is_correct ? 'font-bold text-green-600 dark:text-green-400' : ''}">${opt.option_text}${opt.is_correct ? ' (Correct)' : ''}</li>`;
                });
            } else {
                optionsHtml += '<li>No options available for this question.</li>';
            }
            optionsHtml += '</ul>';

            const questionCard = `
                <div class="p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg question-card" data-question-id="${question.question_id}">
                    <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">Question ${index + 1}</p>
                    <p class="mt-1 text-lg font-medium text-gray-900 dark:text-white">${question.question_text || 'N/A'}</p>
                    ${optionsHtml}
                    ${question.explanation ? `<p class="mt-3 text-sm text-gray-600 dark:text-gray-400"><span class="font-semibold">Explanation:</span> ${question.explanation}</p>` : ''}
                    <div class="mt-4 flex space-x-3 justify-end">
                        <button class="edit-question-btn px-3 py-1.5 text-sm bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-md shadow-sm" data-question-id="${question.question_id}">Edit</button>
                        <button class="delete-question-btn px-3 py-1.5 text-sm bg-red-600 hover:bg-red-700 text-white font-semibold rounded-md shadow-sm" data-question-id="${question.question_id}">Delete</button>
                    </div>
                </div>
            `;
            self.elements.questionList.append(questionCard);
        });
    },
    
    showQuestionFormForAdd: function() {
        this.isEditingQuestion = false;
        this.editingQuestionId = null;
        this.elements.questionForm[0].reset();
        this.elements.questionIdInput.val('');
        this.elements.optionsContainer.empty();
        this.addOptionInput(); // Add initial two options
        this.addOptionInput();
        this.elements.questionFormTitle.text('Add New Question');
        this.elements.questionFormSection.slideDown();
    },

    showQuestionFormForEdit: function(questionId) {
        const question = this.questions.find(q => q.question_id == questionId); // Use '==' for potential type coercion if IDs are mixed string/number
        if (!question) {
            ToastrUtil.showError('Question not found for editing.');
            return;
        }

        this.isEditingQuestion = true;
        this.editingQuestionId = questionId;

        this.elements.questionForm[0].reset();
        this.elements.questionIdInput.val(question.question_id);
        this.elements.questionTextInput.val(question.question_text);
        this.elements.explanationTextInput.val(question.explanation || '');
        
        this.elements.optionsContainer.empty();
        if (question.options && question.options.length > 0) {
            question.options.forEach((opt, index) => {
                this.addOptionInput(opt.option_text, opt.is_correct, index);
            });
        } else { // Add default 2 empty options if none exist
            this.addOptionInput(); 
            this.addOptionInput();
        }
        
        this.elements.questionFormTitle.text('Edit Question');
        this.elements.questionFormSection.slideDown();
        // Scroll to the form for better UX
        $('html, body').animate({
            scrollTop: this.elements.questionFormSection.offset().top - 20 // 20px offset from top
        }, 500);
    },

    hideQuestionForm: function() {
        this.elements.questionFormSection.slideUp();
        this.elements.questionForm[0].reset();
        this.isEditingQuestion = false;
        this.editingQuestionId = null;
    },

    addOptionInput: function(text = '', isCorrect = false, index = null) {
        // If no index provided, use the current number of options as the next index
        const optionIndex = (index !== null) ? index : this.elements.optionsContainer.children('.option-entry').length;

        const optionHtml = `
            <div class="flex items-center mb-2 option-entry">
                <input type="text" name="option_text[]" class="flex-grow px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-l-md shadow-sm sm:text-sm text-gray-900 dark:text-white" placeholder="Option text ${optionIndex + 1}" value="${text}" required>
                <div class="flex items-center px-3 py-2 border border-l-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-600">
                    <input type="radio" name="correct_option_index" value="${optionIndex}" class="form-radio h-4 w-4 text-indigo-600 dark:text-indigo-400 border-gray-300 dark:border-gray-500 focus:ring-indigo-500" ${isCorrect ? 'checked' : ''}>
                    <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Correct</label>
                </div>
                <button type="button" class="remove-option-btn px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-r-md shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" clip-rule="evenodd" /></svg>
                </button>
            </div>
        `;
        this.elements.optionsContainer.append(optionHtml);
        this.updateOptionIndices(); // Ensure radio button values are correct
    },

    updateOptionIndices: function() {
        this.elements.optionsContainer.children('.option-entry').each(function(idx, el) {
            $(el).find('input[type="radio"]').val(idx);
            $(el).find('input[type="text"]').attr('placeholder', `Option text ${idx + 1}`);
        });
    },

    handleSaveQuestion: function() {
        const self = this;
        const questionData = {
            question_id: this.elements.questionIdInput.val() || null, // For edit mode
            question_text: this.elements.questionTextInput.val().trim(),
            explanation: this.elements.explanationTextInput.val().trim(),
            options: []
        };

        if (!questionData.question_text) {
            ToastrUtil.showWarning('Question text cannot be empty.');
            return;
        }

        let correctOptionSelected = false;
        this.elements.optionsContainer.children('.option-entry').each(function(index) {
            const optionText = $(this).find('input[type="text"]').val().trim();
            const isCorrect = $(this).find('input[type="radio"]').is(':checked');
            if (isCorrect) correctOptionSelected = true;
            if (optionText) { // Only add non-empty options
                questionData.options.push({ option_text: optionText, is_correct: isCorrect });
            }
        });
        
        if (questionData.options.length < 2) {
            ToastrUtil.showWarning('Please provide at least 2 options.');
            return;
        }
        if (!correctOptionSelected) {
            ToastrUtil.showWarning('Please mark one option as correct.');
            return;
        }

        const quizId = this.currentQuiz ? this.currentQuiz.quiz_id : null;
        if (!quizId) {
            ToastrUtil.showError('Quiz ID is missing. Cannot save question.');
            return;
        }

        const successCb = function(response) {
            if (response && response.success) {
                ToastrUtil.showSuccess(`Question ${self.isEditingQuestion ? 'updated' : 'added'} successfully!`);
                self.hideQuestionForm();
                self.loadQuestions(); // Refresh the question list
            } else {
                ToastrUtil.showError(response.message || `Failed to ${self.isEditingQuestion ? 'update' : 'add'} question.`);
            }
        };
        const errorCb = function(error) {
            console.error(`Error ${self.isEditingQuestion ? 'updating' : 'adding'} question:`, error);
            ToastrUtil.showError(`An error occurred while ${self.isEditingQuestion ? 'updating' : 'adding'} the question.`);
        };

        if (this.isEditingQuestion && questionData.question_id) {
            QuizService.updateQuestion(questionData.question_id, questionData, successCb, errorCb);
        } else {
            QuizService.addQuestionToQuiz(quizId, questionData, successCb, errorCb);
        }
    },

    handleDeleteQuestion: function(questionId) {
        const self = this;
        // SweetAlert for confirmation
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                QuizService.deleteQuestion(questionId,
                    function(response) {
                        if (response && response.success) {
                            ToastrUtil.showSuccess('Question deleted successfully!');
                            self.loadQuestions(); // Refresh the list
                        } else {
                            ToastrUtil.showError(response.message || 'Failed to delete question.');
                        }
                    },
                    function(error) {
                        console.error("Error deleting question:", error);
                        ToastrUtil.showError('An error occurred while deleting the question.');
                    }
                );
            }
        });
    }
};
