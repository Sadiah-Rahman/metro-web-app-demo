<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;

class DashboardController extends Controller {
    public function index() {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }
        $this->view('dashboard.php', ['user' => $user]);
    }
}
