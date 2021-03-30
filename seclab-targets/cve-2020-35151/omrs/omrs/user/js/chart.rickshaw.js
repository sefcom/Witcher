$(function(){
  'use strict'


  var bar1 = new Rickshaw.Graph({
    element: document.querySelector('#chartBar1'),
    renderer: 'bar',
    max: 80,
    series: [{
      data: [
        { x: 0, y: 40 },
        { x: 1, y: 49 },
        { x: 2, y: 38 },
        { x: 3, y: 30 },
        { x: 4, y: 32 },
        { x: 5, y: 40 },
        { x: 6, y: 20 },
        { x: 7, y: 10 },
        { x: 8, y: 20 },
        { x: 9, y: 25 },
        { x: 10, y: 35 },
        { x: 11, y: 20 },
        { x: 12, y: 40 },
        { x: 13, y: 25 }
      ],
      color: '#FB9337'
    }]
  });
  bar1.render();

  // Responsive Mode
  new ResizeSensor($('.br-mainpanel'), function(){
    bar1.configure({
      width: $('#chartBar1').width(),
      height: $('#chartBar1').height()
    });
    bar1.render();
  });


  /*********************** BAR 2 *********************/

  var bar2 = new Rickshaw.Graph({
    element: document.querySelector('#chartBar2'),
    renderer: 'bar',
    max: 80,
    series: [{
      data: [
        { x: 0, y: 40 },
        { x: 1, y: 49 },
        { x: 2, y: 38 },
        { x: 3, y: 30 },
        { x: 4, y: 32 },
        { x: 5, y: 40 },
        { x: 6, y: 20 },
        { x: 7, y: 10 },
        { x: 8, y: 20 },
        { x: 9, y: 25 },
        { x: 10, y: 35 },
        { x: 11, y: 20 },
        { x: 12, y: 40 },
        { x: 13, y: 25 }
      ],
      color: '#218bc2'
    }]
  });

  bar2.render();

  // Responsive Mode
  new ResizeSensor($('.br-mainpanel'), function(){
    bar2.configure({
      width: $('#chartBar2').width(),
      height: $('#chartBar2').height()
    });
    bar2.render();
  });


  /************* STACKED BAR1 *************/

  var stacked1 = new Rickshaw.Graph({
    element: document.querySelector('#chartStackedBar1'),
    renderer: 'bar',
    max: 100,
    series: [{
      data: [
        { x: 0, y: 20 },
        { x: 1, y: 30 },
        { x: 2, y: 10 },
        { x: 3, y: 15 },
        { x: 4, y: 10 },
        { x: 5, y: 20 },
        { x: 6, y: 15 },
        { x: 7, y: 20 },
        { x: 8, y: 25 },
        { x: 9, y: 20 },
        { x: 10, y: 25 },
        { x: 11, y: 10 },
        { x: 12, y: 15 },
        { x: 13, y: 10 }
      ],
      color: '#324463'
    },
    {
      data: [
        { x: 0, y: 10 },
        { x: 1, y: 10 },
        { x: 2, y: 15 },
        { x: 3, y: 20 },
        { x: 4, y: 12 },
        { x: 5, y: 20 },
        { x: 6, y: 10 },
        { x: 7, y: 15 },
        { x: 8, y: 20 },
        { x: 9, y: 25 },
        { x: 10, y: 15 },
        { x: 11, y: 10 },
        { x: 12, y: 20 },
        { x: 13, y: 25 }
      ],
      color: '#FB9337'
    },
    {
      data: [
        { x: 0, y: 10 },
        { x: 1, y: 15 },
        { x: 2, y: 15 },
        { x: 3, y: 20 },
        { x: 4, y: 32 },
        { x: 5, y: 30 },
        { x: 6, y: 20 },
        { x: 7, y: 10 },
        { x: 8, y: 20 },
        { x: 9, y: 25 },
        { x: 10, y: 35 },
        { x: 11, y: 20 },
        { x: 12, y: 20 },
        { x: 13, y: 25 }
      ],
      color: '#7CBDDF'
    }]
  });
  stacked1.render();

  // Responsive Mode
  new ResizeSensor($('.br-mainpanel'), function(){
    stacked1.configure({
      width: $('#chartStackedBar1').width(),
      height: $('#chartStackedBar1').height()
    });
    stacked1.render();
  });


  /*************** STACKED 2 **********************/
  var stacked2 = new Rickshaw.Graph({
    element: document.querySelector('#chartStackedBar2'),
    renderer: 'bar',
    max: 100,
    series: [{
      data: [
        { x: 0, y: 20 },
        { x: 1, y: 30 },
        { x: 2, y: 10 },
        { x: 3, y: 15 },
        { x: 4, y: 10 },
        { x: 5, y: 20 },
        { x: 6, y: 15 },
        { x: 7, y: 20 },
        { x: 8, y: 25 },
        { x: 9, y: 20 },
        { x: 10, y: 25 },
        { x: 11, y: 10 },
        { x: 12, y: 15 },
        { x: 13, y: 10 }
      ],
      color: '#384250'
    },
    {
      data: [
        { x: 0, y: 10 },
        { x: 1, y: 10 },
        { x: 2, y: 15 },
        { x: 3, y: 20 },
        { x: 4, y: 12 },
        { x: 5, y: 20 },
        { x: 6, y: 10 },
        { x: 7, y: 15 },
        { x: 8, y: 20 },
        { x: 9, y: 25 },
        { x: 10, y: 15 },
        { x: 11, y: 10 },
        { x: 12, y: 20 },
        { x: 13, y: 25 }
      ],
      color: '#FB9337'
    },
    {
      data: [
        { x: 0, y: 10 },
        { x: 1, y: 15 },
        { x: 2, y: 15 },
        { x: 3, y: 20 },
        { x: 4, y: 32 },
        { x: 5, y: 30 },
        { x: 6, y: 20 },
        { x: 7, y: 10 },
        { x: 8, y: 20 },
        { x: 9, y: 25 },
        { x: 10, y: 35 },
        { x: 11, y: 20 },
        { x: 12, y: 20 },
        { x: 13, y: 25 }
      ],
      color: '#E34856'
    }]
  });
  stacked2.render();

  // Responsive Mode
  new ResizeSensor($('.br-mainpanel'), function(){
    stacked2.configure({
      width: $('#chartStackedBar2').width(),
      height: $('#chartStackedBar2').height()
    });
    stacked2.render();
  });


  /***************** MULTIPLE BOX ********************/

  var multibar = new Rickshaw.Graph({
    element: document.querySelector('#chartMultiBar1'),
    renderer: 'bar',
    stack: false,
    max: 60,
    series: [{
      data: [
        { x: 0, y: 20 },
        { x: 1, y: 25 },
        { x: 2, y: 10 },
        { x: 3, y: 15 },
        { x: 4, y: 20 },
        { x: 5, y: 40 },
        { x: 6, y: 15 },
        { x: 7, y: 40 },
        { x: 8, y: 25 }
      ],
      color: '#324463'
    },
    {
      data: [
        { x: 0, y: 10 },
        { x: 1, y: 30 },
        { x: 2, y: 45 },
        { x: 3, y: 30 },
        { x: 4, y: 42 },
        { x: 5, y: 20 },
        { x: 6, y: 30 },
        { x: 7, y: 15 },
        { x: 8, y: 20 }
      ],
      color: '#FB9337'
    }]
  });

  multibar.render();

  // Responsive Mode
  new ResizeSensor($('.br-mainpanel'), function(){
    multibar.configure({
      width: $('#chartMultiBar1').width(),
      height: $('#chartMultiBar1').height()
    });
    multibar.render();
  });


  /**************** MULTI BAR 2 ***************/

  var multibar2 = new Rickshaw.Graph({
    element: document.querySelector('#chartMultiBar2'),
    renderer: 'bar',
    stack: false,
    max: 60,
    series: [{
      data: [
        { x: 0, y: 20 },
        { x: 1, y: 25 },
        { x: 2, y: 10 },
        { x: 3, y: 15 },
        { x: 4, y: 20 },
        { x: 5, y: 40 },
        { x: 6, y: 15 },
        { x: 7, y: 40 },
        { x: 8, y: 25 }
      ],
      color: '#5B93D3'
    },
    {
      data: [
        { x: 0, y: 10 },
        { x: 1, y: 30 },
        { x: 2, y: 45 },
        { x: 3, y: 30 },
        { x: 4, y: 42 },
        { x: 5, y: 20 },
        { x: 6, y: 30 },
        { x: 7, y: 15 },
        { x: 8, y: 20 }
      ],
      color: '#FB9337'
    },
    {
      data: [
        { x: 0, y: 20 },
        { x: 1, y: 50 },
        { x: 2, y: 25 },
        { x: 3, y: 10 },
        { x: 4, y: 22 },
        { x: 5, y: 40 },
        { x: 6, y: 10 },
        { x: 7, y: 25 },
        { x: 8, y: 40 }
      ],
      color: '#324463'
    }]
  });
  multibar2.render();

  // Responsive Mode
  new ResizeSensor($('.br-mainpanel'), function(){
    multibar2.configure({
      width: $('#chartMultiBar2').width(),
      height: $('#chartMultiBar2').height()
    });
    multibar2.render();
  });


  /*************** LINE BAR 1 *****************/

  var line1 = new Rickshaw.Graph({
    element: document.querySelector('#chartLine1'),
    renderer: 'line',
    max: 80,
    series: [{
      data: [
        { x: 0, y: 40 },
        { x: 1, y: 49 },
        { x: 2, y: 38 },
        { x: 3, y: 30 },
        { x: 4, y: 32 },
        { x: 5, y: 40 },
        { x: 6, y: 20 },
        { x: 7, y: 10 },
        { x: 8, y: 20 },
        { x: 9, y: 25 },
        { x: 10, y: 35 },
        { x: 11, y: 20 },
        { x: 12, y: 40 },
        { x: 13, y: 25 }
      ],
      color: '#FB9337'
    }]
  });
  line1.render();

  // Responsive Mode
  new ResizeSensor($('.br-mainpanel'), function(){
    line1.configure({
      width: $('#chartLine1').width(),
      height: $('#chartLine1').height()
    });
    line1.render();
  });


  /***************** LINE CHART 2 **********************/

  var line2 = new Rickshaw.Graph({
    element: document.querySelector('#chartLine2'),
    renderer: 'line',
    stack: false,
    max: 60,
    series: [{
      data: [
        { x: 0, y: 20 },
        { x: 1, y: 25 },
        { x: 2, y: 10 },
        { x: 3, y: 15 },
        { x: 4, y: 20 },
        { x: 5, y: 40 },
        { x: 6, y: 15 },
        { x: 7, y: 40 },
        { x: 8, y: 25 }
      ],
      color: '#324463'
    },
    {
      data: [
        { x: 0, y: 10 },
        { x: 1, y: 30 },
        { x: 2, y: 45 },
        { x: 3, y: 30 },
        { x: 4, y: 42 },
        { x: 5, y: 20 },
        { x: 6, y: 30 },
        { x: 7, y: 15 },
        { x: 8, y: 20 }
      ],
      color: '#FB9337'
    }]
  });
  line2.render();

  // Responsive Mode
  new ResizeSensor($('.br-mainpanel'), function(){
    line2.configure({
      width: $('#chartLine2').width(),
      height: $('#chartLine2').height()
    });
    line2.render();
  });


  /******************** AREA CHART 1 ******************/

  var area1 = new Rickshaw.Graph({
    element: document.querySelector('#chartArea1'),
    renderer: 'area',
    max: 80,
    series: [{
      data: [
        { x: 0, y: 40 },
        { x: 1, y: 49 },
        { x: 2, y: 38 },
        { x: 3, y: 30 },
        { x: 4, y: 32 },
        { x: 5, y: 40 },
        { x: 6, y: 20 },
        { x: 7, y: 10 },
        { x: 8, y: 20 },
        { x: 9, y: 25 },
        { x: 10, y: 35 },
        { x: 11, y: 20 },
        { x: 12, y: 40 },
        { x: 13, y: 25 }
      ],
      color: '#FB9337'
    }]
  });
  area1.render();

  // Responsive Mode
  new ResizeSensor($('.br-mainpanel'), function(){
    area1.configure({
      width: $('#chartArea1').width(),
      height: $('#chartArea1').height()
    });
    area1.render();
  });


  /****************** AREA CHART 2 ******************/
  var area2 = new Rickshaw.Graph({
    element: document.querySelector('#chartArea2'),
    renderer: 'area',
    stack: false,
    max: 80,
    series: [{
      data: [
        { x: 0, y: 45 },
        { x: 1, y: 60 },
        { x: 2, y: 55 },
        { x: 3, y: 40 },
        { x: 4, y: 52 },
        { x: 5, y: 45 },
        { x: 6, y: 35 },
        { x: 7, y: 25 },
        { x: 8, y: 30 },
        { x: 9, y: 45 },
        { x: 10, y: 40 },
        { x: 11, y: 30 },
        { x: 12, y: 45 },
        { x: 13, y: 35 }
      ],
      color: '#324463'
    },
    {
      data: [
        { x: 0, y: 40 },
        { x: 1, y: 49 },
        { x: 2, y: 38 },
        { x: 3, y: 30 },
        { x: 4, y: 32 },
        { x: 5, y: 40 },
        { x: 6, y: 20 },
        { x: 7, y: 10 },
        { x: 8, y: 20 },
        { x: 9, y: 25 },
        { x: 10, y: 35 },
        { x: 11, y: 20 },
        { x: 12, y: 40 },
        { x: 13, y: 25 }
      ],
      color: '#FB9337'
    }]
  });
  area2.render();

  // Responsive Mode
  new ResizeSensor($('.br-mainpanel'), function(){
    area2.configure({
      width: $('#chartArea2').width(),
      height: $('#chartArea2').height()
    });
    area2.render();
  });


});
