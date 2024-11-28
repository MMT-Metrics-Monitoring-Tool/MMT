// this file is for creating the charts for the Charts/index.ctp files

function setPageChartOptions () {
    Highcharts.setOptions({
        accessibility: {
            enabled: false
        },
        chart: {
            backgroundColor: {
                linearGradient: [0, 0, 0, 300],
                stops: [[0, 'rgb(217, 217, 255)'], [1, 'rgb(255, 255, 255)']],
            }
        },
        title: {
            y: 20,
            align: 'center',
            styleFont: '18px Metrophobic, Arial, sans-serif',
            styleColor: '#0099ff',
        }
    });
};

function createValueChart(earnedValueData, earnedValueSeries, captionText) {
    document.addEventListener('DOMContentLoaded', function () {
        var chart = Highcharts.chart("valuewrapperJS", {
            chart: {
                type: 'line',
            },
            title: {
                text: 'Earned value chart - estimated parts stay in budget',
            },
            legend: {
                itemstyle: {
                    color: '#222',
                },
                backgroundColor: {
                    linearGradient: [0, 0, 0, 25],
                    stops: [[0, 'rgb(217, 217, 217)'], [1, 'rgb(255, 255, 255)']],
                },
            },
            xAxis: {
                title: {
                    text: 'Week number',
                },
                categories: earnedValueData[0]['weekList'],
            },
            yAxis: {
                title: {
                    text: 'Cost (hours)',
                }
            },
            series: earnedValueSeries,
            caption: {
                text: captionText,
            },
            plotOptions: {
                area: {
                    marker: {
                        enabled: false,
                    }
                }
            },
            tooltip: {
                formatter: function() {return 'Cost: ' +' <b>'+
                    Highcharts.numberFormat(this.y, 0) +
                    '</b><br/>Week number ' +
                    this.x +'<br/>Line: ' +
                    this.series.name;}
            }
        });
    });
}

function createValueChart2(earnedValueData2, earnedValueSeries2, captionText2) {
    document.addEventListener('DOMContentLoaded', function () {
        const chart = Highcharts.chart('valuewrapperJS2', {
            chart: {
                type: 'line'
            },
            title: {
                text: 'Earned value chart - estimated parts based on actual data',
            },
            legend: {
                itemstyle: {
                    color: '#222',
                },
                backgroundColor: {
                    linearGradient: [0, 0, 0, 25],
                    stops: [[0, 'rgb(217, 217, 217)'], [1, 'rgb(255, 255, 255)']],
                },
            },
            xAxis: {
                categories: earnedValueData2[0]['weekList'],
                title: {
                    text: 'Week number',
                }
            },
            yAxis: {
                title: {
                    text: 'Cost (hours)',
                }
            },
            series: earnedValueSeries2,    
            caption: {
                text: captionText2,
            },
            plotOptions: {
                area: {
                    marker: {
                        enabled: false,
                    },
                },
            },
            tooltip: {
                formatter: function() {return 'Cost: ' +' <b>'+
                Highcharts.numberFormat(this.y, 0) +'</b><br/>Week number '+ this.x +'<br/>Line: ' + this.series.name;}
            }
        });
    });
}

function createPhaseChart (weeklyreports, phaseData) {
    document.addEventListener('DOMContentLoaded', function () {
        var chart = Highcharts.chart("phasewrapperJS", {
            chart: {
                type: 'area'
            },
            title: {
                text: 'Phases',
            },
            chart: {
                backgroundColor: {
                    linearGradient: [0, 0, 0, 300],
                    stops: [[0, 'rgb(217, 217, 255)'], [1, 'rgb(255, 255, 255)']],
                },
            },
            legend: {
                itemstyle: {
                    color: '#222',
                },
                backgroundColor: {
                    linearGradient: [0, 0, 0, 25],
                    stops: [[0, 'rgb(217, 217, 217)'], [1, 'rgb(255, 255, 255)']],
                },
            },
            xAxis: {
                title: {
                    text: 'Week number',
                },
                categories: weeklyreports['weeks'],
            },
            yAxis: {
                title: {
                    text: 'Total number of phases',
                }
            },
            plotOptions: {
                area: {
                    marker: {
                        enabled: false,
                    },
                },
            },
            series: [{
                name: 'Total phases planned',
                data: phaseData['phaseTotal'],
                type: 'area',
            },
            {
                name: 'Phase',
                data: phaseData['phase'],
                type: 'area',
            }
            ],
            tooltip: {
                formatter: function() {return this.series.name +' <b>'+ Highcharts.numberFormat(this.y, 0) 
                    +'</b><br/>Week number '+ this.x;}
            }
        });
    });
}

