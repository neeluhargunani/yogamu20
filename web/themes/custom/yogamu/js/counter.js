// Function to update counters on the server
function updateCounters() {
  var currentDate = getCurrentDate();

  // Send AJAX request to update counters on the server
  fetch('/update-counters', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      date: currentDate,
    }),
  })
    .then(response => response.json())
    .then(data => {
      // Update HTML elements with counter values and add Bootstrap classes
      document.getElementById("todayCount").innerHTML = data.today;
      document.getElementById("todayCount").classList.add("badge", "badge-primary");

      document.getElementById("yesterdayCount").innerHTML = data.yesterday;
      document.getElementById("yesterdayCount").classList.add("badge", "badge-secondary");

      document.getElementById("weeklyCount").innerHTML = data.weekly;
      document.getElementById("weeklyCount").classList.add("badge", "badge-info");

      document.getElementById("monthlyCount").innerHTML = data.monthly;
      document.getElementById("monthlyCount").classList.add("badge", "badge-success");

      document.getElementById("totalHits").innerHTML = data.total;
      document.getElementById("totalHits").classList.add("badge", "badge-danger");
    })
    .catch(error => {
      console.error('Error updating counters:', error);
    });
}

// Update counters on page load
updateCounters();

// Adding onClick event listener
resetButton.addEventListener("click", () => {
  // Send AJAX request to reset counters on the server
  fetch('/reset-counters', {
    method: 'POST',
  })
    .then(response => response.json())
    .then(data => {
      // Update HTML elements with reset counter values
      document.getElementById("todayCount").innerHTML = data.today;
      document.getElementById("yesterdayCount").innerHTML = data.yesterday;
      document.getElementById("weeklyCount").innerHTML = data.weekly;
      document.getElementById("monthlyCount").innerHTML = data.monthly;
      document.getElementById("totalHits").innerHTML = data.total;
    })
    .catch(error => {
      console.error('Error resetting counters:', error);
    });
});
