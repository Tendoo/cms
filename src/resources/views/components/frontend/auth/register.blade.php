@inject( 'Field', 'Tendoo\Core\Services\Field' )
@extends( 'tendoo::components.frontend.auth.master' )
@section( 'tendoo::components.frontend.auth.master.body' )
<div class="col-md-4">
    <div class="row d-flex flex-column">
        @include( 'tendoo::partials.shared.auth-logo' )
        <div class="col-md-12">
            <form action="{{ route( 'register.post' ) }}" method="post">
                {{ csrf_field() }}
                <div class="card">
                    <div class="card-header">{{ __( 'Register' ) }}</div>
                    <div class="card-body">
                        @each( 'tendoo::partials.shared.fields', $Field::register(), 'field' )
                    </div>
                    <div class="card-footer p-2 d-flex justify-content-between">
                        <button class="mb-0 btn btn-raised btn-primary" type="submit">{{ __( 'Register' ) }}</button>
                        <button onClick="document.location = '{{ route( 'login.index' ) }}'" class="mb-0 btn btn-raised btn-info" type="button">{{ __( 'Login' ) }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection