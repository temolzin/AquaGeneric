@extends('errors::minimal')

@section('title', __('Acceso Denegado'))
@section('code', '403')
@section('message', __('No tienes autorización para acceder a esta página' ?: 'Acceso denegado'))
