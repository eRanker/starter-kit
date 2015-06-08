Highcharts.setOptions({
    plotOptions: {
        series: {
            animation: false
        }
    }
});

function niceToggle(id) {
    jQuery('#' + id + ' i.expandtoggle').toggleClass('show-details');
    if (jQuery('#' + id + ' i.expandtoggle').hasClass('show-details')) {
        jQuery('#' + id + ' i.expandtoggle').removeClass('fa-plus').addClass('fa-minus');
    } else {
        jQuery('#' + id + ' i.expandtoggle').removeClass('fa-minus').addClass('fa-plus');
    }
    jQuery('#' + id + ' .factor-info').toggle();
}
var reportScoreCircle;

function reportinit() {

    try {
        //backlinkspiecharts();
        setTimeout(backlinkspiecharts, 1000);
    } catch (e) {
        console.log(e);
    }
    try {
        //anchorschart();
        setTimeout(anchorschart, 1000);
    } catch (e) {
        console.log(e);
    }

    try {
        //speedanalysispiechartsrequest();
        setTimeout(speedanalysispiechartsrequest, 500);
    } catch (e) {
    }

    try {
        //speedanalysispiechartsweight();
        setTimeout(speedanalysispiechartsweight, 500);
    } catch (e) {
    }




    try {
        if (jQuery('#circles') && jQuery('#circles').attr('data-circle-started') != "1") {
            reportScoreCircle = Circles.create({
                id: 'circles',
                radius: 70,
                value: jQuery('#circles').attr('data-percent'),
                maxValue: 100,
                width: 10,
                text: "",
                colors: ['#E5E5E5', '#0281C4'],
                duration: 400,
                wrpClass: 'circles-wrp',
                textClass: 'circles-text'
            });
            jQuery('#circles').attr('data-circle-started', 1);
        }
    } catch (e) {
        console.log(e);
    }

    try {
        jQuery(".erankertooltip[title]").tooltip({
            show: {
                effect: "slideDown",
                delay: 250
            },
            position: {
                my: "left top",
                at: "left bottom"
            },
            placement: "bottom"
        });
    } catch (e) {
        console.log(e);
    }

    try {
        var divServerLocationMap = '#mapserverlocation';
        serverLocationMapInit(divServerLocationMap, jQuery(divServerLocationMap).attr("data-serverlocation-latitude"),
                jQuery(divServerLocationMap).attr("data-serverlocation-longitude"), jQuery(divServerLocationMap).attr("data-serverlocation-accuracy"),
                jQuery(divServerLocationMap).attr("data-serverlocation-title"), jQuery(divServerLocationMap).html());
    } catch (e) {
        console.log(e);
    }

}

function serverLocationMapInit(div, lat, lon, accuracy, title, content) {

    var mapJQueryObj = jQuery(div + '[data-mapready="false"]');

    if (mapJQueryObj.length) {


        // Create map
        var mapServerLocation = new GMaps({
            div: div,
            scrollwheel: true,
            zoom: 7,
            lat: lat,
            lng: lon
        });

        if (lat !== null && lon !== null) {
            // Create infoWindow
            var infoWindowServerLocation = new google.maps.InfoWindow({
                content: content
            });


            //Fix accuracy
            if (accuracy <= 0) {
                accuracy = 30000;
            }

            // Add the circle for this city to the map.
            new google.maps.Circle({
                center: new google.maps.LatLng(lat, lon),
                radius: accuracy,
                strokeColor: "#4293e5",
                strokeOpacity: 0.3,
                strokeWeight: 1,
                fillColor: "#4293e5",
                fillOpacity: 0.2,
                map: mapServerLocation.map
            });


            var markerServerLocation = mapServerLocation.addMarker({
                lat: lat,
                lng: lon,
                title: title,
                icon: "//www.eranker.com/content/themes/eranker/img/datacenter_location-32.png",
                infoWindow: infoWindowServerLocation
            });

            // This opens the infoWindow
            infoWindowServerLocation.open(mapServerLocation, markerServerLocation);

        }

    }

    mapJQueryObj.attr('data-mapready', 'true');
}



