<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StaticController;
use App\Http\Controllers\BranchesController;
use App\Http\Controllers\HousesController;
use App\Http\Controllers\StreamsController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\BusesController;
use App\Http\Controllers\DesignationsController;
use App\Http\Controllers\FeeitemsController;
use App\Http\Controllers\FeeassignController;
use App\Http\Controllers\ParentsController;
use App\Http\Controllers\PeriodsController;
use App\Http\Controllers\FcategoriesController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\PmodesController;
use App\Http\Controllers\TeachersController;
use App\Http\Controllers\DeptController;
use App\Http\Controllers\SubjectsController;
use App\Http\Controllers\SubassignController;
use App\Http\Controllers\ExamtypesController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\GradingController;
use App\Http\Controllers\JranksController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ModulesController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\RolesReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeeReportController;
use App\Http\Controllers\SupplierController;


Route::get('/', function () {
    return view('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::put('/users/{id}/update', [UsersController::class, 'update'])->name('update.user');
    Route::get('/users/{id}/edit', [UsersController::class, 'edit'])->name('get.user');
});

Route::middleware('auth')->group(function () {
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
});
Route::get('newuser', [UsersController::class, 'index'])->name('newuser.index');
Route::get('musers', [UsersController::class, 'indexfun'])->name('musers.indexfun');
Route::get('/musers/data', [UsersController::class, 'getData'])->name('musers.data');
Route::prefix('users')->name('newuser.')->group(function () {
    
    Route::post('/store', [UsersController::class, 'store'])->name('store');
    Route::put('/{id}', [UsersController::class, 'update'])->name('update');
    Route::delete('/{id}', [UsersController::class, 'destroy'])->name('destroy');
});

Route::get('man_student', [StudentController::class, 'index'])->name('students.index');
Route::get('rep_student', [StudentController::class, 'index1'])->name('students.index1');
Route::get('add_student', [StudentController::class, 'create'])->name('students.create');
Route::post('add_student', [StudentController::class, 'store'])->name('students.store');
Route::get('campus-stats', [StudentController::class, 'getCampusStats'])->name('campus.stats');
Route::post('postnewitem', [StudentController::class, 'postnewitem']);
Route::post('postpercampus', [StudentController::class, 'postpercampus']);
Route::post('postperhouse', [StudentController::class, 'postperhouse']);
Route::post('postperclass', [StudentController::class, 'postperclass']);
Route::get('/students/data', [StudentController::class, 'getData'])->name('students.data');
Route::get('/student/{id}/edit', [StudentController::class, 'editstudent'])->name('get.student');
Route::post('student/{id}', [StudentController::class, 'update'])->name('student.update');
Route::get('/students/report/{admno}', [StudentController::class, 'getReport'])->name('students.report');
Route::get('/students/all-report', [StudentController::class, 'getAllReport'])->name('students.all-report');
Route::get('/students/export', [StudentController::class, 'exportStudents'])->name('students.export');


Route::get('billing', [BillingController::class, 'create'])->name('billing');
Route::get('fee_reports', [BillingController::class, 'index'])->name('fee_reports');
Route::post('billing', [BillingController::class, 'store'])->name('billing.store');
Route::get('billing-students', [BillingController::class, 'getstudents'])->name('billing.getstudents');
Route::get('billing-students2', [BillingController::class, 'getstudents2'])->name('billing.getstudents2');
Route::get('/get-student-billing-details', [BillingController::class, 'getStudentBillingDetails'])->name('getStudentBillingDetails');
Route::post('postFeePayment', [BillingController::class, 'postFeePayment']);
Route::post('/get-student-receipts', [BillingController::class, 'getStudentReceipts'])->name('get.student.receipts');
Route::post('/print-student-statement', [BillingController::class, 'printStudentStatement'])->name('print.student.statement');

Route::post('/preview-student-statement', [BillingController::class, 'previewStudentStatement'])->name('preview.student.statement');
Route::post('/preview-student-receipt', [BillingController::class, 'previewStudentreceipt'])->name('preview.student.receipt');


Route::get('add_parent', [ParentsController::class, 'create'])->name('Parent.create');
Route::post('add_parent', [ParentsController::class, 'store'])->name('Parent.store');
Route::get('manage_parents', [ParentsController::class, 'manage'])->name('parents.manage');
Route::get('get_parents', [ParentsController::class, 'getParents'])->name('parents.get');
Route::match(['put', 'post'], 'parents/{parentId}', [ParentsController::class, 'update'])->name('parents.update');
Route::delete('delete_parent/{id}', [ParentsController::class, 'destroy'])->name('parents.destroy');
Route::get('/parents/get-dropdown', [ParentsController::class, 'getAllparents'])->name('parents.getDropdown');

Route::get('add_teacher', [TeachersController::class, 'create'])->name('Teacher.create');
Route::post('add_teacher', [TeachersController::class, 'store'])->name('Teacher.store');
Route::get('manage_teachers', [TeachersController::class, 'manage'])->name('teachers.manage');
Route::get('get_teachers', [TeachersController::class, 'getteachers'])->name('teacher.get');
Route::put('teachers/{id}', [TeachersController::class, 'update'])->name('teachers.update');
Route::get('/teachers/HODs', [TeachersController::class, 'getHODs'])->name('teachers.getHODs');
Route::get('/teachers/get-dropdown', [TeachersController::class, 'getAllteachers'])->name('teachers.getDropdown');
/*Route::delete('delete_parent/{id}', [ParentsController::class, 'destroy'])->name('parents.destroy');
*/

Route::get('add_subject', [SubjectsController::class, 'create'])->name('subject.create');
Route::post('add_subject', [SubjectsController::class, 'store'])->name('subject.store');
Route::get('getdepts', [DeptController::class, 'getAllDepts'])->name('depts.getDepts');
Route::get('manage_subjects', [SubjectsController::class, 'manage'])->name('subjects.manage');
Route::get('get_subjects', [SubjectsController::class, 'getteachers'])->name('subject.get');
Route::get('assignsub', [SubassignController::class, 'create'])->name('assignsub');
Route::post('/feesubassign/store', [SubassignController::class, 'store'])->name('feesubassign.store');
Route::get('/subjects/get-dropdown', [SubjectsController::class, 'getAllsubjects'])->name('sub.getDropdown');
// Fetch available + assigned students for a subject
Route::get('/subjects/{id}/students', [SubassignController::class, 'getSubjectStudents'])->name('subjects.students');

// Save elective students for a subject
Route::post('/subjects/{id}/students', [SubassignController::class, 'saveSubjectStudents'])->name('subjects.students.save');
Route::get('/streams/by-subject', [SubassignController::class, 'getclassbysub'])->name('subjects.getBysub');

Route::get('preports', [PerformanceController::class, 'reports'])->name('preports');
//Route::post('preports', [PerformanceController::class, 'store'])->name('preports.store');
Route::get('/performance/pentry', [PerformanceController::class, 'index'])->name('performance.pentry');
Route::get('/performance/students', [PerformanceController::class, 'getStudents'])->name('performance.students');
Route::post('/performance/save', [PerformanceController::class, 'saveMarks'])->name('performance.save');
Route::post('/preview-student-report', [PerformanceController::class, 'previewStudentReport'])->name('preview.student.report');
Route::post('/preview-termly-report', [PerformanceController::class, 'StudenttermlyReport'])->name('preview.termly.reports');
Route::post('/preview-class-report', [PerformanceController::class, 'ClassPerfReport'])->name('preview.classperf.reports');
Route::post('/preview-classanal-report', [PerformanceController::class, 'SubjectAnalysisReport'])->name('preview.classanal.reports');
Route::get('/periods/get-dropdown', [PerformanceController::class, 'getperiods'])->name('periods.getDropdown');
Route::get('terms-students2', [PerformanceController::class, 'getpstudents'])->name('terms.getstudents');

/*Route::put('teachers/{id}', [TeachersController::class, 'update'])->name('teachers.update');
Route::get('/teachers/HODs', [TeachersController::class, 'getHODs'])->name('teachers.getHODs'); */
Route::get('static', [StaticController::class, 'create'])->name('staticinfo');
Route::post('static', [StaticController::class, 'store'])->name('staticinfo.store');
Route::get('/staticinfo/getall', [StaticController::class, 'getAll'])->name('staticinfo.getall');
Route::post('static/{id}', [StaticController::class, 'update'])->name('staticinfo.update');

Route::get('branches', [BranchesController::class, 'create'])->name('branches');
Route::post('branches', [BranchesController::class, 'store'])->name('branches.store');
Route::get('/branches/getall', [BranchesController::class, 'getAll'])->name('branches.getall');
Route::get('/branches/get-dropdown', [BranchesController::class, 'getAllBranches'])->name('branches.getDropdown');
Route::post('branches/{id}', [BranchesController::class, 'update'])->name('branches.update');
Route::delete('branches/{id}', [BranchesController::class, 'destroy'])->name('branches.destroy');

Route::get('houses', [HousesController::class, 'create'])->name('houses');
Route::post('houses', [HousesController::class, 'store'])->name('houses.store');
Route::get('/houses/getall', [HousesController::class, 'getAll'])->name('houses.getall');
Route::get('/houses/get-dropdown', [HousesController::class, 'getAllHouses'])->name('houses.getDropdown');
Route::get('/houses/by-campus', [HousesController::class, 'gethousesByCampus'])->name('houses.getByCampus');
Route::post('houses/{id}', [HousesController::class, 'update'])->name('houses.update');
Route::delete('houses/{id}', [HousesController::class, 'destroy'])->name('houses.destroy');

Route::get('streams', [StreamsController::class, 'create'])->name('streams');
Route::post('streams', [StreamsController::class, 'store'])->name('streams.store');
Route::get('/streams/getall', [StreamsController::class, 'getAll'])->name('streams.getall');
Route::get('/streams/get-dropdown', [StreamsController::class, 'getAllStreams'])->name('streams.getDropdown');
Route::post('streams/{id}', [StreamsController::class, 'update'])->name('streams.update');
Route::delete('streams/{id}', [StreamsController::class, 'destroy'])->name('streams.destroy');
Route::get('/streams/by-class', [StreamsController::class, 'getstreamByClass'])->name('streams.getByclass');

Route::get('classes', [ClassesController::class, 'create'])->name('classes');
Route::post('classes', [ClassesController::class, 'store'])->name('classes.store');
Route::get('/classes/getall', [ClassesController::class, 'getAll'])->name('classes.getall');
Route::get('/classes/get-dropdown', [ClassesController::class, 'getAllClasses'])->name('classes.getDropdown');
Route::get('/classes/get-dropdown2', [ClassesController::class, 'getAllClasses2'])->name('classes.getDropdown2');
Route::get('/classes/get-dropdown3', [ClassesController::class, 'getAllClasses3'])->name('classes.getDropdown3');
Route::get('/classes/by-campus', [ClassesController::class, 'getClassesByCampus'])->name('classes.getByCampus');
Route::post('classes/{id}', [ClassesController::class, 'update'])->name('classes.update');
Route::delete('classes/{id}', [ClassesController::class, 'destroy'])->name('classes.destroy');

Route::get('grading', [GradingController::class, 'create'])->name('grading');
Route::post('grading', [GradingController::class, 'store'])->name('grading.store');
Route::get('jranks-students2', [JranksController::class, 'getstudents2'])->name('jranks.getstudents2');
Route::get('jmarks', [JranksController::class, 'create'])->name('jmarks');
Route::post('jmarks', [JranksController::class, 'store'])->name('jmarks.store');

Route::get('set', [BusesController::class, 'create'])->name('set');
Route::post('set', [BusesController::class, 'store'])->name('set.store');
Route::get('/set/getall', [BusesController::class, 'getAll'])->name('set.getall');
Route::get('/set/get-dropdown', [BusesController::class, 'getAllBuses'])->name('set.getDropdown');
Route::post('set/{id}', [BusesController::class, 'update'])->name('set.update');


Route::get('designations', [DesignationsController::class, 'create'])->name('designations');
Route::post('designations', [DesignationsController::class, 'store'])->name('designations.store');
Route::get('/designations/getall', [DesignationsController::class, 'getAll'])->name('designations.getall');
Route::get('/designations/get-dropdown', [DesignationsController::class, 'getAllDesignations'])->name('designations.getDropdown');
Route::post('designations/{id}', [DesignationsController::class, 'update'])->name('designations.update');
Route::delete('designations/{id}', [DesignationsController::class, 'destroy'])->name('designations.destroy');


Route::get('feeitems', [FeeitemsController::class, 'create'])->name('feeitems');
Route::post('feeitems', [FeeitemsController::class, 'store'])->name('feeitems.store');
Route::get('/feeitems/getall', [FeeitemsController::class, 'getAll'])->name('feeitems.getall');
Route::get('/feeitems/get-dropdown', [FeeitemsController::class, 'getAllFeeitems'])->name('a.getDropdown');
Route::post('feeitems/{id}', [FeeitemsController::class, 'update'])->name('feeitems.update');


Route::get('examtypes', [ExamtypesController::class, 'create'])->name('examtypes');
Route::post('examtypes', [ExamtypesController::class, 'store'])->name('examtypes.store');
Route::get('/examtypes/getall', [ExamtypesController::class, 'getAll'])->name('examtypes.getall');
Route::get('/examtypes/get-dropdown', [ExamtypesController::class, 'getExams'])->name('exam.getDropdown');

Route::get('assign', [FeeassignController::class, 'create'])->name('assign');
Route::get('/assign/get-dropdown', [ClassesController::class, 'getAllClasses'])->name('assign.getDropdown');
Route::post('/feeassign/store', [FeeassignController::class, 'store'])->name('feeassign.store');
//Route::get('/feeassign/getall', [FeeitemsController::class, 'getAll'])->name('feeitems.getall');

Route::get('assignmodify', [FeeassignController::class, 'modify'])->name('assignmodify');
Route::get('/feeassign/get-assignments', [FeeassignController::class, 'getAssignments'])->name('feeassign.getAssignments');

Route::get('periods', [PeriodsController::class, 'create'])->name('periods');
Route::post('periods', [PeriodsController::class, 'store'])->name('periods.store');
Route::post('periods2', [PeriodsController::class, 'store2'])->name('periods.store2');
Route::get('/periods/getall', [PeriodsController::class, 'getAll'])->name('periods.getall');
Route::get('/periods/current', [PeriodsController::class, 'getCurrentPeriod'])->name('periods.current');
Route::get('depts', [DeptController::class, 'create'])->name('depts');
Route::post('depts', [DeptController::class, 'store'])->name('depts.store');
Route::get('/fcategories/getall', [FcategoriesController::class, 'getAll'])->name('fcategories.getall');
Route::get('/fcategories/get-dropdown', [FcategoriesController::class, 'getAllCategories'])->name('fcategories.getDropdown');
Route::post('fcategories/{id}', [FcategoriesController::class, 'update'])->name('fcategories.update');
Route::delete('fcategories/{id}', [FcategoriesController::class, 'destroy'])->name('fcategories.destroy');

Route::get('fcategories', [FcategoriesController::class, 'create'])->name('fcategories');
Route::post('fcategories', [FcategoriesController::class, 'store'])->name('fcategories.store');
Route::get('/fcategories/getall', [FcategoriesController::class, 'getAll'])->name('fcategories.getall');
Route::get('/fcategories/get-dropdown', [FcategoriesController::class, 'getAllCategories'])->name('fcategories.getDropdown');
Route::post('fcategories/{id}', [FcategoriesController::class, 'update'])->name('fcategories.update');
Route::delete('fcategories/{id}', [FcategoriesController::class, 'destroy'])->name('fcategories.destroy');

Route::get('pmodes', [PmodesController::class, 'create'])->name('pmodes');
Route::post('pmodes', [PmodesController::class, 'store'])->name('pmodes.store');
Route::get('/pmodes/getall', [PmodesController::class, 'getAll'])->name('pmodes.getall');
Route::get('/pmodes/get-dropdown', [PmodesController::class, 'getAllCategories'])->name('pmodes.getDropdown');
Route::post('pmodes/{id}', [PmodesController::class, 'update'])->name('pmodes.update');
Route::delete('pmodes/{id}', [PmodesController::class, 'destroy'])->name('pmodes.destroy');
Route::get('pmodes', [PmodesController::class, 'getrequired'])->name('pmodes.getrequired');


Route::get('massign', [ModulesController::class, 'index'])->name('massign.index');
Route::prefix('modules')->name('modules.')->middleware('auth')->group(function () {
    Route::post('/get-user-modules', [ModulesController::class, 'getUserModules'])->name('getUserModules');
    Route::post('/get-role-modules', [ModulesController::class, 'getRoleModules'])->name('getRoleModules');
    Route::post('/assign', [ModulesController::class, 'assignModules'])->name('assign');
    Route::post('/save', [ModulesController::class, 'saveModules'])->name('save');
    Route::post('/remove', [ModulesController::class, 'removeModule'])->name('remove');
});

Route::get('roles', [RolesController::class, 'index'])->name('roles.index');
Route::post('roles', [RolesController::class, 'store'])->name('roles.store');
Route::get('/roles/getall', [RolesController::class, 'getAll'])->name('roles.getall');
Route::post('roles/{id}', [RolesController::class, 'update'])->name('roles.update');
Route::get('/roles/get-dropdown', [RolesController::class, 'getAllBranches'])->name('roles.getDropdown');
//Route::get('mngprol', [Managepayroll::class, 'showPayrollPeriod']);
Route::get('/roles/report', [RolesReportController::class, 'generateReport'])->name('roles.report');
Route::get('/roles/report/download', [RolesReportController::class, 'downloadReport'])->name('roles.report.download');

Route::get('/dashboard/data', [DashboardController::class, 'getDashboardData'])->name('dashboard.data');


Route::get('/fee-reports/data', [FeeReportController::class, 'getData'])->name('fee-reports.data');
Route::get('/fee-reports/filters', [FeeReportController::class, 'getFilters'])->name('fee-reports.filters');
Route::get('/fee-reports/classes-by-branch', [FeeReportController::class, 'getClassesByBranch'])->name('fee-reports.classes-by-branch');
Route::get('/fee-reports/export', [FeeReportController::class, 'export'])->name('fee-reports.export');

Route::get('add_supplier', [SupplierController::class, 'create'])->name('supplier.create');
Route::post('add_supplier', [SupplierController::class, 'store'])->name('supplier.store');
Route::get('manage_suppliers', [SupplierController::class, 'manage'])->name('suppliers.manage');
Route::get('get_suppliers', [SupplierController::class, 'getSuppliers'])->name('suppliers.get');
Route::put('suppliers/{id}', [SupplierController::class, 'update'])->name('suppliers.update');
Route::delete('suppliers/{id}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');



Route::get('manage_invoices', [SupplierController::class, 'indexInvoices'])->name('suppliers.invoices');
Route::get('add_invoice', [SupplierController::class, 'createInvoice'])->name('invoices.create');
Route::post('add_invoice', [SupplierController::class, 'storeInvoice'])->name('invoices.store');
Route::get('edit_invoice/{id}', [SupplierController::class, 'editInvoice'])->name('invoices.edit');
Route::put('update_invoice/{id}', [SupplierController::class, 'updateInvoice'])->name('invoices.update');
Route::delete('delete_invoice/{id}', [SupplierController::class, 'destroyInvoice'])->name('invoices.destroy');
Route::get('supplier_invitations', [SupplierController::class, 'supplierInvitations'])->name('suppliers.create_invitations');
Route::post('supplier_invitations',[SupplierController::class, 'storeInvoiceInvitation'])->name('supplier.invitations.store');
Route::post('send_invoice_invitation/{invoice_id}', [SupplierController::class, 'sendInvoiceInvitation'])->name('suppliers.sendInvitation');
Route::get('supplier_payments', [SupplierController::class, 'payments'])->name('payments.manage');
Route::post('supplier_payments', [SupplierController::class, 'storePayment'])->name('suppliers.payments.store');
Route::get('invoice-form/{invitation}', [SupplierController::class, 'showInvoiceForm'])->name('supplier.invoice.form');
Route::post('/supplier/invoice/{id}/approve',[SupplierController::class, 'approveInvoice']);
Route::post('/supplier/invoice/{id}/reject',[SupplierController::class, 'rejectInvoice']);
Route::post('/supplier/invoice/{id}/paid',[SupplierController::class, 'markInvoicePaid']);

Route::post('invoice-form/{invitation}', [SupplierController::class, 'submitInvoiceForm'])->name('supplier.invoice.submit');
require __DIR__.'/auth.php';
