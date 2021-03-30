$(function(){

  'use strict';

  var rs1 = new Rickshaw.Graph({
    element: document.querySelector('#rs1'),
    renderer: 'area',
    max: 80,
    stroke: true,
    series: [{
      data: [
        { x: 0, y: 20 },
        { x: 1, y: 10 },
        { x: 2, y: 15 },
        { x: 3, y: 10 },
        { x: 4, y: 15 },
        { x: 5, y: 5 },
        { x: 6, y: 15 },
        { x: 7, y: 10 },
        { x: 8, y: 20 },
        { x: 9, y: 25 },
        { x: 10, y: 35 }
      ],
      color: '#fdc390',
      stroke: '#FB9337'
    }]
  });
  rs1.render();

  // Responsive Mode
  new ResizeSensor($('.am-mainpanel'), function(){
    rs1.configure({
      width: $('#rs1').width(),
      height: $('#rs1').height()
    });
    rs1.render();
  });

  var rs2 = new Rickshaw.Graph({
    element: document.querySelector('#rs2'),
    renderer: 'area',
    max: 80,
    stroke: true,
    series: [{
      data: [
        { x: 0, y: 10 },
        { x: 1, y: 15 },
        { x: 2, y: 18 },
        { x: 3, y: 15 },
        { x: 4, y: 20 },
        { x: 5, y: 10 },
        { x: 6, y: 15 },
        { x: 7, y: 10 },
        { x: 8, y: 20 },
        { x: 9, y: 25 },
        { x: 10, y: 30 }
      ],
      color: '#8bd0db',
      stroke: '#17A2B8'
    }]
  });
  rs2.render();

  // Responsive Mode
  new ResizeSensor($('.am-mainpanel'), function(){
    rs2.configure({
      width: $('#rs2').width(),
      height: $('#rs2').height()
    });
    rs2.render();
  });

  var rs3 = new Rickshaw.Graph({
    element: document.querySelector('#rs3'),
    renderer: 'area',
    max: 80,
    stroke: true,
    series: [{
      data: [
        { x: 0, y: 20 },
        { x: 1, y: 10 },
        { x: 2, y: 15 },
        { x: 3, y: 10 },
        { x: 4, y: 15 },
        { x: 5, y: 5 },
        { x: 6, y: 15 },
        { x: 7, y: 10 },
        { x: 8, y: 20 },
        { x: 9, y: 25 },
        { x: 10, y: 20 }
      ],
      color: '#a5ecb5',
      stroke: '#28A745'
    }]
  });
  rs3.render();

  // Responsive Mode
  new ResizeSensor($('.am-mainpanel'), function(){
    rs3.configure({
      width: $('#rs3').width(),
      height: $('#rs3').height()
    });
    rs3.render();
  });


  $.plot("#f2", [{
    data: [[0, 3], [2, 8], [4, 5], [6, 13],[8,5], [10,7],[12,8], [14,10]],
    bars: {
      show: true,
      lineWidth: 0,
      fillColor: '#2D3A50'
    }
  },{
    data: [[1, 5], [3, 7], [5, 10], [7, 7],[9,9], [11,5],[13,4], [15,6]],
    bars: {
      show: true,
      lineWidth: 0,
      fillColor: '#FB9337'
    }
  }], {
    grid: {
      borderWidth: 1,
      borderColor: '#D9D9D9'
    },
    yaxis: {
      tickColor: '#d9d9d9',
      font: {
        color: '#666',
        size: 10
      }
    },
    xaxis: {
      tickColor: '#d9d9d9',
      font: {
        color: '#666',
        size: 10
      }
    }
  });


  var newCust = [[0, 2], [1, 3], [2,6], [3, 5], [4, 7], [5, 8], [6, 10]];
  var retCust = [[0, 1], [1, 2], [2,5], [3, 3], [4, 5], [5, 6], [6,9]];

  var plot = $.plot($('#f1'),[{
    data: newCust,
    label: 'New Customer',
    color: '#2D3A50'
  },
  {
    data: retCust,
    label: 'Returning Customer',
    color: '#FB9337'
  }],
  {
    series: {
      lines: {
        show: true,
        lineWidth: 1
      },
      shadowSize: 0
    },
    points: {
      show: false,
    },
    legend: {
      noColumns: 1,
      position: 'nw'
    },
    grid: {
      hoverable: true,
      clickable: true,
      borderColor: '#ddd',
      borderWidth: 0,
      labelMargin: 5,
      backgroundColor: '#fff'
    },
    yaxis: {
      min: 0,
      max: 15,
      color: '#eee',
      font: {
        size: 10,
        color: '#999'
      }
    },
    xaxis: {
      color: '#eee',
      font: {
        size: 10,
        color: '#999'
      }
    }
  });

});