function createReqInNumbersChart(reqData, weeklyreports) {
    document.addEventListener('DOMContentLoaded', function () {
        const chart = Highcharts.chart('reqwrapperJS', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Requirements',
            },
            subtitle: {
                text: 'in numbers',
            },
            legend: {
                enabled: true,
                layout: 'horizontal',
                align: 'center',
                verticalAlign: 'bottom',
                itemStyle: {
                    color: '#222'
                },
                backgroundColor: {
                    linearGradient: [0, 0, 0, 25],
                    stops: [[0, 'rgb(217, 217, 217)'], [1, 'rgb(255, 255, 255)']],
                },
            },
            xAxis: {
                categories: weeklyreports['weeks'],
                title: {
                    text: 'Week number',
                }
            },
            yAxis: {
                title: {
                    text: 'Total number of requirements',
                }
            },
            series: [{
                name: 'Product Backlog',
                data: reqData['new'],
            },
            {
                name: 'Sprint Backlog',
                data: reqData['inprogress'],
            },
            {
                name: 'Done',
                data: reqData['closed'],
            },
            {
                name: 'Rejected',
                data: reqData['rejected'],
            }],
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0,
                }
            },
            tooltip: {
                formatter: function() {return this.y;}
            }
        });
    });
}

function createReqInPercentageChart(reqData, weeklyreports) {
    document.addEventListener('DOMContentLoaded', function () {
        const chart = Highcharts.chart('reqpercentwrapperJS', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Requirements',
            },
            subtitle: {
                text: 'in %',
            },
            legend: {
                itemStyle: {
                    color: '#222'
                },
                backgroundColor: {
                    linearGradient: [0, 0, 0, 25],
                    stops: [[0, 'rgb(217, 217, 217)'], [1, 'rgb(255, 255, 255)']],
                },
            },
            xAxis: {
                categories: weeklyreports['weeks'],
                title: {
                    text: 'Week number',
                }
            },
            yAxis: {
                title: {
                    text: '%',
                }
            },
            series: [{
                name: 'Product Backlog',
                data: reqData['new'],
            },
            {
                name: 'Sprint Backlog',
                data: reqData['inprogress'],
            },
            {
                name: 'Done',
                data: reqData['closed'],
            },
            {
                name: 'Rejected',
                data: reqData['rejected'],
            }],
            plotOptions: {
                column: {
                    stacking: "percent",
                }
            },
            tooltip: {
                formatter: function() {return ''+ this.series.name +': '+ this.y +' ('+ Math.round(this.percentage) +'%)';}
            }
        });
    });
}

function createCommitChart(commitData, weeklyreports) {
    document.addEventListener('DOMContentLoaded', function () {
        const chart = Highcharts.chart('commitwrapperJS', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Commits',
            },
            subtitle: {
                text: 'in total',
            },
            legend: {
                enabled: false,
            },
            xAxis: {
                categories: weeklyreports['weeks'],
                title: {
                    text: 'Week number',
                }
            },
            yAxis: {
                title: {
                    text: 'Total number of commits',
                }
            },
            series: [{
                name: 'Commits',
                data: commitData['commits'],
            }],
            tooltip: {
                formatter: function() {return this.series.name +' produced <b>'
                + Highcharts.numberFormat(this.y, 0) +'</b><br/>Week number '+ this.x;}
            }
        });
    });
}

