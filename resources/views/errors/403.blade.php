@extends('errors.layout')

@section('title', 'Forbidden')
@section('code', '403')
@section('status', 'Access Restricted')
@section('message', 'User credentials lack the necessary clearance level for this sector. Please contact a higher protocol officer if you believe this is in error.')
