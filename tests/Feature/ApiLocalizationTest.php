<?php

namespace Tests\Feature;

use App\Common\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ApiLocalizationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware('api')->get('/api/test-localization', function () {
            $responder = new class
            {
                use ApiResponseTrait;

                public function make()
                {
                    return $this->successResponse([], 'messages.category.list_success');
                }
            };

            return $responder->make();
        });
    }

    public function test_api_returns_english_message_when_lang_query_is_en(): void
    {
        $response = $this->getJson('/api/test-localization?lang=en');

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Categories retrieved successfully');
    }

    public function test_api_returns_vietnamese_validation_message_when_lang_query_is_vi(): void
    {
        $response = $this->postJson('/api/v1/auth/forgot-password?lang=vi', []);

        $response
            ->assertStatus(422)
            ->assertJsonPath('message', 'Dữ liệu không hợp lệ.')
            ->assertJsonPath('data.email.0', 'email là bắt buộc.');
    }
}
