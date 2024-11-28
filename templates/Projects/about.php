
<?php $this->assign('title', 'About');?>

<div id="faq" class="projects index large-9 medium-18 columns content float: left">
    <h3><?= __('About MMT') ?></h3>

    <h4>MMT</h4>
    <p>
        Metrics Monitoring Tool is part of Pekka Mäkiaho's PhD work. MMT is in use at Tampere University's courses: Project work and Software Project management.
        <br>
        It is used for logging working hours in a project, for managing a project and reporting its state, and for observing one project or the whole portfolio visually.
        With MMT you can observe data at portfolio level, at project level or even see the statistics of an individual project member.
        <br>
        <br>
        <!-- See also <a href="http://metricsmonitoring.sis.uta.fi/publications" target="_blank">publications</a> related to MMT -->
        See also <?= $this->Html->link(__('publications'), ['controller' => 'Projects', 'action' => 'publications']) ?> related to MMT.
    </p>

    <h4>Statistic page</h4>
        <p>
        On the Statistic-page you can first see the project manager's opinion on the state of the project at each week indicated with traffic lights. You can see also if the weekly report is missing or if was delivered late (L).
        </p>
        <br>
        <img src="/img/weeklyreports.png" alt="Weekly reports of projects">
        <br>
        <br>
        <p>If you want to see the projects' state according to different <b>metrics</b>, you can also see them as values and as traffic lights. The colours of the traffic lights depends on the value of the metrics and also on the other metrics and the current phase of the projects. </p>
        <br>
        <img src="/img/metrics.png" alt="Metrics of different projects">
        <br>
        <br>
        <p>At the end of the Statistic Page, you can see the <b>Working Hours</b> of each project.</p>
        <br>
        <p>The traffic lights warns if it seems that someone has done significantly less hours than the others on the group or the project member has not been participated to the project work for a while.</p>
        <br>
        <img src="/img/totalnumberofworkinghours.png" alt="Total number of working hours of projects">
        <br>
        <br>
        <p>You can also <b>select a project</b> and see the working hours of each member at more detailed level and see the prediction of the working hours at the MEMBERS PAGE.</p>
        <br>
        <img src="/img/projectmembers.png" alt="All members of a project">
        <br>
        <br>
        <p>If you want to go to personal level: you can select a member of the group and see his or her logged hours by selecting a member above.</p>
        <br>
        <img src="/img/loggedhoursofmember.png" alt="Logged hours of a project member">
        <br>
        <br>
        <p>And you can even see what kind of project work the member has logged at the LOG TIME PAGE.</p>
        <br>
        <img src="/img/workhourdescription.png" alt="Description of what kind of hours the member has done">
        <br>
        <br>
        <p>If you want to go inside the project, you can select a project and go to CHARTS PAGE.</p>
        <p>PHASE CHART gives you information how the project is divided to the phases (e.g. sprints, iterations, etc.) and what is the current phase.</p>
        <br>
        <img src="/img/phasechart.png" alt="Phases of the project">
        <br>
        <br>
        <p><b>Requirement chart</b> gives information weekly based, how many requirements there are still in the <b>product backlog</b>, how many in the <b>backlog of the current sprint</b>. How many requirements have been already <b>done</b> and if some have been <b>rejected</b>.</p>
        <br>
        <img src="/img/requirementchart.png" alt="Chart of the project requirements">
        <br>
        <br>
        <p><b>Commit chart</b> shows the proceeding of software projects: what is the total amount of commits made to the version control system:</p>
        <br>
        <img src="/img/commitchart.png" alt="Chart of the project commits">
        <br>
        <br>
        <p><b>Test case chart</b> shows how many test cases there are and how many of those have been passed.</p>
        <br>
        <img src="/img/testcasechart.png" alt="Chart of the project test cases">
        <br>
        <br>
        <p><b>Working hours charts</b> show the done work categorized and also how the total amount of working hours increases weekly.</p>
        <br>
        <img src="/img/totalhours.png" alt="Total hours of project">
        <br>
        <br>
        <img src="/img/categorizedworkhours.png" alt="Working hours of the project categorized">
        <br>
        <br>
        <p>There are also several <b>charts on risk</b>, based on probability and severity, for example.</p>
        <br>
        <p>The one below shows a combination (multiplication) of the probability and the severity of the risks. </p>
        <br>
        <img src="/img/projectrisks.png" alt="Risks of the project">
        <br>
        <br>
        <p>There are also <b>charts</b> you can use to predict the project on the base of the <b>earned value</b>.</p>
        <p>Here you can see that the project was planned to be finished by week 19 and there were budgeted 910 hours.</p>
        <p>However, there has been delays and even if the project continues as originally planned, the project will be ready at week 21 and will use 957 hours.</p>
        <br>
        <img src="/img/earnedvaluechart.png" alt="Earned value chart of the project">
        <br>
        <br>
        <p>Below, in another project, we are predicting the project assuming that the project's deviation (regarding the budget and the schedule) remains the same as it has been so far. </p>
        <br>
        <img src="/img/earnedvaluechart2.png" alt="Another earned value chart of a project">


    <h4>Test environment</h4>
    <p>
        If you want to use the testing environment, please contact Pekka Mäkiaho at pekka.makiaho@tuni.fi or use
        <a href="https://www.linkedin.com/in/makiaho/" target="_blank">LinkedIn</a>.
        <br>
        See also <a href="/mmttest/projects/publications" target="_blank">publications</a> related to MMT.
    </p>

    <h4>Release notes</h4>
    <p>
        <h6>Version 10.0 (06.12.2024)</h5>
        <ul>
            <li>Updated CakePHP version</li>
            <li>Released an open-source snapshot of MMT at: https://github.com/MMT-Metrics-Monitoring-Tool/MMT.git</li>
            <li>Implemented unit tests for Controllers</li>
            <li>Created test cases for the project</li>
            <li>Fixed several bugs related to risks</li>
            <li>Miscellaneous other bug fixes</li>
            <li>Updated GitLab WIKI</li>
        </ul>
    </p>
    <p>
        <h6>Version 9.0 (10.5.2024)</h5>
        <ul>
            <li>Updated CakePHP version</li>
            <li>Enforced code formatting</li>
            <li>Added two new graphs to display risk statistics</li>
            <li>Fixed errors related to editing risks</li>
            <li>Enhanced user navigation</li>
            <li>Improved security measures</li>
            <li>Added new risk attributes</li>
            <li>Updated GitLab WIKI</li>
        </ul>
    </p>
    <p>
        <h6>Version 8.0 (26.4.2023)</h5>
        <ul>
            <li>Made a Dockerfile for using Docker as the development environment</li>
            <li>Updated CakePHP version from 3.x to 4.x</li>
            <li>Changed charts from HighCharts PHP plugin to HighCharts JS library</li>
            <li>Removed project’s publicity feature</li>
            <li>Bug fixes</li>
            <li>Accessibility fixes</li>
            <li>Changed Supervisor role to Coach and Client to Customer</li>
            <li>Updated several pages and added a privacy notice</li>
            <li>Increased logout timeout</li>   
            <li>Updated GitLab WIKI</li>
        </ul>
    </p>
    <p>
        <h6>Version 7.0 (12.5.2021)</h5>
        <ul>
            <li>Security analysis & updates accordingly (GDPR compliance, threat modeling, manual reviewing, penetration testing)</li>
            <li>Server transfer with both production site and test environment</li>
            <li>PHP 7.4 update</li>
            <li>Database updates - f.ex. removing default NULL-values, removing phone number</li>
            <li>Documentation - full database diagram, creating GitLab WIKI </li>
            <li>Changing former member role of a manager to senior developer (both in code and database)</li>
            <li>Weekly report: Only senior developer can modify working hours after the weekly report is done</li>
            <li>Last seen -timestamp to user's personal data / members page</li>
            <li>Tooltip for the degree of readiness</li>   
            <li>Bug fixes & overall testing</li>
        </ul>
    </p>
    <p>
        <h6>Version 6.2 (24.8.2020)</h5>
        <ul>
            <li>New work types and metric types added (including project's degree of readiness and overall status metrics)</li>
            <li>Earned value method method implemented (data and charts visible only to supervisors)</li>
            <li>Pie chart added displaying projects working hours categorized by type</li>
            <li>New metrics table and some new data added to statistics page (only visible to supervisors)</li>
            <li>Usability updates and bug fixes: new tutorial videos, better instructions for forms, small layout updates</li>
        </ul>
    </p>
    <p>
        <h6>Version 6.1 (4.4.2020)</h5>
        <ul>
            <li>GitHub connection that can fetch number of commits for weekly report</li>
            <li>Predictive chart for project's total hours</li>
            <li>Information of the weekly report creator and updater displayed in weekly report</li>
            <li>Usability updates: target hour marker for the member's personal working hours chart, display metric name when updating, better error messages for weekly report forms, group project's connection settings into project's info page</li>
        </ul>
    </p>
    <p>
        <h6>Version 6.0 (16.3.2020)</h5>
        <ul>
            <li>Increase of the mobile usability</li>
            <li>Last activity of a project  member shown</li>
            <li>Personal working hours chart and prediction of the total hours during the project</li>
            <li>Bugs fixed: HTTPS-redirection issues, diagram visibility issues for supervisors, working hours diagrams issues</li>
            <li>Coming soon: linking to version control systems, usability improvements, prediction for the project…</li>
        </ul>
    </p>
    <h4>Previous versions</h4>
        <p>
            Version 10.0 was implemented in Autumn 2024 COMP.SE.610/620 by project group G27<br>
            Project manager: Esa Karjalainen. Developers: Eetu Hopeaharju, Osmo Laukkanen, Väinö Mäkelä, Pyry Mäkinen, Emilia Sipola.
        </p>
        <p>
           Version 9.0 was implemented in Spring 2024 COMP.SE 610/620 by project group G29<br>
           Project manager: Taisto Tammilehto. Developers: Niko Pärssinen, Jaakko Kitinoja, Jason Korhonen.
        </p>
        <p>
            Version 8.0 was implemented in Spring 2023 COMP.SE 610/620 by project group Artesaaniratkaisu.<br>
            Project manager: Otso Oksanen. Developers: Ella Koivisto, Heidi Seppi, Jimi Niemi, Markus Härkönen, Pauliina Hippula, Tuomo Pöllänen. 
        </p>
        <p>
            Version 7.0 was implemented in Spring 2021 TIEA4 & TIETS19 by project group MMT-VII.<br>
            Project manager: Juha Ranta-Ojala. Developers: Katrin Dieter, Auli Jukkola, Tittamari Salonen, Tatu Sikkinen. 
        </p>
        <p>
            Versions 6.0-6.2 were implemented by Mikko Luukko as a TIETS16 programming project 
            during the spring term of 2020. 
        </p>
        <p>
            Version 5.0 were implemented during the fall term of 2019 as a coursework for TIEA4 Project Work course and TIETS19 Software Project Management course. 
                The team consisted of two project managers (Hanna-Riikka Rantamaa and Henna Lehto) 
                and four developers (Kimi af Forselles, Mikko Luukko, Tommi Piili and Ville Niemi). 
                Updates included: new interface with TUNI-Theme (logo and brand of the new Tampere University), 
                new diagrams like comparing the total hours of the project to the all parallel public projects, 
                HTTPS protocol, bug fixing and other smaller features.
        </p>
        <p>
            Version 4.0 was implemented by Murat Pojon as a TIETS16 programming project 
            during the spring term of 2017. 
        </p>
        <p>
            Versions 2.0-2.1 and version 3.0 were implemented by Sirkku Seitamäki as a TIETS16 programming project 
            during the summer and fall terms of 2016. 
        </p>
        <p>
            Versions 1.1-1.3 were implemented during the spring term of 2016 as a coursework for 
            TIEA4 Project Work course and TIETS19 Software Project Management course. 
            The team consisted of two project managers (Elena Solovieva and Choudhary Shahzad Shabbir) 
            and two developers (Andreas Valjakka and Sirkku Seitamäki). 
        </p>   
        <p>
            Version 1.0 was the product of the fall 2015 Project Work team.  
            Jukka Ala-Fossi and Mykola Andrushchenko were the developers in the project and 
            Katriina Löytty was the manager. This was the first version taken to production.
        </p>    
        <p>
            Version 0.9 was developed during  the academic year 2014-2015. However, after the testing and evaluation, it was never taken to use.
            Even if the coding of the next version was started "from the scratch", a lot of ideas were gathered and implemented during the project.
            
        </p>  

</div>
