document.getElementById('downloadPdf').addEventListener('click', function () {
    const filename = window.location.pathname.split('/').pop();
    console.log(filename);
    const reportType = filename.split('.')[0]; // Assuming the filename is the report type

    switch (reportType) {
        case 'busiestmonths':
            generateBusiestMonthsReport();
            break;
        case 'monthlyperformance':
            generateMonthlyPerformanceReport();
            break;
        case 'toptouristsite':
            generateTopTouristSiteReport();
            break;
        case 'visitor':
            generateVisitorReport();
            break;
        case 'tourperformance':
            generateTourPerformanceReport();
            break;
        default:
            console.error('Unknown report type');
    }
});

function generateBusiestMonthsReport() {
    const doc = new jsPDF();
    const canvas = document.getElementById('busiestmonths');
    const imgData = canvas.toDataURL('image/png');

    doc.text('Busiest Months Statistics', 10, 10);
    doc.addImage(imgData, 'PNG', 10, 20, 180, 100); // Adjust the width and height as needed

    doc.text('Total Completed Tours This Year:', 10, 130);
    doc.text('449', 10, 140);

    doc.save('BusiestMonthsStatistics.pdf');
}

function generateMonthlyPerformanceReport() {
    const doc = new jsPDF();
    // Add code to generate the monthly performance report
    doc.save('MonthlyPerformanceReport.pdf');
}

function generateTopTouristSiteReport() {
    const doc = new jsPDF();
    const canvas = document.getElementById('topsite');
    const imgData = canvas.toDataURL('image/png');

    doc.text('Busiest Months Statistics', 10, 10);
    doc.addImage(imgData, 'PNG', 10, 20, 180, 100); // Adjust the width and height as needed

    doc.text('Total Visitors This Year:', 10, 130);
    doc.text('6000', 10, 140);

    doc.save('TopTouristSiteReport.pdf');
}

function generateVisitorReport() {
    const doc = new jsPDF();
    // Add code to generate the visitor report
    doc.save('VisitorReport.pdf');
}

function generateTourPerformanceReport() {
    const doc = new jsPDF();
    // Add code to generate the tour performance report
    doc.save('TourPerformanceReport.pdf');
}