function downloadFactorsHTML() {

    reportDownloadRetries++;
    if (reportDownloadRetries > 120 || jQuery(".erfactor[data-factorready='0']").size <= 0) { // retry until finished or 10 minutes
        reportinit();
        return;
    }
    if (console) {
        console.log("Downloading missing factors...");
    }
    var factorList = "";
    jQuery(".erfactor[data-factorready='0']").each(function (idx, el) {
        factorList += jQuery(el).attr('data-id') + ",";
    });
    factorList = factorList.substring(0, factorList.length - 1);


    if (factorList === "") {
        if (console) {
            console.log("Finished download the factors data.");
        }
        reportinit();
        return;
    }
    var jsonURL = updateQueryStringParameter(updateQueryStringParameter(window.location.href, "factors", factorList), "ajax", "1");

    jQuery.getJSON(jsonURL, function (data) {
        updateReportScore(data.score);

        if (data.status !== 'DONE') {
            //Try download again in 5 seconds
            setTimeout(function dfact() {
                downloadFactorsHTML();
            }, 3000);
        } else {
            if (console) {
                console.log("Finished download the factors data.");
            }
        }
        jQuery.each(data, function (index, value) {
            if (index === "score" || index === "status") {
                return;
            }
            var section = jQuery('.erfactor[data-id="' + index + '"]');
            if (index == 'backlinks') {
                section.find(".factor-data").html('');
                section.find(".factor-data-backlinks").html(value.html);
            } else {
                section.find(".factor-data").html(value.html);
            }

            jQuery(".printscreen").html('<img id="sitescreen" alt="Website Screenshot" src="' + data.score.thumbnail + '">');

            var statusclass = "info";
            switch (value.status) {
                case "RED":
                case "MISSING":
                    statusclass = 'times';
                    break;
                case "ORANGE":
                    statusclass = 'minus';
                    break;
                case "GREEN":
                    statusclass = 'check';
                    break;
                case "NEUTRAL":
                    statusclass = 'info';
                    break;
                default:
                    statusclass = "question-circle";
                    break;
            }
            var statuscolor = value.status.toLowerCase();

            section.find(".factor-name-inside").html('<i class="fa fa-' + statusclass + ' ' + statuscolor + '"></i> ' + value.friendly_name);

        });
        reportinit();

    }).fail(function () {
        //If an error happens, try download again in 10 seconds
        setTimeout(function dfact() {
            downloadFactorsHTML();
        }, 5000);
        reportinit();
    });
}


function updateQueryStringParameter(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    if (uri.match(re)) {
        return uri.replace(re, '$1' + key + "=" + value + '$2');
    }
    else {
        return uri + separator + key + "=" + value;
    }
}

var reportDownloadRetries = 0;

function updateReportScore(value) {
    if (console) {
        console.log("Updating scores...");
    }
    reportScoreCircle.update(Math.round(value.percentage), 300);

    jQuery('.superreport-seo .overall-score .reportfinalscore').html(Math.round(value.percentage));
    jQuery('#rating-stars .rating-stars').css('width', (Math.round(value.percentage) / 10 * 10.6) + 'px');
    //Multi colors not implemented yet....
    //reportScoreCircle.updateColors(['#E5E5E5', '#0281C4']);

    var total = value.factors.green + value.factors.orange + value.factors.red + value.factors.missing;

    jQuery('.score-table .factors-score .green .factor-score span').html(value.factors.green);
    jQuery('.score-table .factors-score .green .factorbar').css('width', Math.round((value.factors.green / total) * 100) + '%');
    jQuery('.score-table .factors-score .orange .factor-score span').html(value.factors.orange);
    jQuery('.score-table .factors-score .orange .factorbar').css('width', Math.round((value.factors.orange / total) * 100) + '%');
    jQuery('.score-table .factors-score .red .factor-score span').html(value.factors.red);
    jQuery('.score-table .factors-score .red .factorbar').css('width', Math.round((value.factors.red / total) * 100) + '%');
}


function printSeoReport() {
    jQuery("#erreport").print();
}

