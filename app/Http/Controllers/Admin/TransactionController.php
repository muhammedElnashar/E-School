<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function teacherTransactions()
    {
        $transactions = Transaction::with('teacher', 'admin')
            ->orderBy('paid_at', 'desc')
            ->paginate(10);

        return view('admin.purchases.teacher-transaction', compact('transactions'));
    }

    public function pay(Request $request)
    {

        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
        ]);
        $adminId = auth()->id();
        $teacherId = $validated['teacher_id'];
        $amount = $validated['amount'];

        try {
            DB::beginTransaction();

            $transaction = Transaction::create([
                'teacher_id' => $teacherId,
                'admin_id' => $adminId,
                'amount' => $amount,
                'paid_at' => now(),
            ]);

            DB::table('lesson_students')
                ->join('lesson_occurrences', 'lesson_students.lesson_occurrence_id', '=', 'lesson_occurrences.id')
                ->join('lessons', 'lesson_occurrences.lesson_id', '=', 'lessons.id')
                ->where('lessons.teacher_id', $teacherId)
                ->where('lesson_students.is_paid_to_teacher', false)
                ->update(['lesson_students.is_paid_to_teacher' => true]);

            DB::commit();

            return redirect()->route('admin.transaction.teacher-transactions')->with('success', 'تم تسجيل التحويل بنجاح. يُرجى تنفيذ التحويل البنكي يدويًا.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Transaction Failed  ' . $e->getMessage()]);
        }
    }
}
