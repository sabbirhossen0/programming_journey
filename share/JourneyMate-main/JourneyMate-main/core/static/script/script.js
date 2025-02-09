document.addEventListener('DOMContentLoaded', function () {
    const seats = document.querySelectorAll('.seat');
    seats.forEach(seat => {
        seat.addEventListener('click', function () {
            // Skip booked seats
            if (!seat.classList.contains('booked')) {
                seat.classList.toggle('selected');

                // Update selected seats
                updateSelectedSeats();
            }
        });
    });
});

function updateSelectedSeats() {
    const selectedSeats = [];
    const seats = document.querySelectorAll('.seat.selected');

    seats.forEach(seat => {
        selectedSeats.push(seat.dataset.seatNumber);
    });

    // Update hidden input
    document.getElementById('selected-seats').value = selectedSeats.join(',');
}
