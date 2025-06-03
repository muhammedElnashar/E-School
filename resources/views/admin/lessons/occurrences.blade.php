@extends('layouts.master')

@section('title')
    Lesson Occurrences
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Lesson Recurrence - {{ $lesson->id }} </h3>
            <a href="{{ route('lessons.index') }}" class="btn btn-outline-secondary btn-sm">Back to Lessons</a>
        </div>

        <div class="card">
            <div class="card-body">
                <h5>Main Lesson Details</h5>
                <p><strong>Teacher:</strong> {{ $lesson->teacher->name }}</p>
                <p><strong>Subject:</strong> {{ $lesson->subject->name }}</p>
                <p><strong>Weeks Count:</strong> {{ $weeksCount }}</p>

                @if ($exceptionDates)
                    <p><strong>Exception Dates:</strong>
                        @foreach ($exceptionDates as $date)
                            <span class="badge bg-danger text-white">{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}</span>
                        @endforeach
                    </p>
                @endif

                <hr>

                <h5>Occurrences</h5>
                @if ($occurrences->isEmpty())
                    <p>No occurrences found.</p>
                @else
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Occurrence Date</th>
                            <th>Start</th>
                            <th>End</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($occurrences as $index => $occurrence)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($occurrence->occurrence_date)->format('Y-m-d') }}</td>
                                <td>{{ \Carbon\Carbon::parse($occurrence->lesson->start_datetime)->format('H:m:s') }}</td>
                                <td>{{ \Carbon\Carbon::parse($occurrence->lesson->end_datetime)->format('H:m:s') }}</td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
