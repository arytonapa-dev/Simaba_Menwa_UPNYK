<?php
/**
 * Home Controller
 * Public Landing Page
 */

class HomeController extends Controller {

    public function index() {
        // If already logged in, redirect to dashboard
        if (Auth::check()) {
            $this->redirect('/index.php?controller=dashboard&action=index');
        }

        // Render Home Page
        $this->view('home/index', [
            'title' => 'Beranda'
        ], 'public');
    }
}
