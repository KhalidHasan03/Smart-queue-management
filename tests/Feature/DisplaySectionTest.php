<?php

namespace Tests\Feature;

use App\Models\Advertisement;
use App\Models\Counter;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Token;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisplaySectionTest extends TestCase
{
    use RefreshDatabase;

    private function service(): Service
    {
        return Service::firstOrCreate(['name' => 'General'], ['prefix' => 'G', 'start_number' => 1, 'is_active' => true]);
    }

    private function doctor(): Doctor
    {
        return Doctor::firstOrCreate(['name' => 'Dr. Sam'], ['service_id' => $this->service()->id, 'is_active' => true]);
    }

    private function visibleCounter(): Counter
    {
        return Counter::create([
            'name' => 'Counter-1',
            'room_no' => '101',
            'service_id' => $this->service()->id,
            'is_active' => true,
            'show_on_display' => true,
        ]);
    }

    public function test_display_page_renders_structured_table_layout(): void
    {
        $this->seed(DatabaseSeeder::class);

        $html = $this->get(route('display'))->assertOk()->getContent();

        foreach (['Recent Display', 'Counter', 'Token', 'Status', 'Advertisement', 'Upcoming in Queue', 'Notice'] as $needle) {
            $this->assertStringContainsString($needle, $html);
        }

        // 4-8 layout: counter (4) + advertisement (8, fills the full 12-width gap),
        // upcoming under counter, notice as a full-width bar at the bottom.
        $this->assertStringContainsString('lg:col-span-4', $html);
        $this->assertStringContainsString('lg:col-span-8', $html);
        $this->assertStringContainsString('lg:col-span-12', $html);
        $this->assertStringContainsString('aspect-video', $html);

        $counterPos = strpos($html, 'Recent Display');
        $upcomingPos = strpos($html, 'Upcoming in Queue');
        $advertPos = strpos($html, 'Advertisement');
        $noticePos = strpos($html, '>Notice');

        $this->assertNotFalse($counterPos);
        $this->assertNotFalse($upcomingPos);
        $this->assertNotFalse($advertPos);
        $this->assertNotFalse($noticePos);
        $this->assertLessThan($upcomingPos, $counterPos, 'Counter must sit above Upcoming');
        $this->assertLessThan($advertPos, $upcomingPos, 'Counter + Upcoming must sit before the Advertisement block');
        $this->assertLessThan($noticePos, $advertPos, 'Notice must be the final bottom section');
    }

    public function test_api_falls_back_to_last_issued_token_with_status(): void
    {
        $service = $this->service();
        $doctor = $this->doctor();
        $counter = $this->visibleCounter();
        $patient = Patient::create(['name' => 'Ana', 'phone' => '123', 'age' => 30, 'gender' => 'female']);

        Token::create([
            'token_date' => now(), 'seq' => 1, 'token_no' => 'G-001',
            'service_id' => $service->id, 'doctor_id' => $doctor->id, 'counter_id' => $counter->id, 'patient_id' => $patient->id,
            'status' => Token::COMPLETED,
        ]);
        Token::create([
            'token_date' => now(), 'seq' => 2, 'token_no' => 'G-002',
            'service_id' => $service->id, 'doctor_id' => $doctor->id, 'counter_id' => $counter->id, 'patient_id' => $patient->id,
            'status' => Token::COMPLETED,
        ]);

        $payload = $this->getJson(route('display.api'))->assertOk()->json();

        $this->assertCount(1, $payload['now']);
        $row = $payload['now'][0];

        $this->assertSame('Counter-1', $row['counter']);
        $this->assertSame('G-002', $row['token_no']);
        $this->assertSame(Token::COMPLETED, $row['status']);
        $this->assertSame('Completed', $row['status_label']);
        $this->assertFalse($row['is_live']);
    }

    public function test_api_marks_live_calling_token(): void
    {
        $service = $this->service();
        $doctor = $this->doctor();
        $counter = $this->visibleCounter();
        $patient = Patient::create(['name' => 'Bob', 'phone' => '456', 'age' => 40, 'gender' => 'male']);

        $live = Token::create([
            'token_date' => now(), 'seq' => 1, 'token_no' => 'G-001',
            'service_id' => $service->id, 'doctor_id' => $doctor->id, 'counter_id' => $counter->id, 'patient_id' => $patient->id,
            'status' => Token::CALLING,
        ]);
        $counter->update(['current_token_id' => $live->id]);

        $row = collect($this->getJson(route('display.api'))->assertOk()->json()['now'])->first();

        $this->assertSame('G-001', $row['token_no']);
        $this->assertSame(Token::CALLING, $row['status']);
        $this->assertSame('Calling', $row['status_label']);
        $this->assertTrue($row['is_live']);
        $this->assertSame('Bob', $row['patient']);
    }

    public function test_api_preserves_per_item_duration(): void
    {
        Advertisement::create([
            'title' => 'Flash deal', 'media_type' => Advertisement::TYPE_TEXT,
            'description' => 'Flash deal text', 'duration_secs' => 5,
            'is_active' => true, 'sort_order' => 1,
        ]);
        Advertisement::create([
            'title' => 'Long video ad', 'media_type' => Advertisement::TYPE_VIDEO,
            'duration_secs' => 120, 'is_active' => true, 'sort_order' => 2,
        ]);

        $items = collect($this->getJson(route('display.api'))->assertOk()->json()['advert']['items']);

        $this->assertSame(5, $items->firstWhere('title', 'Flash deal')['duration_secs']);
        $this->assertSame(120, $items->firstWhere('title', 'Long video ad')['duration_secs']);
    }
}
