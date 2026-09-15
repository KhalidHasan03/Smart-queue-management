@extends('landing.layouts.landing')

@section('content')
    @include('landing.components.hero-section')
    @include('landing.components.trust-bar')
    @include('landing.components.how-it-works')
    @include('landing.components.features')
    @include('landing.components.pricing')
    @include('landing.components.reviews')
    @include('landing.components.faq')
    @include('landing.components.demo-cta')
@endsection
