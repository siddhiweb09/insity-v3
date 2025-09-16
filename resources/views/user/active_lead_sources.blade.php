@extends('frames.frame')

@section('content')
    <style>

    </style>
    <div class="content-wrapper">
        <div class="card overflow-hidden p-4 shadow-none">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="font-weight-500 text-primary mb-0">Active Lead Sources</h3>
                <div class="btn-group" role="group" aria-label="Basic example">
                    <a href="{{ route('user.manage_lead_sources') }}" class="btn-primary btn" tabindex="0" title="Manage Lead Sources">
                        <i class="ti ti-toggle-right-filled me-2"></i>Manage Lead Sources
                    </a>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table id="lead_sources" class="table table-hover align-middle mb-0">
                        <thead class="bg-primary text-white sticky-top">
                            <tr>
                                <th scope="col">Lead Source</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activeLeads as $lead)
                                <tr>
                                    <td>{{ $lead->sources }}</td>
                                    <td><i class="ti ti-circle-check me-2 text-success fs-6"></i>
                                        {{ $lead->status }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-3">
                                        <i class="ti ti-alert-circle me-2"></i> No lead sources found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        $(document).ready(function () {

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                }
            });

        });


    </script>
@endsection