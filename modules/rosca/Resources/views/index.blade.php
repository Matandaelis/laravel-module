@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Roscas (Chamas)</h1>

        <ul>
            @foreach($roscas as $rosca)
                <li>
                    <strong>{{ $rosca->name }}</strong>
                    <div>Cycle: {{ $rosca->cycle_period }} | Contribution: {{ number_format($rosca->contribution_amount,2) }} | Members: {{ $rosca->members_count ?? 0 }}</div>
                    <p>{{ $rosca->description }}</p>
                </li>
            @endforeach
        </ul>
    </div>
@endsection
