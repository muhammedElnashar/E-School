@extends('layouts.master')

@section('title')
    {{ __('subject') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">{{ __('manage') . ' ' . __('subjects') }}</h3>
        </div>

        {{-- قسم إنشاء موضوع --}}
        <div class="row">
            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ __('create') . ' ' . __('subject') }}</h4>
                        <form class="pt-3" method="POST" action="{{ route('subjects.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required placeholder="{{ __('name') }}">
                            </div>
                            <div class="form-group">
                                <label>{{ __('image') }} <span class="text-danger">*</span></label>
                                <input type="file" name="image" class="form-control" required placeholder="{{ __('image') }}">
                            </div>
                            <input class="btn btn-theme" type="submit" value="{{ __('submit') }}">
                        </form>
                    </div>
                </div>
            </div>

            {{-- قسم عرض المواضيع --}}
            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ __('list') . ' ' . __('subjects') }}</h4>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('name') }}</th>
                                <th>{{ __('action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($subjects as $subject)
                                <tr>
                                    <td>{{ $subject->id }}</td>
                                    <td>{{ $subject->name }}</td>
                                    <td><img src="{{asset('storage/'.$subject->image)}}" alt=""></td>
                                    <td class="d-flex">
                                        <button
                                            class="btn btn-warning btn-sm mr-2 btn-edit"
                                            data-id="{{ $subject->id }}"
                                            data-url="{{ route('subjects.edit', $subject->id) }}"
                                        >{{ __('edit') }}</button>
                                        <form method="post" action="{{ route('subjects.destroy', $subject->id) }}">
                                            @csrf
                                            @method("DELETE")
                                            <button type="submit" class="btn btn-danger deleted-btn btn-sm btn-delete">{{ __('delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">{{ __('no data') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        {{$subjects->links()}}

                    </div>
                </div>
            </div>
        </div>

        {{-- مودال التعديل --}}
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editSubjectLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form method="POST" id="editForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editSubjectLabel">{{ __('edit') . ' ' . __('subject') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('close') }}">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" id="edit-id">
                            <div class="form-group">
                                <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="edit-name" class="form-control" required placeholder="{{ __('name') }}">
                            </div>
                            <div class="form-group">
                                <label>{{ __('image') }} <span class="text-danger">*</span></label>
                                <input type="file" name="image" id="edit-image" class="form-control" placeholder="{{ __('image') }}">
                                <div class="mt-2">
                                    <img id="current-image" src="" alt="Current Image" style="max-width: 100px; border-radius: 8px;">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-theme">{{ __('submit') }}</button>
                            <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('cancel') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            // فتح مودال التعديل وتحميل البيانات
            $('.btn-edit').click(function () {
                let url = $(this).data('url');
                $.get(url, function (data) {
                    console.log(data); // تحقق هنا

                    $('#edit-id').val(data.id);
                    $('#edit-name').val(data.name);

                    $('#editForm').attr('action', 'subjects/'+data.id );

                    // عرض الصورة الحالية
                    $('#current-image').attr('src', data.image_url); // تأكد أن `image_url` موجود في البيانات المسترجعة

                    $('#editModal').modal('show');
                });
            });

            // حذف مع SweetAlert
            $(`.deleted-btn`).click(function(e){
                let form = $(this).parents('form');
                e.preventDefault();
                Swal.fire({
                    title: '{{ __("delete_title") }}',
                    text: '{{ __("confirm_message") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: '{{ __("cancel") }}',
                    confirmButtonText: '{{ __("yes_delete") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
