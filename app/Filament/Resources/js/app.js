document.addEventListener('DOMContentLoaded', function () {
    Livewire.on('get-location', () => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                let latitude = position.coords.latitude;
                let longitude = position.coords.longitude;

                // Isi field latitude dan longitude di form Filament
                document.querySelector('input[name="latitude"]').value = latitude;
                document.querySelector('input[name="longitude"]').value = longitude;
            }, function() {
                alert('Tidak dapat mengakses lokasi perangkat.');
            });
        } else {
            alert('Geolocation tidak didukung oleh browser ini.');
        }
    });
});
