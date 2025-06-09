// File: frontend/controllers/quizManagementController.js

// Asumsikan QuizService, toastr (huruf kecil), dan Swal (SweetAlert2) sudah tersedia.
// Juga asumsikan elemen-elemen DOM dengan ID yang sesuai ada di quizManagement.html

const QuizManagementController = {
    // State variables
    currentMaterialId: null,
    currentMaterialTitle: null,
    currentQuiz: null, // { quiz_id, title, description, material_id, ... }
    questions: [],     // Array of { question_id, quiz_id, header, explanation, answer (boolean) }
    isEditingQuestion: false,
    editingQuestionId: null,

    // Cache DOM Elements
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
        questionHeaderInput: null, 
        questionExplanationInput: null,
        questionAnswerTrueRadio: null,
        questionAnswerFalseRadio: null,
        saveQuestionBtn: null,
        cancelQuestionFormBtn: null,
        questionList: null,
        loadingQuestionsMessage: null,
        noQuestionsMessage: null
    },

    init: function(materialId, materialTitle) {
        console.log("QuizManagementController: init CALLED. Material ID:", materialId, "Material Title:", materialTitle); // LOG INIT
        this.currentMaterialId = materialId;
        this.currentMaterialTitle = materialTitle;

        this.cacheDOMElements();
        this.resetPageForNewMaterial();
        this.bindEvents();

        if (this.currentMaterialTitle) {
            this.elements.materialTitleDisplay.find('span').text(this.currentMaterialTitle);
        } else {
            this.elements.materialTitleDisplay.find('span').text('[Title Not Provided]');
            console.warn("QuizManagementController: Material title was undefined or null during init.");
        }

        this.loadQuizDataForMaterial();
    },

    cacheDOMElements: function() {
        console.log("QuizManagementController: cacheDOMElements CALLED.");
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
        this.elements.questionHeaderInput = $('#question-header-input');
        this.elements.questionExplanationInput = $('#question-explanation-input');
        this.elements.questionAnswerTrueRadio = $('input[name="question-answer"][value="true"]');
        this.elements.questionAnswerFalseRadio = $('input[name="question-answer"][value="false"]');
        
        this.elements.saveQuestionBtn = $('#save-question-btn');
        this.elements.cancelQuestionFormBtn = $('#cancel-question-form-btn');
        
        this.elements.questionList = $('#question-list');
        this.elements.loadingQuestionsMessage = $('#loading-questions-message');
        this.elements.noQuestionsMessage = $('#no-questions-message');
        console.log("QuizManagementController: DOM elements cached.");
    },

    resetPageForNewMaterial: function() {
        console.log("QuizManagementController: resetPageForNewMaterial CALLED.");
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
        if (this.elements.questionForm && this.elements.questionForm.length > 0) {
            this.elements.questionForm[0].reset();
        }
        console.log("QuizManagementController: Page reset complete.");
    },

    bindEvents: function() {
        console.log("QuizManagementController: bindEvents CALLED.");
        const self = this;

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
        
        this.elements.questionForm.off('submit').on('submit', function(e) {
            e.preventDefault();
            self.handleSaveQuestion();
        });

        this.elements.cancelQuestionFormBtn.off('click').on('click', function() {
            self.hideQuestionForm();
        });

        this.elements.questionList.off('click', '.edit-question-btn').on('click', '.edit-question-btn', function() {
            const questionId = $(this).data('question-id');
            self.showQuestionFormForEdit(questionId);
        });

        this.elements.questionList.off('click', '.delete-question-btn').on('click', '.delete-question-btn', function() {
            const questionId = $(this).data('question-id');
            self.handleDeleteQuestion(questionId);
        });
        console.log("QuizManagementController: Events bound.");
    },

    loadQuizDataForMaterial: function() {
        const self = this;
        console.log("QuizManagementController: loadQuizDataForMaterial CALLED. Material ID:", this.currentMaterialId); // LOG 1

        if (!this.currentMaterialId) {
            console.error("QuizManagementController: Material ID is missing in loadQuizDataForMaterial."); // LOG 2
            toastr.error('Error: Material ID is missing.'); 
            this.displayNoQuizView(); 
            return;
        }

        console.log("QuizManagementController: Calling QuizService.getQuizByMaterialId with ID:", this.currentMaterialId); // LOG 3
        QuizService.getQuizByMaterialId(this.currentMaterialId,
            function(response) { // successCallback
                console.log("QuizManagementController: SUCCESS from QuizService.getQuizByMaterialId. Response:", JSON.stringify(response, null, 2)); // LOG 4

                if (response && response.success !== undefined) { 
                    if (response.quiz) { 
                        console.log("QuizManagementController: Quiz found in response.", response.quiz); // LOG 5
                        self.currentQuiz = response.quiz;
                        self.displayQuizDetails(); 
                        self.loadQuestionsForCurrentQuiz(); 
                    } else {
                        console.log("QuizManagementController: No quiz object in response (response.quiz is null/undefined).", response); // LOG 6
                        self.currentQuiz = null;
                        self.displayNoQuizView(); 
                    }
                } else {
                     console.error("QuizManagementController: Response from getQuizByMaterialId is not in expected format or success is undefined.", response); // LOG 7
                     toastr.error( (response && response.message) ? response.message : 'Failed to load quiz data due to unexpected response format.' );
                     self.displayNoQuizView(); 
                }
            },
            function(error) { // errorCallback
                console.error("QuizManagementController: ERROR from QuizService.getQuizByMaterialId. Error object:", JSON.stringify(error, null, 2)); // LOG 8
                let errorMessage = 'An error occurred while loading quiz data.';
                if (error && typeof error === 'object' && error.message) {
                    errorMessage = error.message;
                } else if (typeof error === 'string') {
                    errorMessage = error;
                } else if (error && error.responseJSON && error.responseJSON.message) {
                    errorMessage = error.responseJSON.message;
                } else if (error && error.statusText) {
                    errorMessage = `Error ${error.status || ''}: ${error.statusText}`;
                }
                toastr.error(errorMessage);
                self.displayNoQuizView(); 
            }
        );
    },

    displayQuizDetails: function() {
        console.log("QuizManagementController: displayQuizDetails CALLED. Current Quiz:", this.currentQuiz);
        if (this.currentQuiz) {
            this.elements.quizInfoDisplay.html(`
                <h3 class="text-xl font-semibold text-black ">${this.currentQuiz.title || 'Quiz Title Not Set'}</h3>
                <p class="text-black mt-1">${this.currentQuiz.description || 'No description provided.'}</p>
            `);
            this.elements.quizTitleInput.val(this.currentQuiz.title || '');
            this.elements.quizDescriptionInput.val(this.currentQuiz.description || '');
            
            this.elements.quizDetailsSection.show();
            this.elements.editQuizDetailsTriggerBtn.show();
            this.elements.editQuizDetailsForm.hide();
            this.elements.noQuizMessage.hide();
            this.elements.questionsContainer.show();
            console.log("QuizManagementController: Quiz details displayed.");
        } else {
            console.log("QuizManagementController: No currentQuiz to display, calling displayNoQuizView.");
            this.displayNoQuizView();
        }
    },

    displayNoQuizView: function() {
        console.log("QuizManagementController: displayNoQuizView CALLED.");
        this.elements.quizDetailsSection.hide();
        this.elements.questionsContainer.hide();
        this.elements.noQuizMessage.show();
        console.log("QuizManagementController: 'No quiz' view displayed.");
    },

    handleCreateQuiz: function() {
        console.log("QuizManagementController: handleCreateQuiz CALLED.");
        const self = this;
        const defaultQuizTitle = `Quiz for: ${this.currentMaterialTitle || 'this Material'}`;
        const quizData = {
            material_id: this.currentMaterialId,
            title: defaultQuizTitle,
            description: "Quiz for material " + (this.currentMaterialTitle || this.currentMaterialId)
        };

        QuizService.createQuiz(quizData,
            function(response) {
                console.log("QuizManagementController: Response from createQuiz:", JSON.stringify(response, null, 2));
                if (response && response.success && response.quiz) {
                    self.currentQuiz = response.quiz;
                    toastr.success('Quiz created successfully!');
                    self.displayQuizDetails();
                    self.loadQuestionsForCurrentQuiz();
                } else {
                    toastr.error(response.message || 'Failed to create quiz.');
                }
            },
            function(error) {
                console.error("QuizManagementController: Error creating quiz:", error);
                toastr.error('An error occurred while creating the quiz.');
            }
        );
    },

    showEditQuizDetailsForm: function() {
        console.log("QuizManagementController: showEditQuizDetailsForm CALLED.");
        this.elements.quizInfoDisplay.hide();
        this.elements.editQuizDetailsTriggerBtn.hide();
        this.elements.editQuizDetailsForm.show();
        this.elements.quizTitleInput.val(this.currentQuiz.title || '');
        this.elements.quizDescriptionInput.val(this.currentQuiz.description || '');
    },

    hideEditQuizDetailsForm: function() {
        console.log("QuizManagementController: hideEditQuizDetailsForm CALLED.");
        this.elements.editQuizDetailsForm.hide();
        this.elements.quizInfoDisplay.show();
        this.elements.editQuizDetailsTriggerBtn.show();
    },

    handleSaveQuizDetails: function() {
        console.log("QuizManagementController: handleSaveQuizDetails CALLED.");
        const self = this;
        const updatedQuizData = {
            title: this.elements.quizTitleInput.val().trim(),
            description: this.elements.quizDescriptionInput.val().trim()
        };

        if (!updatedQuizData.title) {
            toastr.warning('Quiz title cannot be empty.');
            return;
        }
        if (!this.currentQuiz || !this.currentQuiz.quiz_id) {
            toastr.error('Cannot save details: Quiz ID is missing.');
            return;
        }

        QuizService.updateQuiz(this.currentQuiz.quiz_id, updatedQuizData,
            function(response) {
                console.log("QuizManagementController: Response from updateQuiz:", JSON.stringify(response, null, 2));
                if (response && response.success && response.quiz) {
                    self.currentQuiz = response.quiz;
                    toastr.success('Quiz details updated successfully!');
                    self.displayQuizDetails();
                    self.hideEditQuizDetailsForm();
                } else {
                    toastr.error(response.message || 'Failed to update quiz details.');
                }
            },
            function(error) {
                console.error("QuizManagementController: Error updating quiz details:", error);
                toastr.error('An error occurred while updating quiz details.');
            }
        );
    },

    loadQuestionsForCurrentQuiz: function() {
        const self = this;
        console.log("QuizManagementController: loadQuestionsForCurrentQuiz CALLED. Current Quiz ID:", this.currentQuiz ? this.currentQuiz.quiz_id : 'N/A');
        if (!this.currentQuiz || !this.currentQuiz.quiz_id) {
            console.warn("QuizManagementController: No current quiz or quiz_id to load questions for.");
            this.elements.questionList.empty();
            this.elements.noQuestionsMessage.show();
            this.elements.loadingQuestionsMessage.hide();
            return;
        }

        this.elements.loadingQuestionsMessage.show();
        this.elements.noQuestionsMessage.hide();
        this.elements.questionList.empty();

        QuizService.getQuestionsByQuizId(this.currentQuiz.quiz_id,
            function(response) {
                console.log("QuizManagementController: Response from getQuestionsByQuizId:", JSON.stringify(response, null, 2));
                self.elements.loadingQuestionsMessage.hide();
                if (response && response.success && Array.isArray(response.questions)) {
                    self.questions = response.questions;
                    if (self.questions.length > 0) {
                        self.renderQuestionList();
                    } else {
                        console.log("QuizManagementController: No questions found in response for quiz ID:", self.currentQuiz.quiz_id);
                        self.elements.noQuestionsMessage.show();
                    }
                } else {
                    console.warn("QuizManagementController: Failed to load questions or unexpected response format.", response);
                    self.questions = [];
                    self.elements.noQuestionsMessage.show();
                }
            },
            function(error) {
                console.error("QuizManagementController: Error loading questions:", error);
                self.elements.loadingQuestionsMessage.hide();
                toastr.error('Failed to load questions.');
                self.elements.noQuestionsMessage.show();
            }
        );
    },

    renderQuestionList: function() {
        const self = this;
        console.log("QuizManagementController: renderQuestionList CALLED. Questions to render:", this.questions);
        this.elements.questionList.empty();
        if (this.questions.length === 0) {
            this.elements.noQuestionsMessage.show();
            return;
        }
        this.elements.noQuestionsMessage.hide();

        this.questions.forEach((question, index) => {
            const correctAnswerText = question.answer ? 'True' : 'False';
            const questionCard = `
                <div class="p-6 bg-white dark:bg-gray-800 shadow-md rounded-lg question-card mb-4" data-question-id="${question.question_id}">
                    <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">Question ${index + 1}</p>
                    <p class="mt-1 text-lg font-medium text-black">${question.header || 'N/A'}</p>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                        Correct Answer: <span class="font-semibold ${question.answer ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'}">${correctAnswerText}</span>
                    </p>
                    ${question.explanation ? `<p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><span class="font-semibold">Explanation:</span> ${question.explanation}</p>` : ''}
                    <div class="mt-4 flex space-x-3 justify-end">
                        <button class="edit-question-btn px-3 py-1.5 text-sm bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-md shadow-sm" data-question-id="${question.question_id}">Edit</button>
                        <button class="delete-question-btn px-3 py-1.5 text-sm bg-red-600 hover:bg-red-700 text-white font-semibold rounded-md shadow-sm" data-question-id="${question.question_id}">Delete</button>
                    </div>
                </div>
            `;
            self.elements.questionList.append(questionCard);
        });
        console.log("QuizManagementController: Question list rendered.");
    },
    
    showQuestionFormForAdd: function() {
        console.log("QuizManagementController: showQuestionFormForAdd CALLED.");
        this.isEditingQuestion = false;
        this.editingQuestionId = null;
        this.elements.questionForm[0].reset(); 
        this.elements.questionIdInput.val('');
        this.elements.questionFormTitle.text('Add New Question');
        this.elements.questionAnswerFalseRadio.prop('checked', true); 
        this.elements.questionFormSection.slideDown();
    },

    showQuestionFormForEdit: function(questionId) {
        console.log("QuizManagementController: showQuestionFormForEdit CALLED for question ID:", questionId);
        const question = this.questions.find(q => q.question_id == questionId);
        if (!question) {
            toastr.error('Question not found for editing.');
            return;
        }

        this.isEditingQuestion = true;
        this.editingQuestionId = questionId;

        this.elements.questionForm[0].reset();
        this.elements.questionIdInput.val(question.question_id);
        this.elements.questionHeaderInput.val(question.header);
        this.elements.questionExplanationInput.val(question.explanation || '');
        
        if (question.answer === true) { 
            this.elements.questionAnswerTrueRadio.prop('checked', true);
        } else {
            this.elements.questionAnswerFalseRadio.prop('checked', true);
        }
        
        this.elements.questionFormTitle.text('Edit Question');
        this.elements.questionFormSection.slideDown();
        $('html, body').animate({
            scrollTop: this.elements.questionFormSection.offset().top - 20
        }, 500);
    },

    hideQuestionForm: function() {
        console.log("QuizManagementController: hideQuestionForm CALLED.");
        this.elements.questionFormSection.slideUp();
        this.elements.questionForm[0].reset();
        this.isEditingQuestion = false;
        this.editingQuestionId = null;
    },

    handleSaveQuestion: function() {
        console.log("QuizManagementController: handleSaveQuestion CALLED.");
        const self = this;
        const questionData = {
            quiz_id: this.currentQuiz ? this.currentQuiz.quiz_id : null,
            header: this.elements.questionHeaderInput.val().trim(),
            explanation: this.elements.questionExplanationInput.val().trim(),
            answer: this.elements.questionAnswerTrueRadio.is(':checked')
        };

        if (!questionData.quiz_id) {
            toastr.error('Quiz ID is missing. Cannot save question.');
            return;
        }
        if (!questionData.header) {
            toastr.warning('Question text (header) cannot be empty.');
            return;
        }

        const successCb = function(response) {
            console.log("QuizManagementController: Response from save question (add/update):", JSON.stringify(response, null, 2));
            if (response && response.success && response.question) {
                toastr.success(`Question ${self.isEditingQuestion ? 'updated' : 'added'} successfully!`);
                self.hideQuestionForm();
                self.loadQuestionsForCurrentQuiz(); 
            } else {
                toastr.error(response.message || `Failed to ${self.isEditingQuestion ? 'update' : 'add'} question.`);
            }
        };
        const errorCb = function(error) {
            console.error(`QuizManagementController: Error ${self.isEditingQuestion ? 'updating' : 'adding'} question:`, error);
            toastr.error(`An error occurred while ${self.isEditingQuestion ? 'updating' : 'adding'} the question.`);
        };

        if (this.isEditingQuestion && this.editingQuestionId) {
            const updatePayload = { 
                header: questionData.header, 
                explanation: questionData.explanation, 
                answer: questionData.answer 
            };
            console.log("QuizManagementController: Calling QuizService.updateQuestion with ID:", this.editingQuestionId, "Payload:", updatePayload);
            QuizService.updateQuestion(this.editingQuestionId, updatePayload, successCb, errorCb);
        } else {
            console.log("QuizManagementController: Calling QuizService.addQuestionToQuiz with data:", questionData);
            QuizService.addQuestionToQuiz(questionData, successCb, errorCb);
        }
    },

    handleDeleteQuestion: function(questionId) {
        console.log("QuizManagementController: handleDeleteQuestion CALLED for question ID:", questionId);
        const self = this;
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
                console.log("QuizManagementController: Deletion confirmed for question ID:", questionId);
                QuizService.deleteQuestion(questionId,
                    function(response) {
                        console.log("QuizManagementController: Response from deleteQuestion:", JSON.stringify(response, null, 2));
                        if (response && response.success) {
                            toastr.success('Question deleted successfully!');
                            self.loadQuestionsForCurrentQuiz();
                        } else {
                            toastr.error(response.message || 'Failed to delete question.');
                        }
                    },
                    function(error) {
                        console.error("QuizManagementController: Error deleting question:", error);
                        toastr.error('An error occurred while deleting the question.');
                    }
                );
            } else {
                console.log("QuizManagementController: Deletion cancelled for question ID:", questionId);
            }
        });
    }
};
