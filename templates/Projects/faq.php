<?php $this->assign('title', 'FAQ');?>
 
<div id="faq" class="projects index large-9 medium-18 columns content float: left">
    <h3><?= __('FAQ') ?></h3>
    <ol start="1">
        <li><a accesskey="v" href="#Q1">Video tutorials</a></li>
        <li><a accesskey="u" href="#Q2">What can I use the Metrics Monitoring Tool (MMT) for?</a></li>
        <li><a accesskey="g" href="#Q3">How do I get started?</a></li>
        <li><a accesskey="p" href="#Q4">I forgot my password. How do I get a new one?</a></li>
        <li><a accesskey="c" href="#Q5">How can I change my password?</a></li>
        <li><a accesskey="r" href="#Q6">As a senior developer, how do I do the weekly reporting of my project?</a></li>
        <li><a accesskey="i" href="#Q7">Can I edit weekly reports?</a></li>
        <li><a accesskey="l" href="#Q8">How do I log my daily working time?</a></li>
        <li><a accesskey="h" href="#Q9">Where can I find team's/member's logged working time and the total number of working hours?</a></li>
        <li><a accesskey="s" href="#Q10">How can I view the progress of my project?</a></li>
        <li><a accesskey="t" href="#Q11">How can I try this application and/or see a DEMO?</a></li>

    </ol>
    
    See also <a href="/projects/publications" target="_blank">publications</a> related to MMT
    
    <h4 id="Q1">1. Video tutorials</h4>
    <p>
        <ul>
            <li>The navigation tutorial shows the general navigation in MMT and your project.</li>
            <li>The developer tutorial shows how to log time and view the progress of your project.</li>
            <li>Senior developer tutorial shows how to edit project info, add members and fill in the weekly report form.</li>
            <li>Coach (prev. supervisor) tutorial shows how to edit member roles and use the coach-only functionalities to monitor multiple projects.</li>
        </ul>
        <p>
            <iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/AckNF3vEtEU" title="MMT Tutorial Navigation" frameborder="0" allowfullscreen></iframe>
            <iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/T0_wId2h8JA" title="MMT Tutorial Developer" frameborder="0" allowfullscreen></iframe>
            <iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/UQYouJSNmVA" title="MMT Tutorial Manager" frameborder="0" allowfullscreen></iframe>
            <iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/47S049LbCfE" title="MMT Tutorial Supervisor" frameborder="0" allowfullscreen></iframe>
        </p>
        <p><a href="#">[back to the top]</a></p>
    </p>

    <h4 id="Q2">2. What can I use the Metrics Monitoring Tool (MMT) for?</h4>
    <p>
        <p></p>
        All project members can:
        <ul>
            <li>view the progress of your project</li>
            <li>leave comments on the weekly reports of your project</li>
            <li>give feedback on MMT</li>
        </ul>
        As a senior developer you can:
        <ul >
            <li>add members to your project team</li>
            <li>log your daily working time</li>
            <li>do the weekly reporting of your project</li> 
        </ul>
        As a developer you can:
        <ul >
             <li>log your daily working time</li>
        </ul>       
        As a customer:
        <ul >
             <li>you will have the weekly report in your email as a pdf and you see the metrics of your project</li>
        </ul> 
        As a coach:
        <ul>
            <li>add new projects</li>
            <li>edit project info of own projects</li>
            <li>can add new members to own projects, can view/edit member roles, starting dates, and ending dates in own projects</li>
            <li>can view and edit the weekly reports of own projects, unlike an admin or member role of a senior developer a coach cannot add new weekly reports</li>
            <li>can log time for any developer or senior developer in own projects. Can edit/delete logged tasks in own projects</li>
        </ul>      
        <a href="#">[back to the top]</a> 
    </p>

    <h4 id="Q3">3. How do I get started?</h4>
    <p>
        <p></p>
        As a senior developer:
        <ul >
            <li>create a user ID for yourself by signing up in MMT</li>
            <li>contact your course coach, provide him/her with your user ID and ask him/her to create your project in MMT with you as a senior developer</li>
            <li>add your team members as developers to your project - and you are good to go!</li>
        </ul>
        As a developer:
        <ul >
            <li>create a user ID for yourself by signing up in MMT</li>
            <li>contact your project's senior developer, provide him/her with your user ID and ask him/her to add you as a developer to your project</li>
        </ul>      
        <a href="#">[back to the top]</a>
    </p>

    <h4 id="Q4">4. I forgot my password. How do I get a new one?</h4>
    <p>
        There is a “Forgot Your Password?” link on the log-in page.
        <br/>
        <br/>
        <a href="#">[back to the top]</a>
    </p>

    <h4 id="Q5">5. How can I change my password or edit my profile?</h4>
    <p>
        You can change your password and edit your profile by hovering over your name and choose “Edit profile”.
        <br/>
        <br/>
        <a href="#">[back to the top]</a>
    </p>

    <h4 id="Q6">6. As a senior developer, how do I do the weekly reporting of my project?</h4>
    <p>
        On weekly reports, the admin and coach can follow the progress of the project. 
        Weekly reports are done by the senior developer and project charts are made by these.
        <br/>
        <br/>

        The deadline for returning weekly reports is on Monday night. After that, the weekly report will be late.
        <br/>
        <br/>

        The first page of the weekly report is for the basic information. The senior developer can add 
        information about team meetings, challenges, and other relevant information. The weekly reports 
        are made for the previous week so there will be automatically the previous week’s week number. 
        There is also a slot to provide a link for project requirements. It can be for example link to 
        Trello if that is used. Note, that you can sync Trello to MMT.
        <br/>
        <br/>

        The second page is for metrics. For all teams, the phase will be 0 in the first weekly report. 
        After that, the phases can be planned for example by sprints. Product backlog means the total 
        number of new tasks and features. The sprint backlog is the number of tasks/features that are 
        in progress. If you have sync Trello you will get these numbers automatically. You will get 
        the number of your commits in total from your code version control tool, like GitHub. Note 
        that this means the total number of commits of all branches. New features need to be tested 
        and documented. There is a slot for all test cases and passed test cases.
        <br/>
        <br/>

        The third page is for risks.
        <br/>
        <br/>

        The fourth page is a summary of member’s weekly working hours and you cannot edit that.
        <br/>
        <br/> 

        <a href="#">[back to the top]</a>
    </p>

    <h4 id="Q7">7. Can I edit weekly reports?</h4>
    <p>
        No, only minor changes: contact your coach if you need bigger changes.
        <br/>
        <br/>

        <a href="#">[back to the top]</a>
    </p>

    <h4 id="Q8">8. How do I log my daily working time?</h4>
    <p>
        <p></p>
        <!--Working hours can be logged starting from Monday 
        that follows the week that is covered by last weekly report.--> 
        You can log your daily working time in MMT as follows:
        <ul>
            <li>log in and select the project you want to log time for</li>
            <li>click the "Log time" tab at the top of the screen</li>
            <li>click the "Log time" link on the left-hand side</li>
            <li>enter the date, description, duration (hours), and work type for the time to be logged</li>
            <li>click "Submit" once you are done, and your logged task is saved</li>
        </ul>
        NB: Only the senior developer of the project can add working hours to previous weeks of which the weekly report has already been done.
        <br>
        <br>
        <a href="#">[back to the top]</a>
    </p>

    <h4 id="Q9">9. Where can I find the team's/member's logged working time and the total number of working hours?</h4>
    <p>
        <p></p>
        You can view the total numbers of working hours as follows:
        <ul>
            <li>log in and select your project</li>
            <li>to view the total numbers of working hours per team and per member:
                <ul>
                    <li>click the "Members" tab at the top menu</li>
                </ul>
            </li>
            <li>to view the total numbers of member's working hours by work type:
                <ul>
                    <li>click the "Members" tab at the top menu</li> 
                    <li>click member's name on the list</li>
                </ul>
            </li>
        </ul>
    </p>
    <p> 
        <p></p>
        You can view the logged working time as follows:
        <ul>
            <li>log in and select your project</li>
            <li>to view the team's logged tasks:
                <ul>
                    <li>click the "Log time" tab at the top menu and scroll down</li>
                </ul>
            </li> 
            <li>if a member has logged working hours, you can view his/her logged tasks:
                <ul>
                    <li>click the "Log time" tab at the top menu,</li>
                    <li>click member's name on the list</li>
                </ul>
            </li>
            <li>or alternatively
                <ul>
                    <li>click the "Members" tab at the top menu</li>
                    <li>click member's name on the list</li> 
                    <li>click the "Logged tasks" button</li>
                </ul>
            </li>
        </ul>    
        <a href="#">[back to the top]</a>
    </p>

    <h4 id="Q10">10. How can I view the progress of my project?</h4>
    <p>
        <p></p>
        You can view the progress of your project as follows:
        <ul >
            <li>log in and select the project whose progress you want to view</li>
            <li>click the "Charts" tab at the top of the screen</li>
            <li>you now see the progression of the selected project in charts, based on the provided weekly report data</li>
            <li>you can change the viewing period by amending the min and max weeks and years in the Edit limits section on the left-hand side</li>
        </ul> 
        <a href="#">[back to the top]</a>
    </p>
          
    <h4 id="Q11">11. How can I try this application and/or see a DEMO?</h4>
    <p>
        <a href="/projects/about">See About MMT.</a> 
        <br/>
        <br/>
        <a href="#">[back to the top]</a>
    </p>    
</div>
