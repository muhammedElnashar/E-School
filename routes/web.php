<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DigitalAssetController;
use App\Http\Controllers\Admin\EducationStageController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PurchasesController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SubjectController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::get('/', function () {
    return redirect('/admin');
});

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/subjects/stages-management', [SubjectController::class, 'stagesManagement'])->name('subjects.stages.management');
    Route::get('/subjects/{subject}/stages', [SubjectController::class, 'getStages']);
    Route::post('/subjects/{subject}/stages/sync', [SubjectController::class, 'syncStages']);
    Route::resource('subjects', SubjectController::class);
    Route::resource('education/stages', EducationStageController::class);
    Route::resource('teacher', \App\Http\Controllers\Admin\TeacherController::class);
    Route::resource('admin', \App\Http\Controllers\Admin\AdminController::class);
    Route::resource('announcements', \App\Http\Controllers\Admin\AnnouncementController::class);
    Route::resource('marketplace-items/package', PackageController::class);
    Route::get('/subjects/{subject}/related-stages', [SubjectController::class, 'getRelatedStages']);
    Route::resource('marketplace-items/digital-assets', DigitalAssetController::class);
    Route::resource('purchases',PurchasesController::class);
    Route::resource('lessons',\App\Http\Controllers\Admin\LessonController::class);
    Route::get('/lessons/{lesson}/occurrences', [\App\Http\Controllers\Admin\LessonController::class, 'occurrenceLesson'])->name('lessons.occurrences');
    Route::get('/teachers/{teacher}/subjects', [\App\Http\Controllers\Admin\LessonController::class, 'getSubjects']);

    /*Route::get('/purchases', [PurchasesController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/create', [PurchasesController::class, 'create'])->name('purchases.create');
    Route::post('/purchases', [PurchasesController::class, 'store'])->name('purchases.store');
    Route::put('/purchases/{purchase}', [PurchasesController::class, 'update'])->name('purchases.update');
    Route::delete('/purchases/{purchase}', [PurchasesController::class, 'destroy'])->name('purchases.destroy');*/
    Route::get("all-assignment", [App\Http\Controllers\Admin\AssignmentsController::class, 'allAssignment'])->name('assignment.all');
    Route::get("all-submission/{id}", [App\Http\Controllers\Admin\AssignmentsController::class, 'getAllSubmissionsForAssignments'])->name('submission.all');
    Route::delete("destroy-assignment/{id}", [App\Http\Controllers\Admin\AssignmentsController::class, 'destroyAssignment'])->name('assignment.destroy');
    Route::delete("destroy-submission/{id}", [App\Http\Controllers\Admin\AssignmentsController::class, 'destroySubmission'])->name('submission.destroy');
    Route::resource('settings', SettingController::class);
    Route::get('chat',function (){
        $users = \App\Models\User::where('id', '!=', Auth::id())->get();
       return view('admin.chat.admin-chat',compact('users'));
    });

});

Route::get('set-language/{locale}', function ($locale) {
    $availableLocales = ['en', 'ar']; // اللغات المتوفرة

    if (in_array($locale, $availableLocales)) {
        Session::put('locale', $locale);
    }

    return redirect()->back();
});


/*Route::get('/login', function () {
        return view('auth.login');
    })->name('login');*/

