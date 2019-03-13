@extends('layouts.app')
@section('content')
<div class="pageWrapper bgGrey">
    <div class="container">
        <div class="pageWrapper-content">
            <div class="page-title">
                <h2>
                    <span>@if($post->id == 10) {{'About Us'}} @elseif($post->id == 11) {{'Privacy Policy'}} @elseif($post->id == 14) {{'Terms & Conditions'}} @endif  </span>
                </h2>
            </div>

            <div class="_staticPageBlock">
                {!! $post->meta_value !!}
            </div>

        </div>
    </div>
</div>

@endsection