function createTestCaseChart(testcasedata, weeklyreports) {
    document.addEventListener('DOMContentLoaded', function () {
        var chart = Highcharts.chart("testcasewrapperJS", {
            chart: {
                type: 'area'
            },
            title: {
                text: 'Test cases',
            },
            chart: {
                backgroundColor: {
                linearGradient: [0, 0, 0, 300],
                stops: [[0, 'rgb(217, 217, 255)'], [1, 'rgb(255, 255, 255)']],
                },
            },
            legend: {
                itemstyle: {
                    color: '#222',
                },
                backgroundColor: {
                    linearGradient: [0, 0, 0, 25],
                    stops: [[0, 'rgb(217, 217, 217)'], [1, 'rgb(255, 255, 255)']],
                },
            },
            xAxis: {
                title: {
                    text: 'Week number',
                },
                categories: weeklyreports['weeks'],
            },
            yAxis: {
                title: {
                    text: 'Total number of test cases',
                }
            },
            series: [{
                name: 'Total test cases',
                data: testcasedata['testsTotal'],
                type: 'area',
            },
            {
                name: 'Passed test cases',
                data: testcasedata['testsPassed'],
                type: 'area',
            }],
            plotOptions: {
                area: {
                    marker: {
                        enabled: false
                    }
                }
            },
            tooltip: {
                formatter: function() {return this.series.name +' <b>'+
                Highcharts.numberFormat(this.y, 0) +'</b><br/>Week number '+ this.x;}
            }
        });
    });
}

function createHoursPerWeekChart(alltheweeks, hoursperweekdata) {
    document.addEventListener('DOMContentLoaded', function () {
        var chart = Highcharts.chart("hoursperweekwrapperJS", {
            chart: {
                type: 'line'
            },
            title: {
                text: 'Working hours',
            },
            subtitle: {
                text: 'per week',
            },
            chart: {
                backgroundColor: {
                linearGradient: [0, 0, 0, 300],
                stops: [[0, 'rgb(217, 217, 255)'], [1, 'rgb(255, 255, 255)']],
                },
            },
            legend: {
                enabled: false,
            },
            xAxis: {
                title: {
                
                    text: 'Week number',
                },
                categories: alltheweeks
            },
            yAxis: {
                title: {
                    text: 'Working hours',
                }
            },
            series: [{
                name: 'Week number',
                data: hoursperweekdata,
            }],
            plotOptions: {
                area: {
                    marker: {
                        enabled: false
                    }
                }
            },
            tooltip: {
                formatter: function() {return 'Total hours: ' +' <b>'
                + Highcharts.numberFormat(this.y, 0) +'</b><br/>Week number '+ this.x;}
            }
        });
    });
}

function createTotalHourChart(alltheweeks, totalhourdata) {
    document.addEventListener('DOMContentLoaded', function () {
        var chart = Highcharts.chart('totalhourwrapperJS', {
            chart: {
                type: 'line'
            },
            title: {
                text: 'Total hours',
            },
            subtitle: {
                text: 'cumulative',
            },
            chart: {
                backgroundColor: {
                linearGradient: [0, 0, 0, 300],
                stops: [[0, 'rgb(217, 217, 255)'], [1, 'rgb(255, 255, 255)']],
                },
            },
            legend: {
                enabled: false,
            },
            xAxis: {
                title: {
                    text: 'Week number',
                },
                categories: alltheweeks
            },
            yAxis: {
                title: {
                    text: 'Total amount of hours',
                }
            },
            series: [{
                name: 'Week number',
                data: totalhourdata,
            }],
            plotOptions: {
                area: {
                    marker: {
                        enabled: false
                    }
                }
            },
            tooltip: {
                formatter: function() {return 'Total hours at this point: ' +' <b>'
                + Highcharts.numberFormat(this.y, 0) +'</b><br/>Week number '+ this.x;}
            }
        });
    }); 
}

