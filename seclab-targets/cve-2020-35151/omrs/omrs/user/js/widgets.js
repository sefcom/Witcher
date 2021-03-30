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
        { x: 10, y: 35 },
        { x: 11, y: 25 },
        { x: 12, y: 35 },
        { x: 13, y: 40 }
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
    renderer: 'bar',
    max: 80,
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
        { x: 10, y: 35 },
        { x: 11, y: 25 },
        { x: 12, y: 35 },
        { x: 13, y: 40 }
      ],
      color: '#17A2B8'
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
    renderer: 'line',
    max: 80,
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
        { x: 10, y: 35 },
        { x: 11, y: 25 },
        { x: 12, y: 35 },
        { x: 13, y: 40 }
      ],
      color: '#28A745'
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


  // Google Maps
  var styleBlueWater = [{
    'featureType': 'administrative',
    'elementType': 'labels.text.fill',
    'stylers': [{
      'color': '#444444'
    }]
  }, {
    'featureType': 'landscape',
    'elementType': 'all',
    'stylers': [{
      'color': '#f2f2f2'
    }]
  }, {
    'featureType': 'poi',
    'elementType': 'all',
    'stylers': [{
      'visibility': 'off'
    }]
  }, {
    'featureType': 'road',
    'elementType': 'all',
    'stylers': [{
      'saturation': -100
    }, {
      'lightness': 45
    }]
  }, {
    'featureType': 'road.highway',
    'elementType': 'all',
    'stylers': [{
      'visibility': 'simplified'
    }]
  }, {
    'featureType': 'road.arterial',
    'elementType': 'labels.icon',
    'stylers': [{
      'visibility': 'off'
    }]
  }, {
    'featureType': 'transit',
    'elementType': 'all',
    'stylers': [{
      'visibility': 'off'
    }]
  }, {
    'featureType': 'water',
    'elementType': 'all',
    'stylers': [{
      'color': '#0866C6'
    }, {
      'visibility': 'on'
    }]
  }];

  var map1 = GMaps.staticMapURL({
    size: [600, 400],
    zoom: 14,
    lat: 40.702247,
    lng: -73.996349,
    key: 'AIzaSyAEt_DBLTknLexNbTVwbXyq2HSf2UbRBU8',
    styles: styleBlueWater
  });

  $('<img class="img-fluid" alt="">').attr('src', map1).appendTo('#map1');


  var styleMapBox = [{
    'featureType': 'water',
    'stylers': [{
      'saturation': 43
    }, {
      'lightness': -11
    }, {
      'hue': '#0088ff'
    }]
  }, {
    'featureType': 'road',
    'elementType': 'geometry.fill',
    'stylers': [{
      'hue': '#ff0000'
    }, {
      'saturation': -100
    }, {
      'lightness': 99
    }]
  }, {
    'featureType': 'road',
    'elementType': 'geometry.stroke',
    'stylers': [{
      'color': '#808080'
    }, {
      'lightness': 54
    }]
  }, {
    'featureType': 'landscape.man_made',
    'elementType': 'geometry.fill',
    'stylers': [{
      'color': '#ece2d9'
    }]
  }, {
    'featureType': 'poi.park',
    'elementType': 'geometry.fill',
    'stylers': [{
      'color': '#ccdca1'
    }]
  }, {
    'featureType': 'road',
    'elementType': 'labels.text.fill',
    'stylers': [{
      'color': '#767676'
    }]
  }, {
    'featureType': 'road',
    'elementType': 'labels.text.stroke',
    'stylers': [{
      'color': '#ffffff'
    }]
  }, {
    'featureType': 'poi',
    'stylers': [{
      'visibility': 'off'
    }]
  }, {
    'featureType': 'landscape.natural',
    'elementType': 'geometry.fill',
    'stylers': [{
      'visibility': 'on'
    }, {
      'color': '#b8cb93'
    }]
  }, {
    'featureType': 'poi.park',
    'stylers': [{
      'visibility': 'on'
    }]
  }, {
    'featureType': 'poi.sports_complex',
    'stylers': [{
      'visibility': 'on'
    }]
  }, {
    'featureType': 'poi.medical',
    'stylers': [{
      'visibility': 'on'
    }]
  }, {
    'featureType': 'poi.business',
    'stylers': [{
      'visibility': 'simplified'
    }]
  }];

  var map2 = GMaps.staticMapURL({
    size: [600, 400],
    zoom: 14,
    lat: 40.702247,
    lng: -73.996349,
    key: 'AIzaSyAEt_DBLTknLexNbTVwbXyq2HSf2UbRBU8',
    styles: styleMapBox
  });

  $('<img class="img-fluid" alt="">').attr('src', map2).appendTo('#map2');

  var styleShadesOfGrey = [{
    'featureType': 'water',
    'elementType': 'geometry.fill',
    'stylers': [{
      'color': '#d3d3d3'
    }]
  },{
    'featureType': 'transit',
    'stylers': [{
      'color': '#808080'
    },{
      'visibility': 'off'
    }]
  },{
    'featureType': 'road.highway',
    'elementType': 'geometry.stroke',
    'stylers': [{
      'visibility': 'on'
    },{
      'color': '#b3b3b3'
    }]
  },{
    'featureType': 'road.highway',
    'elementType': 'geometry.fill',
    'stylers': [{
      'color': '#ffffff'
    }]
  },{
    'featureType': 'road.local',
    'elementType': 'geometry.fill',
    'stylers': [{
      'visibility': 'on'
    },{
      'color': '#ffffff'
    },{
      'weight': 1.8
    }]
  },{
    'featureType': 'road.local',
    'elementType': 'geometry.stroke',
    'stylers': [{
      'color': '#d7d7d7'
    }]
  },{
    'featureType': 'poi',
    'elementType': 'geometry.fill',
    'stylers': [{
      'visibility': 'on'
    },{
      'color': '#ebebeb'
    }]
  },{
    'featureType': 'administrative',
    'elementType': 'geometry',
    'stylers': [{
      'color': '#a7a7a7'
    }]
  },{
    'featureType': 'road.arterial',
    'elementType': 'geometry.fill',
    'stylers': [{
      'color': '#ffffff'
    }]
  },{
    'featureType': 'road.arterial',
    'elementType': 'geometry.fill',
    'stylers': [{
      'color': '#ffffff'
    }]
  },{
    'featureType': 'landscape',
    'elementType': 'geometry.fill',
    'stylers': [{
      'visibility': 'on'
    },{
      'color': '#efefef'
    }]
  },{
    'featureType': 'road',
    'elementType': 'labels.text.fill',
    'stylers': [{
      'color': '#696969'
    }]
  },{
    'featureType': 'administrative',
    'elementType': 'labels.text.fill',
    'stylers': [{
      'visibility': 'on'
    },{
      'color': '#737373'
    }]
  },{
    'featureType': 'poi',
    'elementType': 'labels.icon',
    'stylers': [{
      'visibility': 'off'
    }]
  },{
    'featureType': 'poi',
    'elementType': 'labels',
    'stylers': [{
      'visibility': 'off'
    }]
  },{
    'featureType': 'road.arterial',
    'elementType': 'geometry.stroke',
    'stylers': [{
      'color': '#d6d6d6'
    }]
  },{
    'featureType': 'road',
    'elementType': 'labels.icon',
    'stylers': [{
      'visibility': 'off'
    }]
  },{
    'featureType': 'poi',
    'elementType': 'geometry.fill',
    'stylers': [{
      'color': '#dadada'
    }]
  }];

  var map3 = GMaps.staticMapURL({
    size: [600, 400],
    zoom: 14,
    lat: 40.702247,
    lng: -73.996349,
    key: 'AIzaSyAEt_DBLTknLexNbTVwbXyq2HSf2UbRBU8',
    styles: styleShadesOfGrey
  });

  $('<img class="img-fluid" alt="">').attr('src', map3).appendTo('#map3');

});
