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
                    <button type="submit" id="updateLeadSource" class="btn btn-primary">
                        <i class="ti ti-send-2 me-2"></i>Submit
                    </button>
                </div>
            </div>

            <div class="card-body p-0" id="lead_sources_table">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table id="lead_sources" class="table table-hover align-middle mb-0">
                        <thead class="bg-primary text-white sticky-top">
                            <tr>
                                <th><input type="checkbox" name="checkAll" id="checkAll"></th>
                                <th>Lead Source</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($registeredSources as $source)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="check" class="leadCheckbox" value="{{ $source }}" {{ in_array($source, $activeSources) ? 'checked' : '' }}>
                                    </td>
                                    <td>{{ $source }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-3">
                                        <i class="ti ti-alert-circle me-2"></i> No active lead sources found.
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

            $('#updateLeadSource').click(function () {

                const selectedLeads = [];

                $('.leadCheckbox:checked').each(function () {
                    selectedLeads.push($(this).val());
                });

                console.log(selectedLeads);

                if (selectedLeads.length === 0) {
                    alert("Please select at least one lead source.");
                    return;
                }

                $.ajax({
                    url: '{{ route('updateLeadSources') }}',
                    method: 'POST',
                    data: {
                        leads: selectedLeads,
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            alert('Lead sources updated successfully!');
                            setTimeout(function () {
                                location.reload();
                            }, 1000);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function () {
                        alert('AJAX error occurred.');
                    }
                });
            });


        });


    </script>
@endsection