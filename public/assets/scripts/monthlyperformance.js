let visitorchartpreview = document.getElementById('visitorchartpreview').getContext('2d');

let visitorchartpreviewstats = new Chart(visitorchartpreview, {
    type: 'line',
    data: {
        labels: chartDays, // Use PHP-generated days array
        datasets: [{
            label: 'Visitors',
            data: chartVisitors, // Use PHP-generated visitors array
            borderColor: '#102E47',
            backgroundColor: 'rgba(16, 46, 71, 0.1)',
            fill: true,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true // This is crucial for height control
    }
});