/*Route::group(['middleware' => ['Role', 'auth']], function () {
    Route::group(['middleware' => 'language'], function () {
        // Route::get('/home', [HomeController::class, 'index']);
        Route::get('home', [HomeController::class, 'index'])->name('home');
        Route::get('/logout', [HomeController::class, 'logout'])->name('logout');
        Route::get('subject-by-class-section', [HomeController::class, 'getSubjectByClassSection'])->name('class-section.by.subject');
        Route::get('teacher-by-class-subject', [HomeController::class, 'getTeacherByClassSubject'])->name('teacher.by.class.subject');
        ///new reset password controller
        Route::get('home/reset_password', [HomeController::class, 'resetPasswordView']);

        Route::get('roles-list/{id}', [RoleController::class, 'showList'])->name('roles-list');
        Route::resource('roles', RoleController::class);
        Route::resource('users', UserController::class);

        Route::get('settings', [SettingController::class, 'index']);
        Route::post('settings', [SettingController::class, 'update']);

        Route::get('fcm-settings', [SettingController::class, 'fcm_index']);
        Route::post('fcm-settings', [SettingController::class, 'fcm_update']);

        Route::resource('medium', MediumController::class);
        Route::get('medium_list', [MediumController::class, 'show']);

        Route::resource('teachers', TeacherController::class);
        Route::get('teacher_details',[TeacherController::class,'teacherListIndex'])->name('teacher.details');
        Route::get('teacher_list', [TeacherController::class, 'show']);


        Route::resource('section', SectionController::class);
        Route::get('section_list', [SectionController::class, 'show']);

        Route::get('class/subject', [ClassSchoolController::class, 'subject'])->name('class.subject');
        Route::post('class/subject', [ClassSchoolController::class, 'update_subjects'])->name('class.subject.update');
        Route::delete('class/subject/{class_subject_id}', [ClassSchoolController::class, 'subject_destroy'])->name('class.subject.delete');
        Route::delete('class/subject-group/{group_id}', [ClassSchoolController::class, 'subject_group_destroy'])->name('class.subject-group.delete');
        Route::get('class/subject/list', [ClassSchoolController::class, 'subject_list'])->name('class.subject.list');
        Route::get('class-list', [ClassSchoolController::class, 'show']);
        Route::resource('class', ClassSchoolController::class);

        Route::get('class-subject-list/{medium_id}', [ClassSchoolController::class, 'getSubjectsByMediumId']);

        Route::get('assign/class/teacher', [ClassTeacherController::class, 'teacher'])->name('class.teacher');
        Route::post('class/teacher/store', [ClassTeacherController::class, 'assign_teacher'])->name('class.teacher.store');
        Route::get('class-teacher-list', [ClassTeacherController::class, 'show']);
        Route::post('remove-class-teacher/{id}/{class_teacher_id}', [ClassTeacherController::class, 'removeClassTeacher']);

        Route::resource('subject', SubjectController::class);
        Route::get('subject-list', [SubjectController::class, 'show']);

        Route::get('/parent/search', [ParentsController::class, 'search']);
        Route::resource('parents', ParentsController::class);
        Route::get('parents_list', [ParentsController::class, 'show']);

        Route::resource('session-years', SessionYearController::class);
        Route::get('session_years_list', [SessionYearController::class, 'show']);
        Route::delete('remove-installment-data/{id}',[SessionYearController::class, 'deleteInstallmentData']);

        Route::get('students-list', [StudentController::class, 'show'])->name('students.list');
        Route::get('students/assign-class', [StudentController::class, 'assignClass'])->name('students.assign-class');
        Route::post('students/assign-class', [StudentController::class, 'assignClass_store'])->name('students.assign-class.store');
        Route::get('students/new-student-list', [StudentController::class, 'newStudentList'])->name('students.new-student-list');
        Route::get('students/create_bulk', [StudentController::class, 'createBulkData'])->name('students.create-bulk-data');
        Route::post('students/store_bulk', [StudentController::class, 'storeBulkData'])->name('students.store-bulk-data');
        Route::resource('students', StudentController::class);

        //student generate roll number
        Route::get('student/assign-roll-number',[StudentController::class, 'indexStudentRollNumber'])->name('students.index-students-roll-number');
        Route::get('student/list-assign-roll-number',[StudentController::class, 'listStudentRollNumber'])->name('students.list-students-roll-number');
        Route::post('student/store-roll-number',[StudentController::class, 'storeStudentRollNumber'])->name('students.store-roll-number');

        Route::resource('category', CategoryController::class);
        Route::get('category_list', [CategoryController::class, 'show']);

        Route::resource('subject-teachers', SubjectTeacherController::class);
        Route::get('subject-teachers-list', [SubjectTeacherController::class, 'show']);

        Route::resource('timetable', TimetableController::class);
        Route::get('timetable-list', [TimetableController::class, 'show']);
        Route::get('checkTimetable', [TimetableController::class, 'checkTimetable']);

        Route::get('get-subject-by-class-section', [TimetableController::class, 'getSubjectByClassSection']);
        Route::get('getteacherbysubject', [TimetableController::class, 'getteacherbysubject']);

        Route::get('gettimetablebyclass', [TimetableController::class, 'gettimetablebyclass'])->name('get.timetable.class');
        Route::get('gettimetablebyteacher', [TimetableController::class, 'gettimetablebyteacher']);
        Route::get('get-timetable-by-subject-teacher-class', [TimetableController::class, 'getTimetableBySubjectTeacherClass']);

        Route::get('class-timetable', [TimetableController::class, 'class_timetable']);
        Route::get('teacher-timetable', [TimetableController::class, 'teacher_timetable']);
        Route::get('get-timetable-data/{id}', [TimetableController::class, 'getTimeatableDetails']);
        Route::post('link-url-update',[TimetableController::class, 'linkUpdate']);

        Route::resource('attendance', AttendanceController::class);
        Route::get('view-attendance', [AttendanceController::class, 'view'])->name("attendance.view");
        Route::get('student-attendance-list', [AttendanceController::class, 'attendance_show']);
        Route::get('getAttendanceData', [AttendanceController::class, 'getAttendanceData']);
        Route::get('student-list', [AttendanceController::class, 'show']);
        Route::get('add-bulk-attendance', [AttendanceController::class, 'createBulkData'])->name('attendance.add-bulk-data');
        Route::post('attendance/store_bulk',[AttendanceController::class,'storeBulkData'])->name('attendance.store-bulk-data');
        Route::post('student/export',[AttendanceController::class,'studentExport'])->name('student-export');

        Route::resource('lesson', LessonController::class);
        Route::get('search-lesson', [LessonController::class, 'search']);
        Route::delete('file/delete/{id}', [LessonController::class, 'deleteFile'])->name('file.delete');
        Route::resource('lesson-topic', LessonTopicController::class);

        Route::resource('announcement', AnnouncementController::class);
        Route::get('announcement-list', [AnnouncementController::class, 'show']);
        Route::get('getAssignData', [AnnouncementController::class, 'getAssignData']);

        Route::resource('holiday', HolidayController::class);
        Route::get('holiday-list', [HolidayController::class, 'show']);
        Route::get('holiday-view', [HolidayController::class, 'holiday_view']);

        Route::resource('assignment', AssignmentController::class);
        Route::get('assignment-submission', [AssignmentController::class, 'viewAssignmentSubmission'])->name('assignment.submission');
        Route::put('assignment-submission/{id}', [AssignmentController::class, 'updateAssignmentSubmission'])->name('assignment.submission.update');
        Route::get('assignment-submission-list', [AssignmentController::class, 'assignmentSubmissionList'])->name('assignment.submission.list');

        Route::resource('sliders', SliderController::class);

        Route::get('exams/exam-result', [ExamController::class, 'getExamResultIndex'])->name('exams.get-result');
        Route::get('exams/show-result', [ExamController::class, 'showExamResult'])->name('exams.show-result');
        Route::post('exams/update-result-marks', [ExamController::class, 'updateExamResultMarks'])->name('exams.update-result-marks');

        Route::post('exams/submit-marks', [ExamController::class, 'submitMarks'])->name('exams.submit-marks');

        Route::get('exams/upload-marks', [ExamController::class, 'uploadMarks'])->name('exams.upload-marks');
        Route::get('exams/marks-list', [ExamController::class, 'marksList'])->name('exams.marks-list');

        Route::get('exams/get-exams/{class_id}', [ExamController::class, 'getExamByClass'])->name('exams.classes');
        Route::delete('/delete-exam-class/{exam_id}/{class_id}', [ExamController::class, 'deleteExamClass']);
        Route::get('exams/get-subjects/{class_id}/{exam_id}', [ExamController::class, 'getSubjectByExam'])->name('exams.subject');
        Route::post('exams/publish/{id}', [ExamController::class, 'publishExamResult'])->name('exams.publish');
        Route::get('exams/get-publish-exam/{class_id}',[ExamController::class,'getPublishExam'])->name('exam.publish.list');
        Route::resource('exams', ExamController::class);

        Route::post('exams/update-timetable', [ExamTimetableController::class, 'updateTimetable'])->name('exams.update-timetable');
        Route::delete('exams/delete-timetable/{id}', [ExamTimetableController::class, 'deleteTimetable'])->name('exams.delete-timetable');
        Route::get('grades', [ExamController::class, 'indexGrades'])->name('grades');

        Route::get('exams/get-exam-subjects/{exam_id}', [ExamController::class, 'getExamSubjects'])->name('exams.subjects');

        Route::post('create-grades', [ExamController::class, 'createGrades'])->name('create-grades');
        Route::get('show-grades', [ExamController::class, 'showGrades'])->name('show-grades');
        Route::put('update-grades/{grade_id}', [ExamController::class, 'updateGrades'])->name('update-grades');
        Route::delete('destroy-grades/{grade_id}', [ExamController::class, 'destroyGrades'])->name('destroy-grades');

        Route::resource('exam-timetable', ExamTimetableController::class);
        Route::get('exam/get-classes/{exam_id}', [ExamTimetableController::class, 'getClassesByExam'])->name('exams.classes');
        Route::get('exam/get-subjects/{class_id}', [ExamTimetableController::class, 'getSubjectsByClass'])->name('exams.class-subjects');

        Route::get('email-settings', [SettingController::class, 'email_index'])->name('setting.email-config-index');
        Route::post('email-settings', [SettingController::class, 'email_update']);
        Route::post('verify-email-settings', [SettingController::class, 'verifyEmailConfigration'])->name('setting.varify-email-config');

        Route::get('privacy-policy/index', [SettingController::class, 'privacy_policy_index'])->name('privacy.index');
        Route::get('terms-condition', [SettingController::class, 'terms_condition_index']);
        Route::get('contact-us', [SettingController::class, 'contact_us_index']);
        Route::get('about-us', [SettingController::class, 'about_us_index']);

        Route::post('setting-update', [SettingController::class, 'setting_page_update']);
        Route::post('notification-setting',[SettingController::class, 'notification_setting']);

        Route::get('reset-password', function () {
            return view('students.reset_password');
        })->name('students.reset_password');
        Route::get('reset-password-list', [StudentController::class, 'reset_password']);
        Route::post('student-change-password', [StudentController::class, 'change_password']);

        Route::resource('promote-student', StudentSessionController::class);
        Route::get('getPromoteData', [StudentSessionController::class, 'getPromoteData']);
        Route::get('promote-student-list', [StudentSessionController::class, 'show']);

        Route::get('resetpassword', [HomeController::class, 'resetpassword'])->name('resetpassword');
        Route::get('checkPassword', [HomeController::class, 'checkPassword']);
        Route::post('changePassword', [HomeController::class, 'changePassword']);

        Route::get('edit-profile', [HomeController::class, 'editProfile'])->name('edit-profile');
        Route::post('update-profile', [HomeController::class, 'updateProfile'])->name('update-profile');

        Route::resource('language', LanguageController::class);
        Route::get('language-sample', [LanguageController::class, 'language_sample']);
        Route::get('language-list', [LanguageController::class, 'show']);

        Route::get('set-language/{lang}', [LanguageController::class, 'set_language']);
        Route::get('sendtest', [SettingController::class, 'test_mail']);

        // fees
        Route::resource('fees-type', FeesTypeController::class);

        Route::get('fees/classes', [FeesTypeController::class, 'feesClassListIndex'])->name('fees.class.index');
        // Route::post('fees/classes/update', [FeesTypeController::class, 'updateFeesClass'])->name('fees.class.update');
        Route::get('fees/classes/list', [FeesTypeController::class, 'feesClassList'])->name('fees.class.list');


        Route::post('class/fees-type', [FeesTypeController::class, 'updateFeesClass'])->name('class.fees.type.update');
        Route::delete('class/fees-type/{fees_class_id}', [FeesTypeController::class, 'removeFeesClass'])->name('class.fees.type.delete');

        Route::get('fees/paid', [FeesTypeController::class, 'feesPaidListIndex'])->name('fees.paid.index');
        Route::get('fees/paid/list', [FeesTypeController::class, 'feesPaidList'])->name('fees.paid.list');
        Route::post('fees/paid/store', [FeesTypeController::class, 'feesPaidStore'])->name('fees.paid.store');

        Route::get('fees-config', [FeesTypeController::class, 'feesConfigIndex'])->name('fees.config.index');
        Route::post('fees-config/update', [FeesTypeController::class, 'feesConfigUpdate'])->name('fees.config.udpate');
        Route::put('fees/paid/update/{id}', [FeesTypeController::class, 'feesPaidUpdate'])->name('fees.paid.udpate');
        Route::delete('fees/paid/remove-choiceable-fees/{id}', [FeesTypeController::class, 'feesPaidRemoveChoiceableFees'])->name('fees.paid.remove.choiceable.fees');
        Route::delete('fees/paid/remove-installment-fees/{id}', [FeesTypeController::class, 'feesPaidRemoveInstallmentFees'])->name('fees.paid.remove.installment.fees');
        Route::delete('fees/paid/clear-data/{id}', [FeesTypeController::class, 'clearFeesPaidData'])->name('fees.paid.clear.data');

        Route::post('fees/optional-paid/store', [FeesTypeController::class, 'optionalFeesPaidStore'])->name('fees.optional-paid.store');
        Route::post('fees/compulsory-paid/store', [FeesTypeController::class, 'compulsoryFeesPaidStore'])->name('fees.compulsory-paid.store');

        Route::get('fees/transaction-logs', [FeesTypeController::class, 'feesTransactionsLogsIndex'])->name('fees.transactions.log.index');
        Route::get('fees/transaction-logs/list', [FeesTypeController::class, 'feesTransactionsLogsList'])->name('fees.transactions.log.list');

        Route::get('fees/paid/receipt-pdf/{id}', [FeesTypeController::class, 'feesPaidReceiptPDF'])->name('fees.paid.receipt.pdf');
        Route::get('fees/fees-receipt', function () {
            return view('fees.fees_receipt');
        })->name('fees.receipt');

        // //Pending Fees
        // Route::get('fees/fees-pending',[FeesTypeController::class,'feesPendingIndex'])->name('fees.pending.index');
        // Route::get('fees/fees-pending/list', [FeesTypeController::class, 'feesPendingList'])->name('fees.pending.list');

        // Online Exam
        Route::get('online-exam/terms-conditions',[OnlineExamController::class ,'onlineExamTermsConditionIndex'])->name('online-exam.terms-conditions');
        Route::post('online-exam/store-terms-conditions',[OnlineExamController::class ,'storeOnlineExamTermsCondition'])->name('online-exam.store-terms-conditions');

        Route::resource('online-exam', OnlineExamController::class);
        Route::post('online-exam/add-new-question',[OnlineExamController::class ,'storeExamQuestionChoices'])->name('online-exam.add-new-question');
        Route::get('online-exam/get-class-subject-questions/{online_exam_id}',[OnlineExamController::class ,'getClassSubjectQuestions'])->name('online-exam-question.get-class-subject-questions');
        Route::get('get-subject-online-exam',[OnlineExamController::class ,'getSubjects']);
        Route::get('get-exam-question-index',[OnlineExamController::class ,'examQuestionsIndex'])->name('exam.questions.index');
        Route::post('online-exam/store-questions-choices',[OnlineExamController::class ,'storeQuestionsChoices'])->name('online-exam.store-choice-question');
        Route::delete('online-exam/remove-choiced-question/{id}',[OnlineExamController::class ,'removeQuestionsChoices'])->name('online-exam.remove-choice-question');
        Route::get('online-exam/result/{id}', [OnlineExamController::class, 'onlineExamResultIndex'])->name('online-exam.result.index');
        Route::get('online-exam/result-show/{id}', [OnlineExamController::class, 'showOnlineExamResult'])->name('online-exam.result.show');

        Route::resource('online-exam-question', OnlineExamQuestionController::class);
        Route::delete('online-exam-question/remove-option/{id}', [OnlineExamQuestionController::class , 'removeOptions']);
        Route::delete('online-exam-question/remove-answer/{id}', [OnlineExamQuestionController::class , 'removeAnswers']);
        // End Online Exam Routes

        Route::get('app-settings', [SettingController::class, 'app_index']);
        Route::post('app-settings', [SettingController::class, 'app_update']);
        Route::get('system-update', [SystemUpdateController::class, 'index'])->name('system-update.index');
        Route::post('system-update', [SystemUpdateController::class, 'update'])->name('system-update.update');


        Route::resource('stream',StreamController::class);

        Route::resource('shifts',ShiftController::class);
        Route::get('get-class-teacher/{class_section_id}',[ClassTeacherController::class,'getClassTeacherlist']);
        Route::get('get-all-class-teacher/{class_section_id}',[ClassTeacherController::class,'getNotClassTeacherList']);
        Route::get('update-warning-modal',[HomeController::class, 'updateWarningModal'])->name('update-warning-modal');
        Route::get('attendance/create_bulk',[AttendanceController::class,'createBulkData'])->name('attendance.create-bulk-data');

        Route::post('form-fields/change-rank', [FormFieldController::class,'changeRank']);
        Route::resource('form-fields', FormFieldController::class);

        Route::resource('notifications', NotificationController::class);

        Route::delete('multiple-event/{id}', [EventController::class, 'deleteMultipleEvent'])->name('multiple.event.delete');
        Route::resource('events', EventController::class);

        Route::get('content',[WebSettingController::class,'content_index'])->name('content.index');
        Route::get('content-list',[WebSettingController::class,'content_show'])->name('content.show');
        Route::post('content/{id}',[WebSettingController::class,'content_update'])->name('content.edit');
        Route::get('educational-program',[WebSettingController::class,'educational_index'])->name('educational.index');
        Route::post('educational-program/store',[WebSettingController::class,'educational_store'])->name('educational.store');
        Route::get('educational-program-list',[WebSettingController::class,'educational_show'])->name('educational.show');
        Route::post('educational-program/update',[WebSettingController::class,'educational_update'])->name('educational.update');
        Route::delete('educational-program/delete{id}',[WebSettingController::class,'educational_delete'])->name('educational.delete');

        Route::get('faq',[WebSettingController::class,'faq_index'])->name('faq.index');
        Route::post('faq/store',[WebSettingController::class,'faq_store'])->name('faq.store');
        Route::get('faq-list',[WebSettingController::class,'faq_show'])->name('faq.show');
        Route::post('faq/update/{id}',[WebSettingController::class,'faq_update'])->name('faq.update');
        Route::delete('faq/delete/{id}',[WebSettingController::class,'faq_delete'])->name('faq.delete');

        Route::get('contact-us/web',[WebSettingController::class,'contact_us_index'])->name('contact_us.index');
        Route::get('contact-us-list',[WebSettingController::class,'contact_us_show'])->name('contact_us.show');
        Route::post('contact-us/reply/{id}',[WebSettingController::class,'reply'])->name('contact_us.reply');
        Route::delete('contact-us/delete/{id}',[WebSettingController::class,'contact_us_delete'])->name('contact_us.delete');


        Route::get('photos',[MediaController::class,'photo_index'])->name('photo.index');
        Route::post('photos/store',[MediaController::class,'photo_store'])->name('photos.store');
        Route::get('photos-list',[MediaController::class,'photo_show'])->name('photos.show');
        Route::get('photos/edit/{id}',[MediaController::class,'edit_index'])->name('edit.index');
        Route::post('photos/update',[MediaController::class,'photo_update'])->name('photo.update');
        Route::delete('photos/delete/{id}',[MediaController::class,'photo_delete'])->name('photo.delete');
        Route::post('image/update',[MediaController::class,'image_update'])->name('image.update');
        Route::delete('image/delete/{id}',[MediaController::class,'image_delete'])->name('image.delete');

        Route::get('/videos-index',[MediaController::class,'video_index'])->name('video.index');
        Route::post('videos/store',[MediaController::class,'video_store'])->name('video.store');
        Route::get('videos-list',[MediaController::class,'video_show'])->name('video.show');
        Route::post('videos/update/{id}',[MediaController::class,'video_update'])->name('video.update');
        Route::delete('videos/delete/{id}',[MediaController::class,'video_delete'])->name('video.delete');

        Route::resource('staff', StaffController::class);

        Route::get('generate-id',[StudentController::class,'generateIdCardIndex'])->name('generate_id.index');
        Route::get('id-card-settings',[StudentController::class,'idCardSettingIndex'])->name('id_card_setting.index');
        Route::post('id-card-settings/update',[StudentController::class, 'updateIdCardSetting'])->name('id_card_settings.update');
        Route::post('generate-id-card',[StudentController::class, 'generateIdCard']);
        Route::delete('remove-image/{type}',[StudentController::class, 'deleteImage']);

        Route::get('generate-bonafide-certificate/{id}',[StudentController::class, 'bonafideCertificateIndex'])->name('bonafide.certificate.index');
        Route::post('bonafide-certificate', [StudentController::class, 'generateBonafideCertificate'])->name('bonafide.certificate.pdf');

        Route::get('generate-leaving-certificate/{id}',[StudentController::class, 'leavingCertificateIndex'])->name('leaving.certificate.index');
        Route::post('leaving-certificate', [StudentController::class, 'generateLeavingCertificate'])->name('leaving.certificate.pdf');

        Route::get('student-generate-result',[StudentController::class, 'resultIndex'])->name('student.result.index');

        Route::get('get-student-list',[StudentController::class,'studentList'])->name('get.student.list');
        Route::get('generate-result/{id}',[StudentController::class, 'generateResult'])->name('generate.result');


        Route::resource('leave-master', LeaveMasterController::class);

        Route::get('leave-report',[LeaveController::class, 'leaveReportIndex'])->name('leave-report.index');
        Route::get('leave-details', [LeaveController::class, 'leaveDetails'])->name('leave-details-list');
        Route::get('leave-request',[LeaveController::class,'leaveRequestIndex'])->name('leave-request.index');
        Route::get('leave-request-show',[LeaveController::class, 'leaveRequestShow'])->name('leave-request.show');
        Route::post('leave-request-update',[LeaveController::class,'leaveStatusUpdate'])->name('leave-status.update');
        Route::delete('leave-request-delete/{id}', [LeaveController::class, 'leaveRequestDelete'])->name('leave-request.delete');
        Route::resource('leave', LeaveController::class);

        Route::get('chat-settings',[SettingController::class,'chat_setting_index'])->name('chat_setting.index');
        Route::post('chat-settings-update',[SettingController::class, 'chat_setting_update'])->name('chat_setting.update');
        Route::post('delete-chat-messages',[SettingController::class, 'delete_chat_messages'])->name('chat_message.delete');

        Route::get('attendance-report',[AttendanceController::class, 'attendaceReportIndex'])->name('attendace_report.index');
        Route::get('student-attendance-report',[AttendanceController::class, 'attendanceReportShow']);

        Route::get('view-schedule/{id}',[EventController::class,'viewScheduleIndex'])->name('view_schedule.index');
        Route::post('events-update', [EventController::class, 'updateEvents'])->name('events.update');

        Route::get('online-registration',[StudentController::class, 'onlineRegistrationIndex'])->name('online-registration.index');
        Route::get('online-registration-list', [StudentController::class, 'onlineRegistrationList'])->name('online-registration.list');
        Route::delete('permanent-delete/{id}',[StudentController::class, 'permanentDelete'])->name('permanent-delete');
        Route::post('update-active-status', [StudentController::class, 'updateStatus'])->name('update-active-status');
        Route::get('get-class-section-by-class/{class_id}', [StudentController::class, 'getClassSectionByClass']);

        Route::resource('semester', SemesterController::class);

        Route::get('class-subject-edit/{id}', [ClassSchoolController::class,'classSubjectsEdit'])->name('class-subject-edit.index');
        Route::get('student-leave-request',[LeaveController::class,'studentLeaveRequestIndex'])->name('student-leave-request.index');
        Route::get('student-leave-request-list',[LeaveController::class, 'studentLeaveRequestList'])->name('student-leave-request.show');
        Route::post('student-leave-request-update',[LeaveController::class,'studentLeaveStatusUpdate'])->name('student-leave-status.update');

        Route::get('staff-leave',[LeaveController::class,'staffLeaveIndex'])->name('staff-leave.index');
        Route::get('staff-leave-list',[LeaveController::class, 'staffLeaveList'])->name('staff-leave.show');

    });
});*/

Route::get('delete-chat-message/cron-job', [SettingController::class, 'cron_job']);


Route::get('clear', function () {
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    return redirect()->back();
});


