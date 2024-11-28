<script src="/js/highcharts.js"></script>
<script src="/js/exporting.js"></script>
<script src="/js/chartview.js"></script>
<?php use Cake\I18n\Time; ?>
<input type="hidden" name="mysession" id="mysession">
<body>
    <script>
        window.addEventListener('load', (e) => {
            let timestemp = new Date(e.timeStamp).getTime()
            console.log('loading time is', timestemp)
        })
    </script>
</body>
<div class="statistics">
        <h3><?= __('Edit limits') ?></h3> 
        <?= $this->Form->create() ?>
            <div id="chart-limits">
            <?php
                
                $weekMin = $this->request->getSession()->read('chart_limits')['weekmin'] ?? null;
                $weekMax = $this->request->getSession()->read('chart_limits')['weekmax'] ?? null;
                $yearMin = $this->request->getSession()->read('chart_limits')['yearmin'] ?? null;
                $yearMax = $this->request->getSession()->read('chart_limits')['yearmax'] ?? null;
                $time = Time::now();
                // Set min and max values for input fields
                echo $this->Form->control('weekmin', array('type' => 'number', 'min' => 1, 'max' => 52, 'value' => $weekMin));
                echo $this->Form->control('weekmax', array('type' => 'number', 'min' => 1, 'max' => 52, 'value' => $weekMax));
                echo $this->Form->control('yearmin', array('type' => 'number', 'min' => 2015, 'max' => $time->year, 'value' => $yearMin));
                echo $this->Form->control('yearmax', array('type' => 'number', 'min' => 2015, 'max' => $time->year, 'value' => $yearMax));
            ?>
            </div>
            <button>Submit</button>
        <?= $this->Form->end() ?>
</div>

