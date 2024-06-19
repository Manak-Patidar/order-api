@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-6 offset-3">
        <div class="card">
            <div class="card-header">
                <p class="text-center">Geo Fencing</p>
            </div> 
            <div class="card-body">
                <form id="geofence-form">
                    Latitude: <input type="text" id="latitude" name="latitude" value="37.7750" class="form-control"><br>
                    Longitude: <input type="text" id="longitude" name="longitude" value="-122.4183" class="form-control"><br>
                    <button type="button" class="btn btn-primary" onclick="checkGeofence()">Check Geofence</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    function checkGeofence() {
        const latitude = parseFloat(document.getElementById('latitude').value);
        const longitude = parseFloat(document.getElementById('longitude').value);

        fetch('/geofencing/check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    latitude,
                    longitude,
                }),
            })
            .then(response => response.json())
            .then(data => {
                alert(data.inside_geofence ? 'Inside Geofence' : 'Outside Geofence');
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
</script>
@stop