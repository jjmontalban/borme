<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BormeDownloaderTest extends TestCase
{
    /**
     * 
     *
     * A basic test example.
     *
     * @return void
     */
    
    public function testExample()
    {
        $this->post('/bormedownloader')
        	 ->assertStatus(200);
    }
}
