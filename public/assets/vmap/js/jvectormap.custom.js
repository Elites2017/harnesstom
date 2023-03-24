$(function() {
    "use strict";

    var $accessionData = document.getElementById('accessionData');
    var arr = jQuery.parseJSON($accessionData.getAttribute('data-val'));
    var gdpData = {};
    arr.forEach(element => {
        gdpData[element.iso2] = element.accQty;
        //console.log("Element.. ", gdpData);    
    }); 

    jQuery('#world-map-markers').vectorMap({
        map: 'world_mill_en',
        series: {
            regions: [{
            values: gdpData,
            scale: ['#C8EEFF', '#0071A4', '#AFEEFF', '#0F971A4'],
            normalizeFunction: 'polynomial'
            }]
        },
        onRegionTipShow: function(e, el, code){
            el.html(el.html()+': '+gdpData[code]+' ');
        },
        backgroundColor: 'transparent',
        borderColor: '#818181',
        borderOpacity: 0.25,
        borderWidth: 1,
        zoomOnScroll: true,
        color: '#8b94d6',
        regionStyle: {
            initial: {
                fill: '#8b94d6'
            }
        },
        markerStyle: {
            initial: {
                r: 9,
                'fill': '#fff',
                'fill-opacity': 1,
                'stroke': '#000',
                'stroke-width': 5,
                'stroke-opacity': 0.4
            },
        },
        enableZoom: true,
        hoverColor: '#8b94d6',
        markers: [{
            latLng: [21.00, 78.00],
            name: 'India'

        }],
        hoverOpacity: null,
        normalizeFunction: 'linear',
        scaleColors: ['#b6d6ff', '#005ace'],
        selectedColor: '#c9dfaf',
        selectedRegions: [],
        showTooltip: true,
        onRegionClick: function(element, code, region) {
            var message = 'You clicked "' +
                region +
                '" which has the code: ' +
                code.toUpperCase();

            alert(message);
        }
    });

});