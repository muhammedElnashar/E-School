@extends('layouts.master')

@section('title')
    {{ __('education_stages') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">{{ __('manage') . ' ' . __('education stages') }}</h3>
        </div>

        <div class="row">
            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ __('create') . ' ' . __('education stages') }}</h4>
                        <form class="pt-3" method="POST" action="{{ route('stages.store') }}">
                            @csrf
                            <div class="form-group">
                                <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required placeholder="{{ __('name') }}">
                            </div>
                            <input class="btn btn-theme" type="submit" value="{{ __('submit') }}">
                        </form>
                    </div>
                </div>
            </div>

            {{-- قسم عرض المراحل التعليمية --}}
            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ __('list') . ' ' . __('education stages') }}</h4>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('name') }}</th>
                                <th>{{ __('action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($educationStages as $stage)
                                <tr>
                                    <td>{{ $stage->id }}</td>
                                    <td>{{ $stage->name }}</td>
                                    <td class="d-flex">
                                        <button
                                            class="btn btn-warning btn-sm mr-2 btn-edit"
                                            data-id="{{ $stage->id }}"
                                            data-url="{{ route('stages.edit', $stage->id) }}"
                                        >{{ __('edit') }}</button>
                                        <form method="post" action="{{ route('stages.destroy', $stage->id) }}">
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
                        {{$educationStages->links()}}

                    </div>
                </div>
            </div>
        </div>

        {{-- مودال التعديل --}}
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editStageLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form method="POST" id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editStageLabel">{{ __('edit') . ' ' . __('education_stages') }}</h5>
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
            $('.btn-edit').click(function () {
                let url = $(this).data('url');
                $.get(url, function (data) {
                    $('#edit-id').val(data.id);
                    $('#edit-name').val(data.name);
                    $('#editForm').attr('action', '/education/stages/' + data.id);
                    $('#editModal').modal('show');
                });
            });

            // حذف مع SweetAlert
            $('.deleted-btn').click(function(e){
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
