// File: frontend/services/quiz-service.js

// Asumsikan RestClient.js sudah ada dan di-load, dan menangani base URL API serta header JWT.
// Juga diasumsikan ToastrUtil untuk notifikasi tersedia secara global.

const QuizService = {

    // =======================
    // Metode untuk Kuis (Quiz)
    // =======================

    /*
    Mengambil kuis yang terkait dengan material_id tertentu.
    Backend diharapkan mengembalikan: { success: true, quiz: quizObject | null }
    atau { success: true, data: [quizObject] | [] } jika mengikuti pola lama
     */
    getQuizByMaterialId: function(materialId, successCallback, errorCallback) {
        RestClient.get("quiz/material/" + materialId, null, 
            function(response) { // Penanganan wrapper untuk konsistensi
                if (response && response.success !== undefined) {
                    // Jika backend mengembalikan { success: true, data: [quiz] }
                    // dan kita hanya butuh satu objek kuis atau null
                    if (response.data && Array.isArray(response.data)) {
                        successCallback({ success: true, quiz: response.data.length > 0 ? response.data[0] : null });
                    } 
                    // Jika backend sudah mengembalikan { success: true, quiz: quizObj }
                    else if (response.quiz !== undefined) {
                         successCallback(response);
                    } else {
                        // Fallback jika struktur tidak terduga tapi sukses
                        successCallback({ success: response.success, quiz: null });
                    }
                } else {
                    // Jika respons tidak memiliki properti success, anggap sebagai error atau data tak terduga
                    console.warn("getQuizByMaterialId: Respons tidak terduga", response);
                    errorCallback(response); // atau buat objek error kustom
                }
            }, 
            errorCallback
        );
    },

    /*
    Membuat kuis baru.
    Backend diharapkan mengembalikan: { success: true, message: string, quiz: quizObject }
    atau untuk anotasi lama: { success: true, message: string, quiz_id: number }
     */
    createQuiz: function(quizData, successCallback, errorCallback) {
        RestClient.post("quiz", quizData, successCallback, errorCallback);
    },

    /*
    Memperbarui detail kuis yang sudah ada.
    Backend diharapkan mengembalikan: { success: true, message: string, quiz: quizObject }
     */
    updateQuiz: function(quizId, quizData, successCallback, errorCallback) {
        RestClient.put("quiz/" + quizId, quizData, successCallback, errorCallback);
    },

    /*
    (Opsional) Menghapus kuis.
    Backend diharapkan mengembalikan: { success: true, message: string }
     */
    deleteQuiz: function(quizId, successCallback, errorCallback) {
        RestClient.delete("quiz/" + quizId, successCallback, errorCallback);
    },

    // ====================================
    // Metode untuk Pertanyaan (Question)
    // ====================================

    /*
    Mengambil semua pertanyaan untuk quiz_id tertentu.
    Backend diharapkan mengembalikan: { success: true, questions: arrayOfQuestionObjects }
     */
    getQuestionsByQuizId: function(quizId, successCallback, errorCallback) {
        RestClient.get("question/quiz/" + quizId, null, successCallback, errorCallback);
    },

    /*
    Menambahkan pertanyaan baru (Benar/Salah) ke sebuah kuis.
    Payload yang dikirim adalah: { quiz_id, header, explanation, answer (boolean) }
    Backend diharapkan mengembalikan: { success: true, message: string, question: questionObject }
     */
    addQuestionToQuiz: function(questionData, successCallback, errorCallback) {
        // Memastikan field "answer" ada dan merupakan boolean
        const payload = {
            quiz_id: questionData.quiz_id,
            header: questionData.header,
            explanation: questionData.explanation || null, // Kirim null jika kosong
            answer: typeof questionData.answer === 'boolean' ? questionData.answer : false // Default ke false jika tidak boolean
        };
        RestClient.post("question", payload, successCallback, errorCallback);
    },

    /*
    Memperbarui pertanyaan (Benar/Salah) yang sudah ada.
    Payload yang dikirim adalah: { header, explanation, answer (boolean) }
    Backend diharapkan mengembalikan: { success: true, message: string, question: questionObject }
     */
    updateQuestion: function(questionId, questionData, successCallback, errorCallback) {
         const payload = {
            header: questionData.header,
            explanation: questionData.explanation || null,
            answer: typeof questionData.answer === 'boolean' ? questionData.answer : false
        };
        RestClient.put("question/" + questionId, payload, successCallback, errorCallback);
    },

    /*
    Menghapus pertanyaan.
    Backend diharapkan mengembalikan: { success: true, message: string }
     */
    deleteQuestion: function(questionId, successCallback, errorCallback) {
        RestClient.delete("question/" + questionId, successCallback, errorCallback);
    },

    /*
     Mengambil satu pertanyaan berdasarkan ID.
     Backend diharapkan mengembalikan: { success: true, question: questionObject } atau { success: true, data: questionObject }
     */
    getQuestionById: function(questionId, successCallback, errorCallback) {
        RestClient.get("question/" + questionId, null, 
            function(response) { // Penanganan wrapper untuk konsistensi
                if (response && response.success) {
                    if (response.question) { // Jika backend mengembalikan { success: true, question: ... }
                        successCallback(response);
                    } else if (response.data) { // Jika backend mengembalikan { success: true, data: ... }
                        successCallback({ success: true, question: response.data });
                    } else {
                         console.warn("getQuestionById: Respons sukses tapi data pertanyaan tidak ditemukan", response);
                         successCallback({ success: true, question: null }); // Atau panggil errorCallback
                    }
                } else {
                    console.warn("getQuestionById: Respons tidak sukses atau tidak terduga", response);
                    errorCallback(response);
                }
            },
            errorCallback
        );
    }
};

