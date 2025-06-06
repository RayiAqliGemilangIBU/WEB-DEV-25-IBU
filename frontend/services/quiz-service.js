// File: frontend/services/quiz-service.js

// Asumsikan RestClient.js sudah ada dan di-load, dan menangani base URL API serta header JWT.
// Juga diasumsikan toastr untuk notifikasi tersedia secara global.

const QuizService = {

    // =======================
    // Metode untuk Kuis (Quiz)
    // =======================

    getQuizByMaterialId: function(materialId, successCallbackFromController, errorCallbackFromController) {
        const apiUrl = "quiz/material/" + materialId;
        console.log("QuizService: Attempting to call RestClient.get for URL:", apiUrl);

        const internalSuccessCallback = function(response) { 
            console.log("QuizService (getQuizByMaterialId): SUCCESS CALLBACK from RestClient.get ENTERED. URL:", apiUrl, "Raw Response:", JSON.stringify(response, null, 2));

            if (response && response.success !== undefined) {
                console.log("QuizService (getQuizByMaterialId wrapper): Response has 'success' property.");
                let quizObject = null;
                if (response.data && Array.isArray(response.data)) { 
                    console.log("QuizService (getQuizByMaterialId wrapper): Response has 'data' as array. Processing...");
                    quizObject = response.data.length > 0 ? response.data[0] : null;
                    if (typeof successCallbackFromController === 'function') {
                        successCallbackFromController({ success: response.success, quiz: quizObject });
                    }
                } else if (response.quiz !== undefined) { 
                    console.log("QuizService (getQuizByMaterialId wrapper): Response has 'quiz' object. Calling successCallback from controller.");
                    quizObject = response.quiz;
                    if (typeof successCallbackFromController === 'function') {
                        // Backend sudah mengembalikan format { success: true, quiz: quizObject }
                        // jadi kita bisa langsung meneruskannya.
                        successCallbackFromController(response); 
                    }
                } else { 
                    console.log("QuizService (getQuizByMaterialId wrapper): Response 'success', but no 'quiz' or 'data' array. Defaulting quiz to null.");
                    if (typeof successCallbackFromController === 'function') {
                        successCallbackFromController({ success: response.success, quiz: null });
                    }
                }
            } else {
                console.warn("QuizService (getQuizByMaterialId wrapper): Unexpected response format or 'success' is undefined.", response);
                if (typeof errorCallbackFromController === 'function') {
                    errorCallbackFromController(response); 
                }
            }
            console.log("QuizService (getQuizByMaterialId): SUCCESS CALLBACK from RestClient.get EXITED.");
        };

        const internalErrorCallback = function(error) {
            console.error("QuizService (getQuizByMaterialId): ERROR CALLBACK from RestClient.get ENTERED. URL:", apiUrl, "Error:", JSON.stringify(error, null, 2));
            if (typeof errorCallbackFromController === 'function') {
                errorCallbackFromController(error); 
            }
        };

        RestClient.get(apiUrl, internalSuccessCallback, internalErrorCallback); 
    },

    createQuiz: function(quizData, successCallback, errorCallback) {
        console.log("QuizService: createQuiz called with data:", quizData);
        RestClient.post("quiz", quizData, successCallback, errorCallback);
    },

    updateQuiz: function(quizId, quizData, successCallback, errorCallback) {
        console.log("QuizService: updateQuiz called for ID:", quizId, "with data:", quizData);
        RestClient.put("quiz/" + quizId, quizData, successCallback, errorCallback);
    },

    deleteQuiz: function(quizId, successCallback, errorCallback) {
        console.log("QuizService: deleteQuiz called for ID:", quizId);
        RestClient.delete("quiz/" + quizId, successCallback, errorCallback);
    },

    // ====================================
    // Metode untuk Pertanyaan (Question)
    // ====================================

    getQuestionsByQuizId: function(quizId, successCallbackFromController, errorCallbackFromController) {
        const apiUrl = "question/quiz/" + quizId;
        console.log("QuizService: Attempting to call RestClient.get for URL:", apiUrl);

        const internalSuccessCallback = function(response) {
            console.log("QuizService (getQuestionsByQuizId): SUCCESS CALLBACK from RestClient.get ENTERED. URL:", apiUrl, "Raw Response:", JSON.stringify(response, null, 2));
            if (response && response.success && Array.isArray(response.questions)) {
                console.log("QuizService (getQuestionsByQuizId): Questions array found. Calling successCallback from controller.");
                if (typeof successCallbackFromController === 'function') {
                    successCallbackFromController(response); 
                }
            } else {
                console.warn("QuizService (getQuestionsByQuizId): Unexpected response format or no questions array.", response);
                if (typeof errorCallbackFromController === 'function') {
                    errorCallbackFromController(response.message || "Failed to parse questions or no questions found.");
                }
            }
            console.log("QuizService (getQuestionsByQuizId): SUCCESS CALLBACK from RestClient.get EXITED.");
        };

        const internalErrorCallback = function(error) {
            console.error("QuizService (getQuestionsByQuizId): ERROR CALLBACK from RestClient.get ENTERED. URL:", apiUrl, "Error:", JSON.stringify(error, null, 2));
            if (typeof errorCallbackFromController === 'function') {
                errorCallbackFromController(error);
            }
        };
        
        // --- BARIS YANG DIPERBAIKI/DITAMBAHKAN ---
        RestClient.get(apiUrl, internalSuccessCallback, internalErrorCallback);
    },

    addQuestionToQuiz: function(questionData, successCallback, errorCallback) {
        console.log("QuizService: addQuestionToQuiz called with data:", questionData);
        const payload = {
            quiz_id: questionData.quiz_id,
            header: questionData.header,
            explanation: questionData.explanation || null, 
            answer: typeof questionData.answer === 'boolean' ? questionData.answer : false 
        };
        RestClient.post("question", payload, successCallback, errorCallback);
    },
    
    updateQuestion: function(questionId, questionData, successCallback, errorCallback) {
        console.log("QuizService: updateQuestion called for ID:", questionId, "with data:", questionData);
        const payload = {
            header: questionData.header,
            explanation: questionData.explanation || null,
            answer: typeof questionData.answer === 'boolean' ? questionData.answer : false
        };
        RestClient.put("question/" + questionId, payload, successCallback, errorCallback);
    },

    deleteQuestion: function(questionId, successCallback, errorCallback) {
        console.log("QuizService: deleteQuestion called for ID:", questionId);
        RestClient.delete("question/" + questionId, successCallback, errorCallback);
    },

    getQuestionById: function(questionId, successCallbackFromController, errorCallbackFromController) { // Ganti nama parameter
        const apiUrl = "question/" + questionId;
        console.log("QuizService: Attempting to call RestClient.get for URL:", apiUrl);

        const internalSuccessCallback = function(response) {
            console.log("QuizService (getQuestionById): SUCCESS CALLBACK from RestClient.get ENTERED. URL:", apiUrl, "Raw Response:", JSON.stringify(response, null, 2));
            if (response && response.success) {
                let questionObject = null;
                if (response.question) { 
                    questionObject = response.question;
                     if (typeof successCallbackFromController === 'function') {
                        successCallbackFromController({success: true, question: questionObject});
                    }
                } else if (response.data) { 
                    questionObject = response.data;
                    if (typeof successCallbackFromController === 'function') {
                        successCallbackFromController({ success: true, question: questionObject });
                    }
                } else {
                    console.warn("QuizService (getQuestionById): Respons sukses tapi data pertanyaan tidak ditemukan dalam format 'question' atau 'data'", response);
                    if (typeof successCallbackFromController === 'function') {
                        successCallbackFromController({ success: true, question: null });
                    }
                }
            } else {
                console.warn("QuizService (getQuestionById): Respons tidak sukses atau tidak terduga", response);
                if (typeof errorCallbackFromController === 'function') {
                    errorCallbackFromController(response);
                }
            }
            console.log("QuizService (getQuestionById): SUCCESS CALLBACK from RestClient.get EXITED.");
        };

        

        const internalErrorCallback = function(error) {
            console.error("QuizService (getQuestionById): ERROR CALLBACK from RestClient.get ENTERED. URL:", apiUrl, "Error:", JSON.stringify(error, null, 2));
            if (typeof errorCallbackFromController === 'function') {
                errorCallbackFromController(error);
            }
        };
        
        // --- BARIS YANG DIPERBAIKI ---
        RestClient.get(apiUrl, internalSuccessCallback, internalErrorCallback);
    },

    getAllQuizzes: function(successCallback, errorCallback) {
        // Panggil RestClient.get dengan DUA argumen
        RestClient.get("quiz", successCallback, errorCallback);
    }
};
