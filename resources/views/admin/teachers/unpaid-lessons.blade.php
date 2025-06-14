@extends('layouts.master')

@section('title', '')

@section('content')
    <div class="content-wrapper">
        <div class="page-header d-flex justify-content-between align-items-center">
            <h3 class="page-title">Unpaid Teacher Lessons {{ $teacher->name }}</h3>
            <a href="{{ route('teacher.index') }}" class="btn btn-secondary">Back</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title mb-4">Teacher Details</h4>

                <table class="table table-bordered">
                    <tr>
                        <th>Teacher Name</th>
                        <td>{{ $teacher->name }}</td>
                    </tr>
                    <tr>
                        <th>Teacher Email</th>
                        <td>{{ $teacher->email }}</td>
                    </tr>
                    <tr>
                        <th>Teacher Phone</th>
                        <td>{{ $teacher->phone }}</td>
                    </tr>
                    <tr>
                        <th>IBAN</th>
                        <td>{{ $teacher->iban }}</td>
                    </tr>
                    <tr>
                        <th>Total Lesson</th>
                        <td>{{ $totalLessons }}</td>
                    </tr>

                    <tr>
                        <th>Paid Lesson</th>
                        <td>{{ $paidLessons }}</td>
                    </tr>
                </table>

                <hr>

                <h4 class="card-title mb-4">UnPaid Finish Lessons Details</h4>

                <table class="table table-bordered text-center">
                    <thead class="thead-dark">
                    <tr>
                        <th>Lesson Type</th>
                        <th>Count</th>
                        <th>Lesson Price</th>
                        <th>Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Individual</td>
                        <td>{{ $individualCount }}</td>
                        <td>{{ number_format($individualPrice, 2) }} $</td>
                        <td>{{ number_format($individualCount * $individualPrice, 2) }} $</td>
                    </tr>
                    <tr>
                        <td>Group</td>
                        <td>{{ $groupCount }}</td>
                        <td>{{ number_format($groupPrice, 2) }} $</td>
                        <td>{{ number_format($groupCount * $groupPrice, 2) }} $</td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                      {{--  <th colspan="1" class="text-ce">Total Lesson</th>
                        <th>{{$groupCount + $individualCount}}</th>--}}
                        <th colspan="3" class="text-right">Total</th>
                        <th>{{ number_format($total, 2) }} $</th>
                    </tr>
                    </tfoot>
                </table>

                <form action="{{route('admin.transaction.pay')}}" method="POST">
                    @csrf
                    <input type="hidden" name="amount" value="{{ $total }}">
                    <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
                    <button type="submit" class="btn btn-success btn-lg w-100 mt-4">
                        Paid  To Teacher
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
