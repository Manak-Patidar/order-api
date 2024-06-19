@extends('layouts.app')
@section('content')
@include('layouts.messages')
<div class="row">
    <div class="col-md-10 offset-1">
        <form method="post" action="{{route('category.create')}}">
            <div class="card">
                <div class="card-header">
                    <p class="text-center">Add Category</p>
                </div>
                <div class="card-body">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 offset-3">
                            <label>Category</label>
                            <input type="text" name="category" value="" class="form-control"><br>
                            @error('category')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>
                <div class="card-footer ">
                    <div class="offset-3">
                        <button type="submit" class="btn btn-primary ">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('script')

@stop