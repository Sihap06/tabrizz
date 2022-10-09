@extends('layouts.master')

@section('content')

{{-- @dd($id_) --}}

<div class="container-fluid mt-8 pb-5">
    <div class="row justify-content-center mt-3">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title text-center">Ubah Password</h3>
                </div>
                
                
                <div class="card">
                    <div class="card-body login-card-body">
                        @include('layouts.alert')
                        
                        
                        <form action="{{url('reset')}}" method="post">
                            @csrf
                            <div class="input-group mb-3">
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password Baru">
                                @error('password') <div class="invalid-feedback">{{$message}}</div> @enderror
                                
                            </div>
                            <div class="input-group mb-3">
                                <input type="hidden" name="id" class="form-control" value="{{$id}}">
                                <input type="password" name="ulangipassword" class="form-control @error('ulangipassword') is-invalid @enderror" placeholder="Ulangi Password">
                                @error('ulangipassword') <div class="invalid-feedback">{{$message}}</div> @enderror
                                
                                
                            </div>
                            <div class="row justify-content-center">
                                
                                <div class="col-4">
                                    <button type="submit" class="btn btn-primary btn-block btn-flat">Ubah</button>
                                    
                                </div>
                                <div class="col-4">
                                    <a href="{{url('')}}">
                                        <button type="button" class="btn btn-secondary btn-block btn-flat">Kembali</button>
                                    </a>
                                    
                                </div>
                                
                            </div>
                        </form>
                    </div>
                    
                </div>
                
            </div>
            
        </div>
        
    </div>
    
</div>

@endsection


