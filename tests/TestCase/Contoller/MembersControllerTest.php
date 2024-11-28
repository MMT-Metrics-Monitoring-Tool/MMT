<?php
namespace App\Test\TestCase\Controller;

use App\Controller\MembersController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class MembersControllerTest extends TestCase
{
    use IntegrationTestTrait;

    protected $fixtures = [
        'app.Users',
        'app.Members',
        'app.Projects'
    ];

    public function setUp(): void {
        parent::setUp();

        // Set auth
        $this->session([
            'Auth' => [
                'User' => [
                    'role' => 'admin',
                    'id' => 1,
                ]
            ]
        ]);
        $this->session(['is_admin' => true]);
        $this->session(['selected_project' => 1]);
    }

    public function testAddNonexistant() {
        $this->session(['selected_project' => ['id' => 1]]);
        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $data = [
            'email' => 'does_not_exist',
            'project_role' => 'senior_developer',
            'target_hours' => "130",
        ];

        $this->post('/members/add', $data);

        $this->assertResponseError();
    }

    public function testAddExisting() {
        $this->session(['selected_project' => ['id' => 1]]);
        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $data = [
            'email' => 'testuser@example.com',
            'project_role' => 'senior_developer',
            'target_hours' => "130",
        ];

        $this->post('/members/add', $data);

        $this->assertResponseSuccess();
    }
}
