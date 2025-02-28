let visitorchartpreview = document.getElementById('visitorchartpreview').getContext('2d');

let visitorchartpreviewstats = new Chart(visitorchartpreview, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
        datasets: [{
            label: 'Visitors',
            data:[100, 200, 300, 600, 300, 600, 700]
        }]
    },
    options: {}
 });

 