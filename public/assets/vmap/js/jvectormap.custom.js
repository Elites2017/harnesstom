$(function() {
    "use strict";

    var $accessionData = document.getElementById('accessionData');
    var accPercountry = jQuery.parseJSON($accessionData.getAttribute('data-val'));
    // object
    var accPerCountryData = {};
    accPercountry.forEach(element => {
        accPerCountryData[element.iso3] = element.accQty +' ('+ element.percentage +'%)';
    }); 
    jQuery('#world-map-markers').vectorMap({
        map: 'world_mill_en',
        series: {
            regions: [{
            values: accPerCountryData,
            scale: ['#A8EEFF', '#DD00F2', '#F9A71A4'],
            normalizeFunction: 'polynomial'
            }]
        },
        onRegionTipShow: function(e, el, code){
            el.html(el.html()+': '+accPerCountryData[code]);
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
            latLng: [53.0000, 9.0000],
            name: 'Europe'

        }],
        hoverOpacity: null,
        normalizeFunction: 'linear',
        scaleColors: ['#b6d6ff', '#005ace'],
        selectedColor: '#c9dfaf',
        selectedRegions: [],
        showTooltip: true,
        // onRegionClick: function(element, code, region) {
        //     console.log("EL ", code, ' ', region, ' el ', element)
        //     var message = 'You clicked "' +
        //         region +
        //         '" which has the code: ' +
        //         code.toUpperCase();

        //     alert(message);
        // }
    });

});