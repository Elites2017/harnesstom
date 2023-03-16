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
var accPCtry0Value = parseInt(accPCtry0.getAttribute('data-value'));
var accPCtry1Value = parseInt(accPCtry1.getAttribute('data-value'));
var accPCtry2Value = parseInt(accPCtry2.getAttribute('data-value'));
var accPCtryValues = [accPCtry0Value, accPCtry1Value, accPCtry2Value]; 
console.log("type of ", accPCtryValues);

// Doughnut Chart With Labels
var ctxP = document.getElementById("myPieChart").getContext('2d');
var myPieChart = new Chart(ctxP, {
  plugins: [ChartDataLabels],
  type: 'doughnut',
  data: {
    labels: accPCtryLabels,
    datasets: [{
      data: accPCtryValues,
      backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
      hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
      hoverBorderColor: "rgba(234, 236, 244, 1)",
    }]
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
    plugins: {
      datalabels: {
        formatter: (value, ctx) => {
          let sum = 0;
          let dataArr = ctx.chart.data.datasets[0].data;
          dataArr.map(data => {
            sum += data;
          });
          let percentage = (value * 100 / sum).toFixed(2) + "%";
          return percentage;
        },
        color: 'white',
        labels: {
          title: {
            font: {
              size: '16'
            }
          }
        }
      }
    }
  }
});