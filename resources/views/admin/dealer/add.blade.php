@extends('layouts.app')
@section('content')
@include('layouts.messages')
<div class="row">
    <div class="col-md-10 offset-1">
        <div class="card">
            <div class="card-header">
                <p class="text-center">Add Dealer</p>
            </div>
            <div class="card-body">

                <form method="post" action="{{route('dealer.create')}}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label>Dealer Name</label>
                            <input type="text" name="name" value="" class="form-control"><br>
                            @error('name')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label>Dealer Address</label>
                            <input type="text" name="address" value="" class="form-control"><br>
                            @error('address')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label>longitude</label>
                            <input type="text" name="longitude" value="" class="form-control"><br>

                            @error('longitude')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label>Attitude</label>
                            <input type="text" name="Attitude" value="" class="form-control"><br>
                            @error('Attitude')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>



                        <div class="col-md-6">
                            <label>Concerned person </label>
                            <select class="form-control" name="concered_name">
                                <option>Manak</option>
                                <option>Patidar</option>
                            </select>

                            @error('concered_name')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label>Dealer code </label>
                            <input type="text" name="code" value="" class="form-control"><br>

                            @error('code')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label>Phone number</label>
                            <input type="text" name="number" value="" class="form-control"><br>

                            @error('number')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label>Brands Dealing in </label>
                            <input type="text" name="brands" value="" class="form-control"><br>

                            @error('brands')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

@stop