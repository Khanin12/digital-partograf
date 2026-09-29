<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('the dashboard returns a successful response', function () {
    $response = $this->get('/test');

    $response->assertStatus(200);
    $response->assertSee('template/css/sb-admin-2.min.css');
    $response->assertSee('template/css/custom-health.css');
});

test('master layout and components render correctly', function () {
    $view = $this->view('master.blank');

    $view->assertSee('Partograf System');
    $view->assertSee('Total Pasien');
    $view->assertSee('Hak Cipta');
});

test('the pasien page returns a successful response with components', function () {
    $response = $this->get('/pasien');

    $response->assertStatus(200);
    $response->assertSee('Data Pasien');
    $response->assertSee('Data Pasien Terdaftar');
    $response->assertSee('dataTable');
});

test('the persalinan page returns a successful response', function () {
    $response = $this->get('/persalinan');

    $response->assertStatus(200);
    $response->assertSee('Data Persalinan');
    $response->assertSee('dataTable');
});

test('the observasi-partograf page returns a successful response', function () {
    $response = $this->get('/observasi-partograf');

    $response->assertStatus(200);
    $response->assertSee('Data Observasi Partograf');
    $response->assertSee('dataTable');
});

test('the catatan-persalinan page returns a successful response', function () {
    $response = $this->get('/catatan-persalinan');

    $response->assertStatus(200);
    $response->assertSee('Catatan Persalinan');
});