function backlinkspiecharts() {

    jQuery(".backlinkchart[data-chartready='false']").each(function (i, e) {

        jQuery(this).highcharts({
            chart: {
                animation: false,
                plotBackgroundColor: 'transparent',
                plotBorderWidth: null,
                plotShadow: false,
                backgroundColor: 'transparent'
            },
            title: {
                text: jQuery(this).attr('data-title1') + ' vs ' + jQuery(this).attr('data-title2'),
                margin: 5
            },
            colors: ['#0281C4', '#FF9000', '#04B974', '#F45B5B'],
            credits: {
                enabled: false
            },
            subtitle: {
                text: "Total: " + (parseInt(jQuery(this).attr('data-value1')) + parseInt(jQuery(this).attr('data-value2')))
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'bottom',
                enabled: true
            },
            exporting: {
                enabled: false
            },
            tooltip: {
                pointFormat: '<b>{point.y} ({point.percentage:.1f}%)</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false,
                        format: '<b>{point.name}</b>: {point.y}',
                        distance: 20,
                        color: 'black'
                    }
                }
            },
            series: [{
                    type: 'pie',
                    name: jQuery(this).attr('data-title1') + ' ' + jQuery(this).attr('data-title2'),
                    showInLegend: true,
                    data: [
                        {
                            name: jQuery(this).attr('data-title1'),
                            y: parseInt(jQuery(this).attr('data-value1')),
                            sliced: true,
                            selected: true
                        },
                        {
                            name: jQuery(this).attr('data-title2'),
                            y: parseInt(jQuery(this).attr('data-value2')),
                            sliced: false,
                            selected: false
                        }
                    ]
                }]
        });

        jQuery(this).attr("data-chartready", "true");
    });

}

function anchorschart() {

    jQuery(".anchorschart[data-chartready='false']").each(function (i, e) {

        jQuery(this).highcharts({
            chart: {
                animation: false,
                plotBackgroundColor: 'transparent',
                plotBorderWidth: null,
                plotShadow: false,
                backgroundColor: 'transparent'
            },
            title: {
                text: 'test',
                margin: 5
            },
            colors: ['#0281C4', '#FF9000', '#04B974', '#F45B5B'],
            credits: {
                enabled: false
            },
            subtitle: {
                text: "Total: " + (parseInt(jQuery(this).attr('total')))
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'bottom',
                enabled: true
            },
            exporting: {
                enabled: false
            },
            tooltip: {
                pointFormat: '<b>{point.y} ({point.percentage:.1f}%)</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false,
                        format: '<b>{point.name}</b>: {point.y}',
                        distance: 20,
                        color: 'black'
                    }
                }
            },
            series: [{
                    type: 'pie',
                    name: 'test name',
                    showInLegend: true,
                    data: [
                        {
                            name: jQuery(this).attr('data-anchor-0'),
                            y: parseInt(jQuery(this).attr('data-backlinks-0')),
                            sliced: true,
                            selected: true
                        },
                        {
                            name: jQuery(this).attr('data-anchor-1'),
                            y: parseInt(jQuery(this).attr('data-backlinks-1')),
                            sliced: true,
                            selected: true
                        },
                        {
                            name: jQuery(this).attr('data-anchor-2'),
                            y: parseInt(jQuery(this).attr('data-backlinks-2')),
                            sliced: true,
                            selected: true
                        },
                        {
                            name: jQuery(this).attr('data-anchor-3'),
                            y: parseInt(jQuery(this).attr('data-backlinks-3')),
                            sliced: true,
                            selected: true
                        },
                        {
                            name: jQuery(this).attr('data-anchor-4'),
                            y: parseInt(jQuery(this).attr('data-backlinks-4')),
                            sliced: true,
                            selected: true
                        },
                        {
                            name: jQuery(this).attr('data-anchor-5'),
                            y: parseInt(jQuery(this).attr('data-backlinks-5')),
                            sliced: true,
                            selected: true
                        },
                        {
                            name: jQuery(this).attr('data-anchor-5'),
                            y: parseInt(jQuery(this).attr('data-backlinks-5')),
                            sliced: true,
                            selected: true
                        },
                        {
                            name: jQuery(this).attr('data-anchor-6'),
                            y: parseInt(jQuery(this).attr('data-backlinks-6')),
                            sliced: true,
                            selected: true
                        },
                        {
                            name: jQuery(this).attr('data-anchor-7'),
                            y: parseInt(jQuery(this).attr('data-backlinks-7')),
                            sliced: true,
                            selected: true
                        },
                        {
                            name: jQuery(this).attr('data-anchor-8'),
                            y: parseInt(jQuery(this).attr('data-backlinks-8')),
                            sliced: true,
                            selected: true
                        },
                        {
                            name: jQuery(this).attr('data-anchor-9'),
                            y: parseInt(jQuery(this).attr('data-backlinks-9')),
                            sliced: true,
                            selected: true
                        }

                    ]
                }]
        });

        jQuery(this).attr("data-chartready", "true");
    });

}


var ssnotavailable_icon = "data:image/png;base64,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";
var ssloading_icon = "data:image/gif;base64,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";



jQuery(document).ready(function () {
    reportinit();
    downloadFactorsHTML();
});