function createWorkingHoursTypeChart(hoursSeries) {
    document.addEventListener('DOMContentLoaded', function () {
        const chart = Highcharts.chart('hourswrapperJS', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Working hours categorized by type',
            },
            subtitle: {
                text: 'project total hours - not affected by time limits',
            },
            legend: {
                enabled: false,
            },
            xAxis: {
                categories: ['Documentation',
                            'Requirements',
                            'Design',
                            'Implementation',
                            'Testing',
                            'Meetings',
                            'Studying',
                            'Other',
                            'Lectures'],
            },
            yAxis: {
                title: {
                    text: 'Working hours',
                }
            },
            series: [{
                name: 'Hour types',
                data: hoursSeries,
            }],    
            plotOptions: {
                column: {
                    stacking: 'normal',
                    dataLabels: {
                        enabled: true,
                    },
                    style: {
                        textShadow: false,
                        color: '#444',
                        fontSize: '1.2em',
                    },
                }
            },
            tooltip: {
                formatter: function() {return 'Hour type total hours: ' +' <b>' +Highcharts.numberFormat(this.y, 0) 
                 +'</b><br/>Work type: '+ this.x;}
            }
        });
    });
}

function createWorkingHoursPieChart(seriesArray) {
    document.addEventListener('DOMContentLoaded', function () {
        var chart = Highcharts.chart("hourswrapper2JS", {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Working hours categorized by type',
            },
            subtitle: {
                text: "percentage of total",
            },
            legend: {
                enabled: true,
            },
            xAxis: {
                categories: [
                    'Documentation',
                    'Requirements',
                    'Design',
                    'Implementation',
                    'Testing',
                    'Meetings',
                    'Studying',
                    'Other',
                    'Lectures',
                ],
            },
            series: [{
                name: "Hour types",
                data: seriesArray,
                type: 'pie',
            }],
            plotOptions: {
                pie: {
                    dataLabels: {
                        formatter: function() {return this.series.chart.axes[0].categories[this.point.index] +
                                ': ' + Highcharts.numberFormat(this.y, 0) + '%';},
                    },
                },
            },
            tooltip: {
                formatter: function() {return 'Hour type percentage: ' +' <b>' +Highcharts.numberFormat(this.y, 0) 
                    +'</b><br/>Work type: '+ this.series.chart.axes[0].categories[this.point.index];}
            },
        });
    });
}

function createRiskProbabilityChart(weeklyreports, riskProbSeries) {
    document.addEventListener('DOMContentLoaded', function () {
        const chart = Highcharts.chart('risksprobwrapperJS', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Risks by Probability',
            },
            legend: {
                itemstyle: {
                    color: '#222',
                },
                backgroundColor: {
                    linearGradient: [0, 0, 0, 25],
                    stops: [[0, 'rgb(217, 217, 217)'], [1, 'rgb(255, 255, 255)']],
                },
            },
            xAxis: {
                categories: weeklyreports['weeks'],
                title: {
                    text: 'Week number',
                }
            },
            yAxis: {
                title: {
                    text: 'Probability',
                }
            },
            series: riskProbSeries,    

            plotOptions: {
                area: {
                    marker: {
                        enabled: false,
                    },
                },
            },
            tooltip: {
                formatter: function() {return this.series.name +' <b>'
                        + Highcharts.numberFormat(this.y, 0) +'</b><br/>Week number '+ this.x;}
            }
        });
    });
}

function createRiskSeverityChart(weeklyreports, riskSeveritySeries) {
    document.addEventListener('DOMContentLoaded', function () {
        const chart = Highcharts.chart('risksseveritywrapperJS', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Risks by Severity',
            },
            legend: {
                itemstyle: {
                    color: '#222',
                },
                backgroundColor: {
                    linearGradient: [0, 0, 0, 25],
                    stops: [[0, 'rgb(217, 217, 217)'], [1, 'rgb(255, 255, 255)']],
                },
            },
            xAxis: {
                categories: weeklyreports['weeks'],
                title: {
                    text: 'Week number',
                }
            },
            yAxis: {
                title: {
                    text: 'Severity',
                }
            },
            series: riskSeveritySeries,

            plotOptions: {
                area: {
                    marker: {
                        enabled: false,
                    },
                },
            },
            tooltip: {
                formatter: function() {return this.series.name +' <b>'
                        + Highcharts.numberFormat(this.y, 0) +'</b><br/>Week number '+ this.x;}
            }
        });
    });
}

