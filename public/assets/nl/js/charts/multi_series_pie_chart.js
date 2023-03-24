var randomScalingFactor = function() {
    return Math.round(Math.random() * 100);
};
var randomColorFactor = function() {
    return Math.round(Math.random() * 255);
};

var ctx = document.getElementById("myChart").getContext("2d");
var myChart = new Chart(ctx, {
  type: 'pie',
  data: {
    labels: ["Day 1", "Day 2", "Day 3", "NEW DAY"],
    datasets: [{
        label: 'working',
        data: [10, 5, 6, 40],
        backgroundColor: ["red", "blue", "pink", "yellow"],
        renderText: [null, "Hello", null]
      },
      {
        label: 'sleeping',
        data: [4, 2, 7],
        backgroundColor: ["red", "blue", "pink"],
        renderText: ["No", "Hello", null]
      },
      {
        label: 'sleeping',
        data: [4, 2, 7],
        backgroundColor: ["red", "blue", "pink"],
        renderText: ["No", "Hello", null]
      },
      {
        label: 'sleeping',
        data: [4, 2, 7],
        backgroundColor: ["red", "blue", "pink"],
        renderText: ["No", "Hello", null]
      },
    ]
  },
  options: {
    plugins: {
      datalabels: {
        color: 'white',
        formatter: (val, ctx) => (ctx.dataset.renderText[ctx.dataIndex])
      }
    }
  }
});