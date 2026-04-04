@extends('errors.layout')

@section('title', 'Page Expired')
@section('code', '419')
@section('status', 'Session Time-out')
@section('message', 'Security protocols have reset the connection due to inactivity. Please refresh the page and re-authenticate your session.')