function createRiskImpactChart(weeklyreports, riskImpactSeries) {
    document.addEventListener('DOMContentLoaded', function () {
        const chart = Highcharts.chart('risksimpactwrapperJS', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Risks by Impact',
            },
            legend: {
                itemstyle: {
                    color: '#222',
                },
                backgroundColor: {
                    linearGradient: [0, 0, 0, 25],
                    stops: [[0, 'rgb(217, 217, 217)'], [1, 'rgb(255, 255, 255)']],
                },
            },
            xAxis: {
                categories: weeklyreports['weeks'],
                title: {
                    text: 'Week number',
                }
            },
            yAxis: {
                title: {
                    text: 'Impact',
                }
            },
            series: riskImpactSeries,    

            plotOptions: {
                area: {
                    marker: {
                        enabled: false,
                    },
                },
            },
            tooltip: {
                formatter: function() {return this.series.name +' <b>'
                + Highcharts.numberFormat(this.y, 0) +'</b><br/>Week number '+ this.x;}
            }
        });
    });
}

function createRiskCombinedChart(weeklyreports, riskCombinedSeries) {
    document.addEventListener('DOMContentLoaded', function () {
        const chart = Highcharts.chart('riskscombinedwrapperJS', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Risks by Probability And Severity',
            },
            legend: {
                itemstyle: {
                    color: '#222',
                },
                backgroundColor: {
                    linearGradient: [0, 0, 0, 25],
                    stops: [[0, 'rgb(217, 217, 217)'], [1, 'rgb(255, 255, 255)']],
                },
            },
            xAxis: {
                categories: weeklyreports['weeks'],
                title: {
                    text: 'Week number',
                }
            },
            yAxis: {
                title: {
                    text: 'Value',
                }
            },
            series: riskCombinedSeries,    

            plotOptions: {
                area: {
                    marker: {
                        enabled: false,
                    },
                },
            },
            tooltip: {
                formatter: function() {return this.series.name +' <b>'
                + Highcharts.numberFormat(this.y, 0) +'</b><br/>Week number '+ this.x;}
            }
        });
    });
}

function createRiskRealizedChart(riskRealizedSeries) {
    document.addEventListener('DOMContentLoaded', function () {
        const chart = Highcharts.chart('risksrealizedwrapperJS', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Risks by Realizations',
            },
            legend: {
                itemstyle: {
                    color: '#222',
                },
                backgroundColor: {
                    linearGradient: [0, 0, 0, 25],
                    stops: [[0, 'rgb(217, 217, 217)'], [1, 'rgb(255, 255, 255)']],
                },
            },
            xAxis: {
                categories: riskRealizedSeries.map(item => item.name),
                title: {
                    text: 'Risk',
                }
            },
            yAxis: {
                title: {
                    text: 'Realizations',
                }
            },
            series: [{
                name: 'Realization Count',
                data: riskRealizedSeries.map(item => item.data),
            }],

            plotOptions: {
                area: {
                    marker: {
                        enabled: false,
                    },
                },
            },
        });
    });
}

function createProjectRisksRealizedChart(weeklyreports, riskProjectRealizedSeries) {
    document.addEventListener('DOMContentLoaded', function () {
        const chart = Highcharts.chart('risksprojectrealizedwrapperJS', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Risks by Realization and Week Number',
            },
            legend: {
                itemstyle: {
                    color: '#222',
                },
                backgroundColor: {
                    linearGradient: [0, 0, 0, 25],
                    stops: [[0, 'rgb(217, 217, 217)'], [1, 'rgb(255, 255, 255)']],
                },
            },
            xAxis: {
                categories: weeklyreports['weeks'],
                title: {
                    text: 'Week number',
                }
            },
            yAxis: {
                title: {
                    text: 'Realizations',
                }
            },
            series: riskProjectRealizedSeries,

            plotOptions: {
                area: {
                    marker: {
                        enabled: false,
                    },
                },
            },
            tooltip: {
                formatter: function() {return this.series.name +' <b>'
                + Highcharts.numberFormat(this.y, 0) +'</b><br/>Week number '+ this.x;}
            }
        });
    });
}

