@extends('layouts.master')

@section('title')
    {{ __('Purchases') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header d-flex justify-content-between align-items-center">
            <h3 class="page-title">{{ __('Payments') }}</h3>


        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title">{{ __(' Payment List') }}</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Username') }}</th>
                            <th>{{ __('Marketplace Item') }}</th>
                            <th>{{ __('Marketplace Item Type') }}</th>
                            <th>{{ __(' price') }}</th>
                            <th>{{ __('Remaining Credits') }}</th>
                            <th>{{ __('File ') }}</th>
                            <th>{{ __('Active At ') }}</th>

                            <th>{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($purchases as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->user->name }}</td>
                                <td>{{ $item->marketplaceItem->name}} @if($item->marketplaceItem-> package_scope) - {{ $item->marketplaceItem-> package_scope}} @endif </td>
                                <td>{{ $item->marketplaceItem->type->value }}</td>
                                <td>{{ $item->price }}</td>
                                <td>@if( $item->remaining_credits ){{ $item->remaining_credits }} @endif</td>
                                <td>@if( $item->marketplaceItem->file_path ) <a target="_blank" href="{{asset('files/'.$item->marketplaceItem->file_path )}}">File</a> @endif</td>
                                <td>{{$item->activated_at->format('Y-m-d')}}</td>
                                <td>
                                    @if($item->marketplaceItem->type->value == \App\Enums\MarketplaceItemType::Package->value)
                                        <button type="button" class="btn btn-sm btn-warning" data-toggle="modal"
                                                data-target="#editModal-{{ $item->id }}">
                                            {{ __('Edit') }}
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            <div class="modal fade" id="editModal-{{ $item->id }}" tabindex="-1" role="dialog"
                                 aria-labelledby="editModalLabel-{{ $item->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-md" role="document">
                                    <form action="{{ route('purchases.update', $item->id) }}" method="POST"
                                          enctype="multipart/form-data" class="w-100">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title">{{ __('Edit Package') }}
                                                    - {{ $item->name }}</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>{{ __('Remaining Credits	') }} <span class="text-danger">*</span></label>
                                                    <input type="number" name="remaining_credits" class="form-control"
                                                           value="{{ old('remaining_credits', $item->remaining_credits) }}" required>
                                                </div>




                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">{{ __('Close') }}</button>
                                                    <button type="submit"
                                                            class="btn btn-success">{{ __('Save Changes') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="7">{{ __('No data found.') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    {{ $purchases->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

