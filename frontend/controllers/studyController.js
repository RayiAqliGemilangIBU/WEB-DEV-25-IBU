// File: frontend/controllers/StudyController.js
const StudyController = {
    // State aplikasi umum
    textMaterials: [],
    quizzes: [],
    
    // State spesifik untuk kuis yang sedang berjalan
    quizState: {
        quizId: null,
        quizTitle: '',
        questions: [],
        userAnswers: {}, // Format: { question_id: boolean_jawaban_user }
        currentIndex: 0,
        isSubmitted: false
    },
    
    elements: {
        textMaterialList: null,
        quizList: null,
        materialsLoading: null,
        quizzesLoading: null,
        
        contentLoadField: null,
        studyWelcomeMessage: null,
        textMaterialContentView: null,
        textMaterialTitle: null,
        textMaterialBody: null,
        
        // Elemen UI Kuis
        quizAttemptView: null,
        quizInProgressView: null,
        quizTitleDisplay: null,
        quizProgressIndicator: null,
        quizQuestionText: null,
        quizAnswerTrueBtn: null,
        quizAnswerFalseBtn: null,
        quizNavPrevBtn: null,
        quizNavNextBtn: null,
        quizNavSubmitBtn: null,
        
        // Elemen UI Hasil Kuis
        quizResultsView: null,
        quizResultTitle: null,
        quizScoreDisplay: null,
        quizScorePercentage: null,
        quizAnswersReviewList: null,
        quizBackToStudyBtn: null
    },

    init: function() {
        console.log("StudyController: init() CALLED.");
        this.cacheDOMElements();
        this.bindEvents();
        this.loadInitialLists();
    },

cacheDOMElements: function() {
    this.elements.textMaterialList = $('#text-material-list');
    this.elements.quizList = $('#quiz-list');
    this.elements.materialsLoading = $('#materials-loading');
    this.elements.quizzesLoading = $('#quizzes-loading'); // Pastikan ini juga ada

    this.elements.contentLoadField = $('#content-load-field');
    this.elements.studyWelcomeMessage = $('#study-welcome-message');
    this.elements.textMaterialContentView = $('#text-material-content-view');
    this.elements.textMaterialTitle = $('#text-material-title');
    this.elements.textMaterialBody = $('#text-material-body');
    
    // --- TAMBAHKAN ATAU PASTIKAN BAGIAN INI ADA DAN LENGKAP ---
    this.elements.quizAttemptView = $('#quiz-attempt-view');
    this.elements.quizInProgressView = $('#quiz-in-progress-view');
    this.elements.quizTitleDisplay = $('#quiz-title-display');
    this.elements.quizProgressIndicator = $('#quiz-progress-indicator');
    this.elements.quizQuestionText = $('#quiz-question-text');
    this.elements.quizAnswerTrueBtn = $('#quiz-answer-true-btn');
    this.elements.quizAnswerFalseBtn = $('#quiz-answer-false-btn');
    this.elements.quizNavPrevBtn = $('#quiz-nav-prev-btn');
    this.elements.quizNavNextBtn = $('#quiz-nav-next-btn');
    this.elements.quizNavSubmitBtn = $('#quiz-nav-submit-btn');

    this.elements.quizResultsView = $('#quiz-results-view');
    this.elements.quizResultTitle = $('#quiz-result-title');
    this.elements.quizScoreDisplay = $('#quiz-score-display');
    this.elements.quizScorePercentage = $('#quiz-score-percentage');
    this.elements.quizAnswersReviewList = $('#quiz-answers-review-list');
    this.elements.quizBackToStudyBtn = $('#quiz-back-to-study-btn');
    // --- AKHIR DARI BAGIAN YANG PERLU DITAMBAHKAN ---

    console.log("StudyController: DOM elements cached.");
},

    bindEvents: function() {
        const self = this;
        this.elements.textMaterialList.off('click').on('click', '.text-material-item', function(e) {
            e.preventDefault();
            self.setActiveItem($(this));
            self.loadTextMaterialContent($(this).data('material-id'), $(this).data('material-title'));
        });

        this.elements.quizList.off('click').on('click', '.quiz-item', function(e) {
            e.preventDefault();
            self.setActiveItem($(this));
            self.startQuiz($(this).data('quiz-id'), $(this).find('.font-medium').text().replace(/^\d+\.\s/, ''));
        });

        this.elements.quizAnswerTrueBtn.off('click').on('click', () => self.selectAnswer(true));
        this.elements.quizAnswerFalseBtn.off('click').on('click', () => self.selectAnswer(false));
        
        this.elements.quizNavPrevBtn.off('click').on('click', () => self.navigateQuestion(-1));
        this.elements.quizNavNextBtn.off('click').on('click', () => self.navigateQuestion(1));
        this.elements.quizNavSubmitBtn.off('click').on('click', () => self.submitQuiz());
        this.elements.quizBackToStudyBtn.off('click').on('click', () => self.loadInitialLists());
        console.log("StudyController: Events bound.");
    },

    setActiveItem: function(activeElement) {
        this.elements.textMaterialList.find('a').removeClass('bg-indigo-100 font-bold');
        this.elements.quizList.find('a').removeClass('bg-indigo-100 font-bold');
        activeElement.addClass('bg-indigo-100 font-bold');
    },

    showContent: function(viewToShow) {
        this.elements.studyWelcomeMessage.hide();
        this.elements.textMaterialContentView.hide();
        this.elements.quizAttemptView.hide();
        viewToShow.show();
    },

    loadInitialLists: function() {
        // ... (fungsi ini tetap sama seperti sebelumnya)
        console.log("StudyController: loadInitialLists CALLED.");
        this.showContent(this.elements.studyWelcomeMessage);
        this.loadAllTextMaterials();
        this.loadAllQuizzes();
    },

    // Mengambil dan menampilkan daftar materi teks
    loadAllTextMaterials: function() {
        const self = this;
        this.elements.materialsLoading.show();

        // Asumsi ada TextMaterialService.getAllTextMaterials()
        TextMaterialService.getAllTextMaterials(
            function(response) {
                console.log("StudyController: Received text materials", response);
                self.elements.materialsLoading.hide();
                if (response && response.success && Array.isArray(response.data)) {
                    self.textMaterials = response.data;
                    self.renderMaterialList();
                } else {
                    self.elements.textMaterialList.html('<p class="p-4 text-sm text-red-500">Failed to load materials.</p>');
                }
            },
            function(error) {
                console.error("StudyController: Error loading text materials", error);
                self.elements.materialsLoading.hide();
                self.elements.textMaterialList.html('<p class="p-4 text-sm text-red-500">Error: Could not fetch materials.</p>');
            }
        );
    },

    // Mengambil dan menampilkan daftar kuis
    loadAllQuizzes: function() {
        const self = this;
        this.elements.quizzesLoading.show();

        // Asumsi ada QuizService.getAllQuizzes()
        QuizService.getAllQuizzes(
            function(response) {
                console.log("StudyController: Received quizzes", response);
                self.elements.quizzesLoading.hide();
                if (response && response.success && Array.isArray(response.data)) {
                    self.quizzes = response.data;
                    self.renderQuizList();
                } else {
                    self.elements.quizList.html('<p class="p-4 text-sm text-red-500">Failed to load quizzes.</p>');
                }
            },
            function(error) {
                console.error("StudyController: Error loading quizzes", error);
                self.elements.quizzesLoading.hide();
                self.elements.quizList.html('<p class="p-4 text-sm text-red-500">Error: Could not fetch quizzes.</p>');
            }
        );
    },

    // Merender daftar materi di sidebar
    renderMaterialList: function() {
        this.elements.textMaterialList.empty();
        if (this.textMaterials.length === 0) {
            this.elements.textMaterialList.html('<p class="p-4 text-sm text-gray-500">No materials available.</p>');
            return;
        }

        this.textMaterials.forEach((material, index) => {
            // ID dari text_materials adalah 'text_id'
            const itemHtml = `
                <a href="#" class="text-material-item block w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-indigo-100 dark:hover:bg-indigo-700 hover:text-indigo-700 dark:hover:text-white transition-colors duration-150" 
                   data-material-id="${material.text_id}" data-material-title="${material.title}">
                    <span class="font-medium">${index + 1}. ${material.title}</span>
                </a>
            `;
            this.elements.textMaterialList.append(itemHtml);
        });
    },

    // Merender daftar kuis di sidebar
    renderQuizList: function() {
        this.elements.quizList.empty();
        if (this.quizzes.length === 0) {
            this.elements.quizList.html('<p class="p-4 text-sm text-gray-500">No quizzes available.</p>');
            return;
        }

        this.quizzes.forEach((quiz, index) => {
            const itemHtml = `
                 <a href="#" class="quiz-item block w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-indigo-100 dark:hover:bg-indigo-700 hover:text-indigo-700 dark:hover:text-white transition-colors duration-150" 
                    data-quiz-id="${quiz.quiz_id}">
                    <span class="font-medium">${index + 1}. ${quiz.title}</span>
                    <span class="block text-xs text-gray-500 dark:text-gray-400 mt-1">${quiz.description || ''}</span>
                </a>
            `;
            this.elements.quizList.append(itemHtml);
        });
    },

    // Memuat konten materi teks yang dipilih
    loadTextMaterialContent: function(materialId, materialTitle) {
        const self = this;
        console.log("StudyController: Loading content for text material ID:", materialId);
        
        this.showContent(this.elements.textMaterialContentView);
        this.elements.textMaterialTitle.text('Loading content...');
        this.elements.textMaterialBody.html('<p class="text-gray-500">Please wait...</p>');

        // Panggil service seperti biasa
        TextMaterialService.getTextMaterialById(materialId, 
            function(response) {
                // Tambahkan log untuk melihat respons mentah
                console.log("StudyController: Raw response from getTextMaterialById:", response);

                // --- PERUBAHAN LOGIKA IF DI SINI ---
                // Cek jika respons adalah objek yang valid dan memiliki properti yang kita harapkan, 
                // seperti 'text_id' dan 'content'.
                if (response && response.text_id && typeof response.content !== 'undefined') {
                    // Karena respons adalah objek langsung, kita bisa langsung menggunakannya.
                    const material = response; 
                    
                    self.elements.textMaterialTitle.text(material.title || materialTitle);
                    
                    // Ganti baris baru (\n) dengan tag <br> untuk tampilan HTML
                    const formattedContent = material.content.replace(/\n/g, '<br />');
                    self.elements.textMaterialBody.html(formattedContent);

                } else {
                     // Blok ini akan dijalankan jika respons kosong atau tidak memiliki format yang diharapkan
                     self.elements.textMaterialTitle.text('Error');
                     self.elements.textMaterialBody.html('<p class="text-red-500">Could not load material content. The response from the server might be empty or in an unexpected format.</p>');
                     toastr.error((response && response.message) || "Failed to load material content.");
                }
            },
            function(error) {
                console.error("StudyController: Error loading text material content", error);
                self.elements.textMaterialTitle.text('Error');
                self.elements.textMaterialBody.html('<p class="text-red-500">An error occurred while fetching the material.</p>');
                toastr.error("An error occurred while fetching the material.");
            }
        );
    },

    //Quiz interface
    startQuiz: function(quizId, quizTitle) {
        console.log(`StudyController: Starting quiz ID: ${quizId}, Title: ${quizTitle}`);
        const self = this;
        this.showContent(this.elements.quizAttemptView);
        this.elements.quizInProgressView.show();
        this.elements.quizResultsView.hide();
        this.elements.quizQuestionText.text('Loading questions, please wait...');
        this.elements.quizNavSubmitBtn.show(); // Pastikan tombol submit terlihat
        
        this.quizState = {
            quizId: quizId,
            quizTitle: quizTitle,
            questions: [],
            userAnswers: {},
            currentIndex: 0,
            isSubmitted: false
        };

        this.elements.quizTitleDisplay.text(quizTitle);

        QuizService.getQuestionsByQuizId(quizId, 
            function(response) {
                if (response && response.success && Array.isArray(response.questions) && response.questions.length > 0) {
                    self.quizState.questions = response.questions;
                    self.renderCurrentQuestion();
                } else {
                    self.elements.quizQuestionText.text('Could not load questions for this quiz or this quiz has no questions.');
                    self.elements.quizNavPrevBtn.hide();
                    self.elements.quizNavNextBtn.hide();
                    self.elements.quizNavSubmitBtn.hide();
                    toastr.error('Failed to load questions.');
                }
            },
            function(error) {
                console.error("StudyController: Error loading questions for quiz", error);
                self.elements.quizQuestionText.text('An error occurred while loading questions.');
                toastr.error('An error occurred while loading questions.');
            }
        );
    },

    renderCurrentQuestion: function() {
        const question = this.quizState.questions[this.quizState.currentIndex];
        if (!question) return;

        console.log(`Rendering question index ${this.quizState.currentIndex}:`, question);

        this.elements.quizProgressIndicator.text(`Question ${this.quizState.currentIndex + 1} of ${this.quizState.questions.length}`);
        this.elements.quizQuestionText.text(question.header);

        this.updateAnswerButtons(question.question_id);
        
        // --- LOGIKA BARU UNTUK VISIBILITAS TOMBOL NAVIGASI ---
        const isFirstQuestion = this.quizState.currentIndex === 0;
        const isLastQuestion = this.quizState.currentIndex === this.quizState.questions.length - 1;

        if (isFirstQuestion) {
            this.elements.quizNavPrevBtn.hide();
        } else {
            this.elements.quizNavPrevBtn.show();
        }

        if (isLastQuestion) {
            this.elements.quizNavNextBtn.hide();
        } else {
            this.elements.quizNavNextBtn.show();
        }
    },

    updateAnswerButtons: function(questionId) {
        this.elements.quizAnswerTrueBtn.removeClass('ring-4 ring-offset-2 ring-yellow-400');
        this.elements.quizAnswerFalseBtn.removeClass('ring-4 ring-offset-2 ring-yellow-400');

        const userAnswer = this.quizState.userAnswers[questionId];
        if (userAnswer === true) {
            this.elements.quizAnswerTrueBtn.addClass('ring-4 ring-offset-2 ring-yellow-400');
        } else if (userAnswer === false) {
            this.elements.quizAnswerFalseBtn.addClass('ring-4 ring-offset-2 ring-yellow-400');
        }
    },

    selectAnswer: function(answer) {
        if (this.quizState.isSubmitted) return; 
        const currentQuestion = this.quizState.questions[this.quizState.currentIndex];
        if (!currentQuestion) return;

        // Simpan jawaban
        this.quizState.userAnswers[currentQuestion.question_id] = answer;
        console.log("User answers:", this.quizState.userAnswers);
        this.updateAnswerButtons(currentQuestion.question_id); // Update UI tombol jawaban

        // --- LOGIKA BARU UNTUK AUTO-ADVANCE ---
        const self = this;
        const isLastQuestion = this.quizState.currentIndex === this.quizState.questions.length - 1;

        // Beri sedikit jeda agar pengguna bisa melihat pilihannya
        setTimeout(function() {
            if (!isLastQuestion) {
                self.navigateQuestion(1); // Otomatis pindah ke pertanyaan berikutnya
            } else {
                // Di pertanyaan terakhir, jangan auto-advance. Biarkan pengguna menekan Submit.
                console.log("Last question answered. User can now submit.");
                // Mungkin bisa berikan efek visual pada tombol Submit
                self.elements.quizNavSubmitBtn.addClass('animate-pulse');
            }
        }, 300); // Jeda 300 milidetik
    },

    navigateQuestion: function(direction) {
        const newIndex = this.quizState.currentIndex + direction;
        if (newIndex >= 0 && newIndex < this.quizState.questions.length) {
            this.quizState.currentIndex = newIndex;
            // Hapus efek visual dari tombol submit jika berpindah soal
            this.elements.quizNavSubmitBtn.removeClass('animate-pulse');
            this.renderCurrentQuestion();
        }
    },

    submitQuiz: function() {
        const totalQuestions = this.quizState.questions.length;
        const totalAnswered = Object.keys(this.quizState.userAnswers).length;

        if (totalAnswered < totalQuestions) {
            Swal.fire({
                title: 'Are you sure?',
                text: `You have not answered ${totalQuestions - totalAnswered} question(s). Do you still want to submit?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, submit it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.calculateAndShowResults();
                }
            });
        } else {
            this.calculateAndShowResults();
        }
    },

    calculateAndShowResults: function() {
        console.log("Calculating results...");
        this.quizState.isSubmitted = true;
        let score = 0;
        
        this.quizState.questions.forEach(question => {
            const userAnswer = this.quizState.userAnswers[question.question_id];
            const correctAnswer = question.answer; // 'answer' dari backend adalah boolean
            if (userAnswer === correctAnswer) {
                score++;
            }
        });

        this.displayQuizResults(score, this.quizState.questions.length);
    },

    displayQuizResults: function(score, totalQuestions) {
        console.log(`Displaying results: ${score} / ${totalQuestions}`);
        this.elements.quizInProgressView.hide();
        this.elements.quizResultsView.show();

        const percentage = totalQuestions > 0 ? ((score / totalQuestions) * 100).toFixed(0) : 0;

        this.elements.quizResultTitle.text(`Results for: ${this.quizState.quizTitle}`);
        this.elements.quizScoreDisplay.text(`${score}/${totalQuestions}`);
        this.elements.quizScorePercentage.text(`(${percentage}%)`);

        // Render review jawaban
        this.elements.quizAnswersReviewList.empty();
        this.quizState.questions.forEach((question, index) => {
            const userAnswer = this.quizState.userAnswers[question.question_id];
            const correctAnswer = question.answer;
            const isCorrect = userAnswer === correctAnswer;
            
            let userAnswerText = 'Not Answered';
            if (userAnswer === true) userAnswerText = 'True';
            if (userAnswer === false) userAnswerText = 'False';

            const reviewHtml = `
                <div class="p-4 rounded-lg ${isCorrect ? 'bg-green-50' : 'bg-red-50'}">
                    <p class="font-semibold text-gray-800">${index + 1}. ${question.header}</p>
                    <p class="text-sm mt-2">
                        Your Answer: <span class="font-bold ${isCorrect ? 'text-green-700' : 'text-red-700'}">${userAnswerText}</span>
                        ${!isCorrect ? `<span class="text-green-700 ml-2">(Correct Answer: ${correctAnswer ? 'True' : 'False'})</span>` : ''}
                    </p>
                    ${question.explanation ? `<p class="text-sm mt-1 text-gray-600"><em>Explanation:</em> ${question.explanation}</p>` : ''}
                </div>
            `;
            this.elements.quizAnswersReviewList.append(reviewHtml);
        });
    }
};
