@extends('layouts.batu')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <!-- Applicant Details -->
            <div class="col-lg-8 col-md-12 mb-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6 class="mb-0">Applicant Details: {{ $joinRequest->full_name }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                    <img src="{{ $joinRequest->photo_path }}"
                                        class="img-fluid rounded border shadow-sm" style="max-height: 150px;">
                                @else
                                    <div class="bg-gray-200 rounded text-center py-4">No Photo</div>
                                @endif
                            </div>
                            <div class="col-md-9">
                                <p><strong>National ID:</strong> {{ $joinRequest->national_id }}</p>
                                <p><strong>Academic ID:</strong> {{ $joinRequest->academic_id }}</p>
                                <p><strong>Group:</strong> {{ $joinRequest->group }}</p>
                                <p><strong>Phone:</strong> {{ $joinRequest->phone_number }}</p>
                                <p><strong>WhatsApp:</strong> {{ $joinRequest->whatsapp_number }}</p>
                                <p><strong>DOB:</strong> {{ $joinRequest->date_of_birth->format('Y-m-d') }}</p>
                            </div>
                        </div>

                        <hr class="my-3">

                        <h6 class="text-primary">Answers</h6>
                        @php $answers = $joinRequest->answers; @endphp

                        @if($answers)
                            <div class="bg-gray-50 p-3 rounded h-96 overflow-y-auto">
                                @foreach($answers as $key => $value)
                                    @if(is_array($value))
                                        <p class="mb-1"><strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong></p>
                                        <ul class="list-disc list-inside mb-2 pl-4">
                                            @foreach($value as $subKey => $subValue)
                                                <li>
                                                    @if(!is_numeric($subKey))
                                                        <strong>{{ ucwords(str_replace('_', ' ', $subKey)) }}:</strong>
                                                    @endif
                                                    {{ is_array($subValue) ? json_encode($subValue) : $subValue }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="mb-2"><strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No answers found.</p>
                        @endif

                    </div>
                </div>
            </div>

            <!-- Create User Form -->
            <div class="col-lg-4 col-md-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0 text-white">Approve & Create User</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('join.storeUser', $joinRequest->id) }}" method="POST">
                            @csrf
                            <div class="input-group input-group-outline mb-3 is-filled">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" value="{{ $joinRequest->full_name }}" readonly>
                            </div>

                            <div class="input-group input-group-outline mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            <div class="input-group input-group-outline mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <div class="input-group input-group-outline mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn bg-gradient-success w-100 my-4 mb-2">Create
                                    Account</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection