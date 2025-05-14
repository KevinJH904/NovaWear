<?php

class pruebaController extends AppController{
    public function index() {
        try {
            $this->metodosPago = (new metodospago())->find();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}