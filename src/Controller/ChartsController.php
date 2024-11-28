<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;

class ChartsController extends AppController
{
    public $name = 'Charts';
    public $uses = array();
    
    public function initialize(): void {
        parent::initialize();

        // Argument 1 passed to Cake\Http\Session::_overwrite() must be of the type array, null given,
        // called in /var/www/html/vendor/cakephp/cakephp/src/Http/Session.php on line 495
        $this->request->getSession()->read();
    }
    
    public function index() {
        $admin = $this->request->getSession()->read('is_admin');
        $supervisor = ( $this->request->getSession()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;

        $chartLimits = $this->request->getSession()->read('chartLimits');

        // When the chart limits are updated this is where they are saved
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            
            // If user tries to select more than 52 weeks, display error and don't save time limits
            if (($data['yearmin'] < $data['yearmax']) && (53 - $data['weekmin'] + $data['weekmax'] > 52)) {
                $this->Flash->error(__('Can\'t display more than 52 weeks'));
            } elseif ($data['yearmin'] > $data['yearmax']) { // If user tries to put max year value smaller than min year
                $this->Flash->error(__('Min year can\'t be more than max year'));
            } elseif ($data['weekmin'] > $data['weekmax']) { // If user tries to put max week value smaller than min week
                $this->Flash->error(__('Min week can\'t be more than max week'));
            } else {
                $chartLimits['weekmin'] = $data['weekmin'];
                $chartLimits['weekmax'] = $data['weekmax'];
                $chartLimits['yearmin'] = $data['yearmin'];
                $chartLimits['yearmax'] = $data['yearmax'];
                $this->request->getSession()->write('chartLimits', $chartLimits);
                
                // refreshing the page to apply the new limits
                $page = $_SERVER['PHP_SELF'];
            }
        }
        
        // Set the stock limits for the chart limits
        // They are only set once, if the "chartLimits" cookie is not in the session
        if (!$this->request->getSession()->check('chartLimits')) {
            $time = Time::now();
            
            // show last year, current year and next year
            $chartLimits['weekmin'] = 1;
            $chartLimits['weekmax'] = date('W', strtotime($time));
            $chartLimits['yearmin'] = $time->year;
            $chartLimits['yearmax'] = $time->year;
            
            $this->request->getSession()->write('chartLimits', $chartLimits);
        }

        // The ID of the currently selected project
        $project_id = $this->request->getSession()->read('selected_project')['id'];
        
        // Get all the data for the charts, based on the chartlimits
        // Fuctions in "ChartsTable.php"
        $weeklyreports = $this->Charts->reports(
            $project_id,
            $chartLimits['weekmin'],
            $chartLimits['weekmax'],
            $chartLimits['yearmin'],
            $chartLimits['yearmax']
        );
        $this->request->getSession()->write('weeklyreports', $weeklyreports);

        $allTheWeeks = $this->Charts->weekList(
            $chartLimits['weekmin'],
            $chartLimits['weekmax'],
            $chartLimits['yearmin'],
            $chartLimits['yearmax']
        );
        $this->request->getSession()->write('alltheweeks', $allTheWeeks);

        // Line chart displaying the cumulative amount of hours done in the project
        $totalhourData = $this->Charts->totalhourLineData(
            $project_id,
            $allTheWeeks,
            $chartLimits['weekmin'],
            $chartLimits['weekmax'],
            $chartLimits['yearmin'],
            $chartLimits['yearmax']
        );
        $this->request->getSession()->write('totalhourdata', $totalhourData);
        $projectStartDate = clone $this->request->getSession()->read('selected_project')['created_on'];
        $projectEndDate = $this->request->getSession()->read('selected_project')['finished_date'];

        // This is used so that new charts from MMT6 (EVC1, EVC2 and hour pie chart) are not displayed for older projects
        // as they would just cause errors (the data necessary for new charts is not present in older projects)
        $dateOfChartUpdates = new Time('2020-08-01');
        
        // For some charts data is only created (and chart displayed) if project has reports and hours
        if (($admin || $supervisor) && sizeof($weeklyreports['id']) > 0 && $this->Charts->getTotalHours($project_id) > 0 && $projectStartDate > $dateOfChartUpdates) {
            $this->request->getSession()->write('displayCharts', true);

            $earnedValueData = $this->Charts->earnedValueData($project_id, $projectStartDate, $projectEndDate);
            // Save data in a session variable for use in front end
            $this->request->getSession()->write('earnedValueData', $earnedValueData);

            $earnedValueData2 = $this->Charts->earnedValueData2($project_id, $projectStartDate, $projectEndDate);
            $this->request->getSession()->write('earnedValueData2', $earnedValueData2);
        } else {
            $this->request->getSession()->write('displayCharts', false);
        }

        // Gather the data for different charts
        $phaseData = $this->Charts->phaseAreaData($weeklyreports['id']);
        $this->request->getSession()->write('phaseData', $phaseData);

        $reqData = $this->Charts->reqColumnData($weeklyreports['id']);
        $this->request->getSession()->write('reqData', $reqData);

        $commitData = $this->Charts->commitAreaData($weeklyreports['id']);
        $this->request->getSession()->write('commitData', $commitData);

        $testcaseData = $this->Charts->testcaseAreaData($weeklyreports['id']);
        $this->request->getSession()->write('testcasedata', $testcaseData);
        
        // Bar chart displaying the amount of hours in each category
        $hoursData = $this->Charts->hoursData($project_id);
        $this->request->getSession()->write('hoursData', $hoursData);
        $hoursData_1 = array(
            $hoursData[1],
            $hoursData[2],
            $hoursData[3],
            $hoursData[4],
            $hoursData[5],
            $hoursData[6],
            $hoursData[7],
            $hoursData[8],
            $hoursData[9]
        );
        $this->request->getSession()->write('hoursData_1', $hoursData_1);
        
        // Line chart displaying the amount of hours done by the team per week
        $hoursperweekData = $this->Charts->hoursPerWeekData(
            $project_id,
            $allTheWeeks,
            $chartLimits['weekmin'],
            $chartLimits['weekmax'],
            $chartLimits['yearmin'],
            $chartLimits['yearmax']
        );
        $this->request->getSession()->write('hoursperweekdata', $hoursperweekData);

        $riskData = $this->Charts->riskData($weeklyreports['id'], $project_id);

        // Data for the hoursComparisonChart (HighCharts JS)
        $userId = $this->request->getSession()->read('Auth.User.id');
        $this->request->getSession()->write('riskData', $riskData);
        $hoursComparisonData = $this->Charts->hoursComparisonData(
            $allTheWeeks,
            $chartLimits['weekmin'],
            $chartLimits['weekmax'],
            $chartLimits['yearmin'],
            $chartLimits['yearmax'],
            $admin,
            $userId
        );
        $this->request->getSession()->write('hoursComparisonData', $hoursComparisonData);
        $this->request->getSession()->write('allTheWeeksData', $allTheWeeks);
    }

    public function isAuthorized($user) {
        return true;
    }
}
