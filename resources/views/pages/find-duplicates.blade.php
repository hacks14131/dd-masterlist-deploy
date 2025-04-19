@extends('layouts.app')

@section('main_content')
<div>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>Find Duplicate Entries</h2>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <form action="{{ route('masterlists.find-duplicates') }}" method="GET" class="form-inline">
                                <div class="input-group mb-3">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Search by name (e.g., John Montejo Villarosa)" 
                                           value="{{ $search }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">Search Similar Entries</button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="threshold" class="mr-2">Similarity Threshold:</label>
                                    <input type="range" class="custom-range" min="50" max="95" step="5" 
                                           id="threshold" name="threshold" value="{{ $threshold }}"
                                           onchange="document.getElementById('thresholdValue').textContent = this.value + '%'">
                                    <span id="thresholdValue">{{ $threshold }}%</span>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('masterlists.find-duplicates') }}" method="GET">
                                <input type="hidden" name="scan" value="1">
                                <input type="hidden" name="threshold" value="{{ $threshold }}">
                                <button type="submit" class="btn btn-warning">
                                    Scan Entire Database for Duplicates
                                </button>
                            </form>
                        </div>
                    </div>

                    @if(isset($fullScan) && $fullScan)
                        <div class="alert alert-info">
                            <strong>Full Database Scan Results</strong>
                            <p>Displaying entries with similarity above {{ $threshold }}%</p>
                        </div>
                    @endif

                    @if(count($potentialDuplicates) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Original Record</th>
                                        <th>Potential Duplicate</th>
                                        <th>Overall Similarity</th>
                                        <th>Similarity Details</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($potentialDuplicates as $pair)
                                    <tr>
                                        <td>
                                            <strong>ID:</strong> {{ $pair['original']->id }}<br>
                                            <strong>First Name:</strong> {{ $pair['original']->firstName }}<br>
                                            <strong>Middle Name:</strong> {{ $pair['original']->middleName }}<br>
                                            <strong>Family Name:</strong> {{ $pair['original']->familyName }}<br>
                                            <strong>Ext:</strong> {{ $pair['original']->nameExtension }}<br>
                                            <strong>Brgy:</strong> {{ $pair['original']->barangay }}<br>
                                            <strong>Precinct:</strong> {{ $pair['original']->precinctNo }}
                                        </td>
                                        <td>
                                            <strong>ID:</strong> {{ $pair['duplicate']->id }}<br>
                                            <strong>First Name:</strong> {{ $pair['duplicate']->firstName }}<br>
                                            <strong>Middle Name:</strong> {{ $pair['duplicate']->middleName }}<br>
                                            <strong>Family Name:</strong> {{ $pair['duplicate']->familyName }}<br>
                                            <strong>Ext:</strong> {{ $pair['duplicate']->nameExtension }}<br>
                                            <strong>Brgy:</strong> {{ $pair['duplicate']->barangay }}<br>
                                            <strong>Precinct:</strong> {{ $pair['duplicate']->precinctNo }}
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <span class="badge badge-{{ $pair['similarity'] >= 90 ? 'danger' : ($pair['similarity'] >= 75 ? 'warning' : 'info') }}">
                                                    {{ $pair['similarity'] }}%
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>First Name:</strong> {{ number_format($pair['details']['firstName'], 1) }}%<br>
                                            <strong>Middle Name:</strong> {{ number_format($pair['details']['middleName'], 1) }}%<br>
                                            <strong>Family Name:</strong> {{ number_format($pair['details']['familyName'], 1) }}%
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('masterlists.edit', $pair['original']->id) }}" class="btn btn-sm btn-primary">Edit Original</a>
                                                <a href="{{ route('masterlists.edit', $pair['duplicate']->id) }}" class="btn btn-sm btn-info">Edit Duplicate</a>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        onclick="if(confirm('Are you sure you want to delete this duplicate?')) { 
                                                            document.getElementById('delete-form-{{ $pair['duplicate']->id }}').submit(); 
                                                        }">
                                                    Delete Duplicate
                                                </button>
                                                <form id="delete-form-{{ $pair['duplicate']->id }}" 
                                                      action="{{ route('masterlists.destroy', $pair['duplicate']->id) }}" 
                                                      method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            @if($search)
                                No potential duplicates found for "{{ $search }}" with the current threshold ({{ $threshold }}%).
                                Try lowering the threshold or modifying your search terms.
                            @else
                                Enter a name to search for potential duplicates.
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection