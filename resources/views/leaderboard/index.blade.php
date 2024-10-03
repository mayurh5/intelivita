@extends('layouts.app')
<style>
        .dataTables_wrapper {
            margin: 20px; 
        }
    </style>
@section('content')
  <div class="container mt-5">
        <h2 class="text-center mb-4">Leaderboard</h2>
        <div class="mb-3 d-flex justify-content-between">
            <div class="form-group">
                <label for="filter" class="form-label">Filter By:</label>
                <select name="filter" id="filter" class="form-select d-inline-block" style="width: auto;">
                    <option value="day">Day</option>
                    <option value="month">Month</option>
                    <option value="year">Year</option>
                </select>
                <button type="button" id="filter_btn" class="btn btn-primary ms-2">Apply Filters</button>
            </div>
            <button type="button" class="btn btn-secondary" id="recalculateButton">Recalculate</button>
        </div>
        <table id="leaderboard" class="table table-striped table-bordered" style="width:100%">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>    Points</th>
                    <th>Rank</th>
                </tr>
            </thead>
        </table>
    </div>
<script>
    
    $(document).ready(function() {
            var LeaderboardTbls = '';
            var allLeaderboardDetails = [];
            var filterOption = true;

            LeaderboardTbls = $("#leaderboard").DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                paging: true,

                "ajax": {
                    "type": "POST",
                    "url": '{{ route("leaderboard.json") }}',
                    "data": function(d) {
                        d._token = $('meta[name="csrf-token"]').attr('content'),
                            d.filter = filterOption,
                            d.options = {
                                'filter' :  $('#filter').val()
                            }
                    },
                    "dataSrc": function(json) {
                        allLeaderboardDetails = json.data;
                        return json.data;
                    }
                },
                "columns": [
                    {
                        "data": "id"
                    },
                    {
                        "data": "full_name"
                    },
                    {
                        "data": "total_points"
                    },
                    {
                        "data": "rank"
                    },
                
                ],
                "order": [
                    [3, 'asc']
                ],
                'columnDefs': [{
                        'targets': [3], 
                        'orderable': false, 
                    }
                ]
            });

            $('#filter_btn').click(function() {
                LeaderboardTbls.ajax.reload();
            });

        
            $('#recalculateButton').click(function() {
                $.ajax({
                    url: "{{ route('leaderboard.recalculate') }}",
                    type: "POST", 
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        if (data.success) {
                            LeaderboardTbls.ajax.reload();
                            alert(data.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error: ", error); 
                        alert("Something went wrong.");
                    }
                });
            });
    });

</script>
@endsection