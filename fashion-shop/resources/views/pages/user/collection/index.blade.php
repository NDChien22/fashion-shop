@extends('layouts.user-layout')

@section('title', 'Bộ sưu tập')

@section('content')
    <livewire:user.collection-listing :collection="$collection" />
@endsection