<div class="metrics index large-9 medium-8 columns content float: left">

    <?php
    // Earned value chart is visible only to admins and supervisor at the moment
    if (($this->request->getSession()->read('is_admin') || $this->request->getSession()->read('is_supervisor')) && $this->request->getSession()->read('displayCharts')) { ?>
        <h4>Earned value</h4>
        <div class="chart">
            <div id="valuewrapperJS">
            </div>
        </div>
        <div class="chart">
            <div id="valuewrapperJS2">
            </div>
        </div> 
    <?php } ?>

    <h4>Requirements</h4>
    <div class="chart">
        <div id="reqwrapperJS">
        </div>
    </div>
    <div class="chart">
        <div id="reqpercentwrapperJS">
        </div>
    </div>

    <h4>Misc</h4>
    <div class="chart">
        <div id="phasewrapperJS">
        </div>
    </div>
    <div class="chart">
        <div id="commitwrapperJS">
        </div>
    </div>
    <div class="chart">
        <div id="testcasewrapperJS">
        </div>
    </div>

    <h4>Hours</h4>
    <div class="chart">
        <div id="hoursperweekwrapperJS">
        </div>
    </div>
    <div class="chart">
        <div id="totalhourwrapperJS">
        </div>
    </div>
    <div class="chart">
        <div id="hourswrapperJS">
        </div>
    </div>
    <div class="chart">
        <div id="hourswrapper2JS">
        </div>
    </div>

    <h4>Risks</h4>
    <div class="chart">
        <div id="risksprobwrapperJS">
        </div>
    </div>
    <div class="chart">
        <div id="risksseveritywrapperJS">
        </div>
    </div>
    <div class="chart">
        <div id="riskscombinedwrapperJS">
        </div>
    </div>
    <div class="chart">
        <div id="risksrealizedwrapperJS">
        </div>
    </div>
    <div class="chart">
        <div id="risksprojectrealizedwrapperJS">
        </div>
    </div>

    <?php
    // Total hours of projects chart which is only visible for admins and coaches.
    if ($this->request->getSession()->read('is_admin') || $this->request->getSession()->read('is_supervisor')) { ?>  
        <h4>Total hours of projects</h4>
        <div class="chart">
            <div id="hourscomparisonwrapperJS">
            </div>
        </div>
    <?php } ?>

    </div>

    <!-- Set formatting for the charts on page -->
    <script>
        setPageChartOptions();    
    </script>
    <!-- Earned value chart (HIGHCHARTS JS) -->
    <script>
        var earnedValueData=<?php echo json_encode($this->request->getSession()->read('earnedValueData'));?>;
        var earnedValueSeries = [];
        for (let i = 0; i < earnedValueData.length; i++) {
            earnedValueSeries.push({
                name: earnedValueData[i]['name'],
                data: earnedValueData[i]['values'],
                marker: earnedValueData[i]['marker'],
                type: earnedValueData[i]['type'],
                dashStyle: earnedValueData[i]['dashStyle'],
                lineWidth: earnedValueData[i]['lineWidth'],
                color: earnedValueData[i]['color'],
            });
        };
        var captionText = `Current week: ${earnedValueData[6]['currentWeek']}, `
                        + `Estimated 100% hours: week ${earnedValueData[6]['estimatedWeekFullHours']}, `
                        + `Estimated 100% readiness: week ${earnedValueData[6]['estimatedCompletionWeek']}, `
                        + `Planned 100% readiness: week ${earnedValueData[6]['plannedCompletionWeek']} `
                        + `<br/>DR (Degree of Readiness: ${earnedValueData[6]['DR']}, `
                        + `AC (Actual Costs): ${earnedValueData[6]['AC']} hours, `
                        + `BAC (Budget At Completion): ${earnedValueData[6]['BAC']} hours `
                        + `<br/>EAC (Estimated Actual Costs): ${earnedValueData[6]['EAC'].toFixed(1)} h, `
                        + `CPI (Cost Performance Index): ${earnedValueData[6]['CPI'].toFixed(2)}, `
                        + `SPI (Schedule Performance Index): ${earnedValueData[6]['SPI'].toFixed(2)}, `
                        + `<b>VAC (Variance At Completion): ${earnedValueData[6]['VAC'].toFixed(1)} h, `
                        + `Schedule variance at completion: ${earnedValueData[6]['SVAC'].toFixed(0)} weeks</b> `
                        + `<br/>Weeks used: ${earnedValueData[6]['weeksUsed']}, `
                        + `Weeks budgeted: ${earnedValueData[6]['weeksBudgeted']}, `
                        + `Weeks estimated: ${earnedValueData[6]['weeksEstimated']}`;

        // Create the chart in chartview.js
        createValueChart(earnedValueData, earnedValueSeries, captionText);
    </script>

    <!-- Earned value 2 chart (HIGHCHARTS JS) -->
    <script>
        var earnedValueData2=<?php echo json_encode($this->request->getSession()->read('earnedValueData2'));?>;
        earnedValueSeries2 = [];
        for (let i = 0; i < earnedValueData2.length; i++) {
            earnedValueSeries2.push({
                name: earnedValueData2[i]['name'],
                data: earnedValueData2[i]['values'],
                marker: earnedValueData2[i]['marker'],
                type: earnedValueData2[i]['type'],
                dashStyle: earnedValueData2[i]['dashStyle'],
                lineWidth: earnedValueData2[i]['lineWidth'],
                color: earnedValueData2[i]['color'],
            });
        };

        var captionText2 = `Current week: ${earnedValueData2[6]['currentWeek']}, `
                        + `Estimated 100% hours: week ${earnedValueData2[6]['estimatedWeekFullHours']}, `
                        + `Estimated 100% readiness: week ${earnedValueData2[6]['estimatedCompletionWeek']}, `
                        + `Planned 100% readiness: week ${earnedValueData2[6]['plannedCompletionWeek']} `
                        + `<br/>DR (Degree of Readiness: ${earnedValueData2[6]['DR']}, `
                        + `AC (Actual Costs): ${earnedValueData2[6]['AC']} hours, `
                        + `BAC (Budget At Completion): ${earnedValueData2[6]['BAC']} hours `
                        + `<br/>EAC (Estimated Actual Costs): ${earnedValueData2[6]['EAC'].toFixed(1)} h, `
                        + `CPI (Cost Performance Index): ${earnedValueData2[6]['CPI'].toFixed(2)}, `
                        + `SPI (Schedule Performance Index): ${earnedValueData2[6]['SPI'].toFixed(2)}, `
                        + `<b>VAC (Variance At Completion): ${earnedValueData2[6]['VAC'].toFixed(1)} h, `
                        + `Schedule variance at completion: ${earnedValueData2[6]['SVAC'].toFixed(0)} weeks</b> ` 
                        + `<br/>Weeks used: ${earnedValueData2[6]['weeksUsed']}, `
                        + `Weeks budgeted: ${earnedValueData2[6]['weeksBudgeted']}, `
                        + `Weeks estimated: ${earnedValueData2[6]['weeksEstimated']}`;

        // Create the chart earned value 2 in chartview.js
        createValueChart2(earnedValueData2, earnedValueSeries2, captionText2);
    </script>

    <!--<h4>Phase Chart (HIGHCHARTS JS)</h4>-->
    <script>
        var phaseData=<?php echo json_encode($this->request->getSession()->read('phaseData'));?>;
        var weeklyreports=<?php echo json_encode($this->request->getSession()->read('weeklyreports'));?>;

        // Create the chart in chartview.js
        createPhaseChart (weeklyreports, phaseData);
    </script>

    <!-- Both of the requirement charts (in numbers and in percentage) (HIGHCHARTS JS) -->
    <script>
        var reqData=<?php echo json_encode($this->request->getSession()->read('reqData'));?>;
        var weeklyreports=<?php echo json_encode($this->request->getSession()->read('weeklyreports'));?>;

        // Create the charts in chartview.js
        createReqInNumbersChart(reqData, weeklyreports);
        createReqInPercentageChart(reqData, weeklyreports);
    </script>

    <!-- Commit chart (HIGHCHARTS JS) -->
    <script>
        var commitData=<?php echo json_encode($this->request->getSession()->read('commitData'));?>;
        var weeklyreports=<?php echo json_encode($this->request->getSession()->read('weeklyreports'));?>;
        
        createCommitChart(commitData, weeklyreports);
    </script>

    <!--<h4>Test case chart (HIGHCHARTS JS)</h4>-->
    <script>
        var testcasedata=<?php echo json_encode($this->request->getSession()->read('testcasedata'));?>;
        var weeklyreports=<?php echo json_encode($this->request->getSession()->read('weeklyreports'));?>;
        // Create the chart in chartview.js
        createTestCaseChart(testcasedata, weeklyreports);
    </script>
    </br>

    <!--<h4>Working hours (HIGHCHARTS JS)</h4>-->
    <script>
        var hoursperweekdata=<?php echo json_encode($this->request->getSession()->read('hoursperweekdata'));?>;
        var alltheweeks=<?php echo json_encode($_SESSION['alltheweeks']);?>;

        // Create the chart in chartview.js
        createHoursPerWeekChart(alltheweeks, hoursperweekdata)
    </script>

    <!-- Total hours cumulative line chart -->
    <script>
        var totalhourdata=<?php echo json_encode($this->request->getSession()->read('totalhourdata'));?>;
        var alltheweeks=<?php echo json_encode($this->request->getSession()->read('alltheweeks'));?>;

        // Create the chart in chartview.js
        createTotalHourChart(alltheweeks, totalhourdata);
    </script>

    <!-- Working hours categorized by type chart (HIGHCHARTS JS) -->
    <script>
        var hoursData = <?php echo json_encode($this->request->getSession()->read('hoursData'));?>;
            var hoursSeries = [];
            for (let i = 1; i < 10; i++) {
                var hour = hoursData[i];
                hoursSeries.push(hour);
            };
        
        // Create the chart in chartview.js
        createWorkingHoursTypeChart(hoursSeries);
    </script>

    <!-- <h4>Working hours pie chart (HIGHCHARTS JS)</h4> -->
    <script>
        var hoursData_1=<?php echo json_encode($this->request->getSession()->read('hoursData_1'));?>;
        var seriesArray = [];
        var hoursSum = hoursData_1.reduce((a, b) => a + b, 0);
        if (hoursSum > 0) {
            for (let i = 0; i < hoursData_1.length; i++) {
            var number = parseInt((hoursData_1[i]/hoursSum * 100).toFixed(0));
            seriesArray.push(number);
            }
        } else {
            // Fill array with zeroes if there are no working hours
            seriesArray.fill(0, 0, 8);
        };
        createWorkingHoursPieChart(seriesArray);
    </script>

    <!-- Risk charts (probability, severity and combined) (HIGHCHARTS JS) -->
    <script>
        // Series for all of the risk charts (probability, severity and combined) are created here.
        var weeklyreports=<?php echo json_encode($this->request->getSession()->read('weeklyreports'));?>;
        var riskData=<?php echo json_encode($this->request->getSession()->read('riskData'));?>;
        var riskProbSeries = [];
        var riskSeveritySeries = [];
        var riskCombinedSeries = [];
        var riskRealizedSeries = [];
        var riskProjectRealizedSeries = [];
        for (let i = 0; i < riskData.length; i++) {
            riskProbSeries.push({
                name: riskData[i]['name'],
                data: riskData[i]['probability'],
            });
            riskSeveritySeries.push({
                name: riskData[i]['name'],
                data: riskData[i]['severity'],
            });
            riskCombinedSeries.push({
                name: riskData[i]['name'],
                data: riskData[i]['combined'],
            });
            riskProjectRealizedSeries.push({
                name: riskData[i]['name'],
                data: riskData[i]['total_realizations']
            })
            riskRealizedSeries.push({
                name: riskData[i]['name'],
                data: riskData[i]['realizations']
            })
        };

        createRiskProbabilityChart(weeklyreports, riskProbSeries);
        createRiskSeverityChart(weeklyreports, riskSeveritySeries);
        createRiskCombinedChart(weeklyreports, riskCombinedSeries);
        createRiskRealizedChart(riskProjectRealizedSeries);
        createProjectRisksRealizedChart(weeklyreports, riskRealizedSeries);
    </script>

    <!-- Total hours of projects charts (only for admins and coaches) -->
    <script>
        var allTheWeeksData=<?php echo json_encode($this->request->getSession()->read('allTheWeeksData'));?>;
        var hoursComparisonData=<?php echo json_encode($this->request->getSession()->read('hoursComparisonData'));?>;
        var hoursComparisonSeries = [];
        for (let i = 0; i < hoursComparisonData.length; i++) {
                hoursComparisonSeries.push({
                    name: hoursComparisonData[i]['name'],
                    data: hoursComparisonData[i]['data'],
                });
        };

        createHoursComparisonChart(allTheWeeksData, hoursComparisonSeries);
    </script>
