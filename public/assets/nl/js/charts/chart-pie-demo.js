// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

// Pie Chart Example
var ctx = document.getElementById("myPieChart");
// DP
// ids
var accPCtry0 = document.getElementById("accessionPerCountry0");
var accPCtry1 = document.getElementById("accessionPerCountry1");
var accPCtry2 = document.getElementById("accessionPerCountry2");

// labels
var accPCtry0Label = accPCtry0.getAttribute('data-name');
var accPCtry1Label = accPCtry1.getAttribute('data-name');
var accPCtry2Label = accPCtry2.getAttribute('data-name');
var accPCtryLabels = [accPCtry0Label, accPCtry1Label, accPCtry2Label];

// values
var accPCtry0Value = accPCtry0.getAttribute('data-value');
var accPCtry1Value = accPCtry1.getAttribute('data-value');
var accPCtry2Value = accPCtry2.getAttribute('data-value');
var accPCtryValues = [accPCtry0Value, accPCtry1Value, accPCtry2Value]; 

var myPieChart = new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: accPCtryLabels,
    datasets: [{
      data: accPCtryValues,
      backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
      hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
      hoverBorderColor: "rgba(234, 236, 244, 1)",
    }],
  },
  options: {
    maintainAspectRatio: false,
    tooltips: {
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      caretPadding: 10,
      responsive:true,
    },
    legend: {
      display: true
    },
    
    cutoutPercentage: 80,
  },
});
