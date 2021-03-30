$(function(){
  'use strict'

  /***************** LINE CHARTS *****************/
  $('#sparkline1').sparkline('html', {
    width: 200,
    height: 70,
    lineColor: '#0083CD',
    fillColor: false
  });

  $('#sparkline2').sparkline('html', {
    width: 200,
    height: 70,
    lineColor: '#B654C3',
    fillColor: false
  });


  /************** AREA CHARTS ********************/

  $('#sparkline3').sparkline('html', {
    width: 200,
    height: 70,
    lineColor: '#0083CD',
    fillColor: 'rgba(0,131,205,0.2)',
  });

  $('#sparkline4').sparkline('html', {
    width: 200,
    height: 70,
    lineColor: '#B654C3',
    fillColor: 'rgba(182,84,195,0.2)'
  });


  /******************* BAR CHARTS *****************/

  $('#sparkline5').sparkline('html', {
    type: 'bar',
    barWidth: 10,
    height: 70,
    barColor: '#0083CD',
    chartRangeMax: 12
  });

  $('#sparkline6').sparkline('html', {
    type: 'bar',
    barWidth: 10,
    height: 70,
    barColor: '#B654C3',
    chartRangeMax: 12
  });

  /***************** STACKED BAR CHARTS ****************/

  $('#sparkline7').sparkline('html', {
    type: 'bar',
    barWidth: 10,
    height: 70,
    barColor: '#0083CD',
    chartRangeMax: 12
  });

  $('#sparkline7').sparkline([4,5,6,7,4,5,8,7,6,6,4,7,6,4,7], {
    composite: true,
    type: 'bar',
    barWidth: 10,
    height: 70,
    barColor: '#11546F',
    chartRangeMax: 12
  });

  $('#sparkline8').sparkline('html', {
    type: 'bar',
    barWidth: 10,
    height: 70,
    barColor: '#E97B9E',
    chartRangeMax: 12
  });

  $('#sparkline8').sparkline([4,5,6,7,4,5,8,7,6,6,4,7,6,4,7], {
    composite: true,
    type: 'bar',
    barWidth: 10,
    height: 70,
    barColor: '#92288D',
    chartRangeMax: 12
  });


  /**************** PIE CHART ****************/

  $('#sparkline9').sparkline('html', {
    type: 'pie',
    height: 70,
    sliceColors: ['#F4C62B', '#F6931E', '#8CC63E']
  });

  $('#sparkline10').sparkline('html', {
    type: 'pie',
    height: 70,
    sliceColors: ['#F4C62B', '#F6931E', '#8CC63E', '#93268F', '#EB1E79', '#828BC4', '#E97A9B', '#0083CD']
  });

});