function createPredictiveProjectChart(predictiveProjectData, seriesArray) {
    document.addEventListener('DOMContentLoaded', function () {
        const chart = Highcharts.chart('predictiveProjectChartWrapperJS', {
            chart: {
                type: 'line'
            },
            title: {
                text: 'Working hours prediction',
            },
            subtitle: {
                text: 'per week',
            },
            legend: {
                itemStyle: {
                    color: '#222',
                },
                backgroundColor: {
                    linearGradient: [0, 0, 0, 25],
                    stops: [[0, 'rgb(217, 217, 217)'], [1, 'rgb(255, 255, 255)']],
                },
            },
            xAxis: {
                categories: predictiveProjectData[0]['weekList'],
                title: {
                    text: 'Week number',
                }
            },
            yAxis: {
                title: {
                    text: 'Working hours',
                }
            },
            series: seriesArray,
            tooltip: {
                formatter: function() {return 'Total hours: ' +' <b>'+
                    Highcharts.numberFormat(this.y, 0) +'</b><br/>Week number '+ 
                    this.x +'<br/>Line: ' + this.series.name;}
            },
            colors: ['#fc0303', '#036ffc', '#fc08f8'],
            plotOptions: {
                area: {
                    marker: {
                        enabled: false
                    }
                }
            }
        });
    });
}

function createHoursComparisonChart(allTheWeeksData, hoursComparisonSeries) {
    document.addEventListener('DOMContentLoaded', function () {
        const chart = Highcharts.chart('hourscomparisonwrapperJS', {
            chart: {
                type: 'line',
                backgroundColor: {
                    linearGradient: [0, 0, 0, 300],
                    stops: [[0, 'rgb(217, 217, 255)'], [1, 'rgb(255, 255, 255)']],
                },
            },
            title: {
                text: 'Total hours of each project',
                y: 20,
                align: 'center',
                styleFont: '18px Metrophobic, Arial, sans-serif',
                styleColor: '#0099ff',
            },
            legend: {
                itemstyle: {
                    color: '#222',
                },
                backgroundColor: {
                    linearGradient: [0, 0, 0, 25],
                    stops: [[0, 'rgb(217, 217, 217)'], [1, 'rgb(255, 255, 255)']],
                },
            },
            xAxis: {
                categories: allTheWeeksData,
                title: {
                    text: 'Week number',
                }
            },
            yAxis: {
                title: {
                    text: 'Total amount of hours',
                }
            },
            series: hoursComparisonSeries,    

            plotOptions: {
                area: {
                    marker: {
                        enabled: false,
                    },
                },
            },
            tooltip: {
                formatter: function() {return 'Total hours at this point ' +' <b>'+ Highcharts.numberFormat(this.y, 0) 
                +'</b><br/>Week number '+ this.x +'<br/>Project: ' + this.series.name;}
            }
        });
    });
}
function createPredictiveMemberChart(predictiveMemberData, seriesArray) {
    document.addEventListener('DOMContentLoaded', function () {
        const chart = Highcharts.chart('predictiveMemberChartWrapperJS', {
            chart: {
                type: 'line'
            },
            title: {
                text: 'Working hours prediction',
            },
            subtitle: {
                text: 'per week',
            },
            legend: {
                itemStyle: {
                    color: '#222',
                },
                backgroundColor: {
                    linearGradient: [0, 0, 0, 25],
                    stops: [[0, 'rgb(217, 217, 217)'], [1, 'rgb(255, 255, 255)']],
                },
            },
            xAxis: {
                categories: predictiveMemberData[0]['weekList'],
                title: {
                    text: 'Week number',
                }
            },
            yAxis: {
                title: {
                    text: 'Working hours',
                }
            },
            series: seriesArray,
            tooltip: {
                formatter: function() {return 'Total hours: ' +' <b>'+
                    Highcharts.numberFormat(this.y, 0) +'</b><br/>Week number '+ 
                    this.x +'<br/>Line: ' + this.series.name;}
            },
            colors: ['#fc0303', '#036ffc', '#fc08f8'],
            plotOptions: {
                area: {
                    marker: {
                        enabled: false
                    }
                }
            }
        });
    });
}
