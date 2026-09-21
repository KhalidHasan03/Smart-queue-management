<?php

namespace Database\Seeders;

use App\Models\Advertisement;
use App\Models\Counter;
use App\Models\Doctor;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['email' => 'superadmin@queuecare.local'], [
            'name' => 'Super Administrator',
            'password' => 'password123',
            'role' => User::ROLE_SUPER_ADMIN,
            'is_active' => true,
        ]);
        $admin = User::firstOrCreate(['email' => 'admin@queuecare.local'], [
            'name' => 'Administrator',
            'password' => 'password123',
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);
        User::firstOrCreate(['email' => 'reception@queuecare.local'], [
            'name' => 'Receptionist',
            'password' => 'password123',
            'role' => User::ROLE_RECEPTIONIST,
            'is_active' => true,
        ]);

        $general = Service::firstOrCreate(['prefix' => 'G'], [
            'name' => 'General',
            'start_number' => 1,
            'is_active' => true,
        ]);
        $dental = Service::firstOrCreate(['prefix' => 'D'], [
            'name' => 'Dental',
            'start_number' => 1,
            'is_active' => true,
        ]);

        Doctor::firstOrCreate(['name' => 'Dr. Ahmed', 'service_id' => $general->id], [
            'specialization' => 'General Physician',
            'room_no' => '101',
            'is_active' => true,
        ]);
        Doctor::firstOrCreate(['name' => 'Dr. Sara', 'service_id' => $dental->id], [
            'specialization' => 'Dentist',
            'room_no' => '102',
            'is_active' => true,
        ]);

        $c1 = Counter::firstOrCreate(['name' => 'Counter-1'], [
            'room_no' => '101',
            'service_id' => $general->id,
            'is_active' => true,
        ]);
        Counter::firstOrCreate(['name' => 'Counter-2'], [
            'room_no' => '102',
            'service_id' => $dental->id,
            'is_active' => true,
        ]);

        User::firstOrCreate(['email' => 'operator@queuecare.local'], [
            'name' => 'Counter Operator',
            'password' => 'password123',
            'role' => User::ROLE_OPERATOR,
            'counter_id' => $c1->id,
            'is_active' => true,
        ]);

        User::firstOrCreate(['email' => 'staff@queuecare.local'], [
            'name' => 'Service Staff',
            'password' => 'password123',
            'role' => User::ROLE_STAFF,
            'service_id' => $general->id,
            'is_active' => true,
        ]);
        User::firstOrCreate(['email' => 'display@queuecare.local'], [
            'name' => 'Display Operator',
            'password' => 'password123',
            'role' => User::ROLE_DISPLAY_OPERATOR,
            'is_active' => true,
        ]);

        Setting::updateOrCreate(['key' => 'clinic_name'], ['value' => 'Queue-Pro Hospital']);
        Setting::updateOrCreate(['key' => 'clinic_address'], ['value' => 'Main Road']);
        Setting::updateOrCreate(['key' => 'token_footer'], ['value' => 'Please wait in waiting area']);
        Setting::updateOrCreate(['key' => 'display.refresh_secs'], ['value' => '4']);
        Setting::updateOrCreate(['key' => 'display.ticker'], ['value' => 'Please keep your token with you']);
        Setting::updateOrCreate(['key' => 'display.show_patient'], ['value' => '1']);

        Setting::updateOrCreate(['key' => 'advert.enabled'], ['value' => '1']);
        Setting::updateOrCreate(['key' => 'advert.mode'], ['value' => 'cycle']);
        Setting::updateOrCreate(['key' => 'advert.duration_secs'], ['value' => '12']);
        Setting::updateOrCreate(['key' => 'advert.position'], ['value' => 'right']);

        Advertisement::firstOrCreate(['title' => 'Welcome to Queue-Pro'], [
            'description' => 'Thank you for choosing our hospital. Please keep your token with you and watch the display for your number.',
            'media_type' => Advertisement::TYPE_TEXT,
            'is_active' => true,
            'is_live' => true,
            'sort_order' => 0,
        ]);

        // Page content (dynamic landing pages)
        Setting::updateOrCreate(['key' => 'page.features'], ['value' => json_encode([
            'hero_subtitle' => 'Products & Features',
            'hero_title_1' => 'Everything your hospital',
            'hero_title_2' => 'needs.',
            'hero_description' => 'Eight integrated modules that cover every aspect of patient flow management.',
            'items' => [
                ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'teal', 'title' => 'Customer Flow Management', 'desc' => 'Real-time patient queue tracking across departments and counters.'],
                ['icon' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z', 'color' => 'emerald', 'title' => 'Self-Service Kiosks', 'desc' => 'Patients check in at kiosks with QR scanning or phone number entry.'],
                ['icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'color' => 'teal', 'title' => 'Digital Signage', 'desc' => 'Any screen becomes a live display showing calling names and numbers.'],
                ['icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'color' => 'emerald', 'title' => 'Patient Feedback', 'desc' => 'Token-gated reviews ensure only real patients can leave feedback.'],
                ['icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'teal', 'title' => 'e-Appointment Booking', 'desc' => 'Patients book appointments online and auto-receive queue numbers.'],
                ['icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'emerald', 'title' => 'Digital Banking Kiosk', 'desc' => 'Integrated payment kiosks for co-payments and fees.'],
                ['icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z', 'color' => 'teal', 'title' => 'Visitor Management', 'desc' => 'Track visitors, issue passes, and manage visitor flow.'],
                ['icon' => 'M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129', 'color' => 'emerald', 'title' => 'Smart Analytics', 'desc' => 'Wait times, peak hours, department performance — all in one dashboard.'],
            ],
            'how_it_works_title' => 'Three steps to a faster clinic.',
            'steps' => [
                ['num' => '01', 'desc' => 'Register the patient at reception or kiosk — token is issued instantly.'],
                ['num' => '02', 'desc' => 'Patient watches the live display and gets called to the right counter.'],
                ['num' => '03', 'desc' => 'Staff completes the visit, token is marked done. Analytics updated in real-time.'],
            ],
            'cta_title' => 'Start using Queue-Pro today.',
            'cta_description' => 'Free 30-day trial. No card required. Cancel anytime.',
        ])]);

        Setting::updateOrCreate(['key' => 'page.industry'], ['value' => json_encode([
            'hero_subtitle' => 'Industries',
            'hero_title_1' => 'Queue management for',
            'hero_title_2' => 'every industry.',
            'hero_description' => 'From hospitals to banks, Queue-Pro adapts to any environment where people wait.',
            'items' => [
                ['icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'color' => 'teal', 'title' => 'Healthcare', 'desc' => 'Hospitals, clinics, labs, and pharmacies. Reduce patient wait times by 40%.', 'features' => ['Patient flow management', 'Doctor queue assignment', 'Waiting room displays', 'Token-based reviews']],
                ['icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'emerald', 'title' => 'Banking & Finance', 'desc' => 'Banks, insurance offices, and financial institutions.', 'features' => ['Teller queue management', 'Priority customer routing', 'Digital banking kiosks', 'Service time analytics']],
                ['icon' => 'M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z', 'color' => 'teal', 'title' => 'Government', 'desc' => 'Municipalities, civil services, and public offices.', 'features' => ['Walk-in queue management', 'Multi-department routing', 'Public display screens', 'Wait time forecasting']],
                ['icon' => 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z', 'color' => 'emerald', 'title' => 'Education', 'desc' => 'Universities, schools, and training centers.', 'features' => ['Student service desks', 'Admissions flow', 'Exam scheduling queues', 'Parent visit management']],
                ['icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z', 'color' => 'teal', 'title' => 'Retail', 'desc' => 'Retail stores, service centers, and customer support desks.', 'features' => ['Customer service queues', 'Return & exchange flow', 'Peak hour management', 'Customer satisfaction tracking']],
                ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'emerald', 'title' => 'Others', 'desc' => 'Telecom offices, visa centers, salons, repair shops.', 'features' => ['Telecom service centers', 'Visa & immigration offices', 'Salon & spa bookings', 'Repair & service shops']],
            ],
            'cta_title' => 'Ready for your industry?',
            'cta_description' => "Queue-Pro adapts to any environment. Let's build your custom solution.",
        ])]);

        Setting::updateOrCreate(['key' => 'page.about'], ['value' => json_encode([
            'hero_subtitle' => 'About Queue-Pro',
            'hero_title_1' => 'Modernizing patient',
            'hero_title_2' => 'since 2020.',
            'hero_description' => 'Queue-Pro was built to solve a simple problem: patients waiting too long and clinics losing control of their flow. We give both sides a better experience.',
            'mission_subtitle' => 'Our Mission',
            'mission_title' => 'Reduce wait times to zero.',
            'mission_description' => 'Every minute a patient waits is a minute the clinic could be serving someone else. Queue-Pro eliminates idle time, removes confusion, and gives both staff and patients full visibility of the queue.',
            'stats' => [
                ['value' => '40%', 'label' => 'Average reduction in wait time'],
                ['value' => '3x', 'label' => 'More patients served per day'],
            ],
            'clients_title' => 'Hospitals that trust Queue-Pro.',
            'clients_description' => 'From small clinics to large hospital networks, Queue-Pro scales to fit any size.',
            'clients' => [
                ['name' => 'Al-Shifa Hospital', 'city' => 'Amman'],
                ['name' => 'Jordan Medical Center', 'city' => 'Irbid'],
                ['name' => 'Royal Health Clinic', 'city' => 'Zarqa'],
                ['name' => 'City Hospital', 'city' => 'Aqaba'],
            ],
            'partners_title' => 'Built with trusted technology partners.',
            'partners_description' => 'We integrate with the tools hospitals already use.',
            'partners' => [
                ['name' => 'Laravel', 'type' => 'Framework'],
                ['name' => 'MySQL', 'type' => 'Database'],
                ['name' => 'Tailwind CSS', 'type' => 'UI'],
                ['name' => 'Alpine.js', 'type' => 'Interactivity'],
            ],
            'cta_title' => 'Ready to modernize your hospital?',
            'cta_description' => 'Start your free 30-day trial today. No card required.',
        ])]);

        Setting::updateOrCreate(['key' => 'page.contact'], ['value' => json_encode([
            'hero_subtitle' => 'Contact Us',
            'hero_title_1' => "Let's talk about",
            'hero_title_2' => 'your hospital.',
            'hero_description' => "Whether you need a demo, a custom deployment, or just have a question — we're here.",
            'email' => 'hello@queuepro.com',
            'phone' => '+962 78 253 3233',
            'phone_link' => 'https://wa.me/962782533233',
        ])]);

        Setting::updateOrCreate(['key' => 'page.pricing'], ['value' => json_encode([
            'hero_subtitle' => 'Pricing',
            'hero_title_1' => 'Simple, transparent',
            'hero_title_2' => 'pricing.',
            'hero_description' => "Start free. Upgrade when you're ready. No hidden fees, no surprises.",
            'plans' => [
                [
                    'name' => 'Starter', 'subtitle' => 'For small clinics', 'price' => '$490', 'period' => '/year',
                    'note' => '~$41/month · Billed annually',
                    'features' => ['Up to 3 departments', 'Up to 5 staff users', '1 live display screen', 'Basic analytics dashboard', 'Token issuance & queue', 'Email support', '30-day free trial'],
                    'cta' => 'Start Free Trial', 'dark' => false,
                ],
                [
                    'name' => 'Enterprise', 'subtitle' => 'For hospitals & networks', 'price' => 'Custom', 'period' => '',
                    'note' => 'Tailored to your needs',
                    'features' => ['Unlimited departments', 'Unlimited staff users', 'Multiple display screens', 'Advanced analytics & reports', 'Custom integrations (API)', 'Self-service kiosks', '24/7 priority support', 'On-site setup & training', 'Annual maintenance contract'],
                    'cta' => 'Contact Sales', 'dark' => true, 'badge' => 'Most Popular',
                ],
            ],
            'trial_note' => 'All plans include a <strong class="text-slate-700">30-day free trial</strong>. No credit card required. Cancel anytime.',
            'faqs_title' => 'Frequently asked questions',
            'faqs' => [
                ['q' => 'Is there really a free trial?', 'a' => 'Yes. Every plan starts with a 30-day free trial — no card required.'],
                ['q' => 'Can I upgrade from Starter to Enterprise later?', 'a' => 'Absolutely. You can upgrade at any time. We prorate the difference.'],
                ['q' => 'Do you offer discounts for non-profits?', 'a' => 'Yes. We offer special pricing for non-profit hospitals. Contact us for details.'],
                ['q' => 'What happens to my data if I cancel?', 'a' => 'Your data stays available for 30 days after cancellation.'],
            ],
        ])]);

        Setting::updateOrCreate(['key' => 'page.services'], ['value' => json_encode([
            'hero_subtitle' => 'Our Services',
            'hero_title_1' => 'Services we',
            'hero_title_2' => 'provide.',
            'hero_description' => 'From consultation to deployment, we handle everything so your team can focus on patients.',
            'svc_services' => [
                ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'title' => 'System Setup & Training', 'desc' => 'We install Queue-Pro on your servers, configure departments, counters, and staff roles, then train your team on day-one usage.'],
                ['icon' => 'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z', 'title' => 'Custom Integrations', 'desc' => 'Connect Queue-Pro with your existing hospital management system, EMR, or payment gateway through our REST API.'],
                ['icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z', 'title' => '24/7 Support', 'desc' => 'Our support team is available around the clock via WhatsApp, phone, or email.'],
                ['icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'title' => 'On-Site Consultation', 'desc' => 'Our team visits your facility to map patient flow, identify bottlenecks, and design the optimal queue layout.'],
                ['icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'title' => 'Annual Maintenance', 'desc' => 'Software updates, security patches, database backups, and performance monitoring included in every plan.'],
                ['icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'title' => 'Analytics & Reporting', 'desc' => 'Custom reports on wait times, peak hours, department performance, and patient satisfaction metrics.'],
            ],
            'dept_title' => 'Active departments in',
            'dept_description' => 'Each department has dedicated doctors and counters.',
            'cta_title' => 'Need a custom solution?',
            'cta_description' => "We tailor Queue-Pro to fit your hospital's exact workflow.",
        ])]);

        // Home page component content
        Setting::updateOrCreate(['key' => 'page.hero'], ['value' => json_encode([
            'badge' => 'Trusted by hospitals worldwide',
            'title_1' => 'Smarter Patient',
            'title_2' => 'Flow Management',
            'description' => 'Streamline hospital queues from check-in to consultation. Reduce wait times, improve patient experience, and keep your facility running efficiently.',
            'cta_primary' => 'See Live Demo',
            'cta_secondary' => 'How It Works',
            'badges' => ['No app needed', 'Works on any TV', '30-day free trial', '24/7 Support'],
        ])]);

        Setting::updateOrCreate(['key' => 'page.trust'], ['value' => json_encode([
            'title' => 'Trusted by leading hospitals',
            'logos' => [
                ['name' => 'Al-Shifa Hospital', 'initials' => 'AS', 'color' => 'from-teal-500 to-emerald-500'],
                ['name' => 'Jordan Medical Center', 'initials' => 'JM', 'color' => 'from-emerald-500 to-cyan-500'],
                ['name' => 'Royal Health Clinic', 'initials' => 'RH', 'color' => 'from-cyan-500 to-teal-500'],
                ['name' => 'City Hospital', 'initials' => 'CH', 'color' => 'from-teal-400 to-emerald-400'],
                ['name' => 'Al-Noor Medical', 'initials' => 'AN', 'color' => 'from-emerald-400 to-cyan-400'],
                ['name' => 'Star Care Clinic', 'initials' => 'SC', 'color' => 'from-cyan-400 to-teal-400'],
            ],
            'stat_text' => 'Over <span class="text-cyan-600 font-bold">50+ hospitals</span> across the region trust Queue-Pro',
        ])]);

        Setting::updateOrCreate(['key' => 'page.how_it_works'], ['value' => json_encode([
            'subtitle' => 'How It Works',
            'title' => 'Three steps to a faster clinic.',
            'description' => 'From patient registration to checkout, Queue-Pro handles the entire flow.',
            'steps' => [
                ['num' => '01', 'title' => 'Register', 'desc' => 'Patient checks in at reception or self-service kiosk. Token is issued instantly with service and doctor assignment.', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                ['num' => '02', 'title' => 'Call & Serve', 'desc' => 'Counter staff calls the next patient. The live display updates immediately showing the called number and counter.', 'icon' => 'M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122'],
                ['num' => '03', 'title' => 'Complete & Analyze', 'desc' => 'Visit is completed, analytics are updated in real-time. Track wait times, peak hours, and staff performance.', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ],
        ])]);

        Setting::updateOrCreate(['key' => 'page.home_features'], ['value' => json_encode([
            'subtitle' => 'Products & Solutions',
            'title' => 'Built for modern hospitals.',
            'description' => 'Everything a hospital needs — from patient flow to digital signage.',
            'items' => [
                ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'gradient' => 'from-teal-500 to-emerald-500', 'title' => 'Customer Flow', 'desc' => 'Real-time patient queue tracking across departments.'],
                ['icon' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z', 'gradient' => 'from-cyan-500 to-blue-500', 'title' => 'Self-Service Kiosks', 'desc' => 'Patients check in at kiosks — no desk needed.'],
                ['icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'gradient' => 'from-violet-500 to-purple-500', 'title' => 'Digital Signage', 'desc' => 'Any screen becomes a live display.'],
                ['icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'gradient' => 'from-pink-500 to-rose-500', 'title' => 'Patient Feedback', 'desc' => 'Token-gated reviews from real patients.'],
                ['icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'gradient' => 'from-blue-400 to-cyan-500', 'title' => 'e-Appointment', 'desc' => 'Book appointments online, auto-assign queue.'],
                ['icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'gradient' => 'from-emerald-400 to-teal-500', 'title' => 'Banking Kiosk', 'desc' => 'Integrated payment kiosks for co-payments.'],
                ['icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z', 'gradient' => 'from-amber-400 to-orange-500', 'title' => 'Visitor Mgmt', 'desc' => 'Track visitors, issue passes, manage flow.'],
                ['icon' => 'M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129', 'gradient' => 'from-red-400 to-pink-500', 'title' => 'Smart Analytics', 'desc' => 'Wait times, peak hours, performance.'],
            ],
        ])]);

        Setting::updateOrCreate(['key' => 'page.home_pricing'], ['value' => json_encode([
            'subtitle' => 'Pricing',
            'title' => 'Simple, transparent pricing for hospitals.',
            'description' => "Start free. Upgrade when you're ready.",
            'plans' => [
                ['name' => 'Starter', 'badge' => 'For Clinics', 'price' => '$490', 'period' => '/year', 'note' => '~$41/month · 30-day free trial', 'features' => ['5 doctor rooms', '2 reception desks', 'Up to 100 patients/day', '2 waiting-room screens', 'Live queue dashboard', 'Ticket printing', 'Basic analytics'], 'cta' => 'Start Free Trial', 'dark' => false],
                ['name' => 'Enterprise', 'badge' => 'Most Popular', 'subtitle' => 'For hospitals & multi-branch clinics', 'price' => 'Custom', 'note' => 'Priced on a call', 'features' => ['Unlimited departments', 'Unlimited staff users', 'Multiple display screens', 'Advanced analytics & reports', 'Custom integrations (API)', 'Self-service kiosks', '24/7 priority support', 'On-site setup & training'], 'cta' => 'Contact Sales', 'dark' => true],
            ],
        ])]);

        Setting::updateOrCreate(['key' => 'page.home_faq'], ['value' => json_encode([
            'subtitle' => 'FAQ',
            'title' => 'Questions hospitals ask before they start.',
            'items' => [
                ['q' => 'What is Queue-Pro?', 'a' => 'Queue-Pro is a hospital queue management system. It gives reception a live board of every patient, lets patients check in from their phone, and turns any screen into a waiting-room display.'],
                ['q' => 'Do patients need to install an app?', 'a' => 'No. Patients scan the QR code at the entrance and register in their browser. They follow their place in the queue from the same page — no download, no account, no password.'],
                ['q' => 'What hardware do I need for the display?', 'a' => "Any television or monitor that can open a web page. A smart TV's browser is enough. Displays reload themselves after updates."],
                ['q' => 'Does Queue-Pro work in Arabic?', 'a' => 'Yes. The interface, printed tickets, and spoken calls are all available in Arabic with proper right-to-left layout.'],
                ['q' => 'Can I try before paying?', 'a' => 'Yes. Every plan starts with a 30-day free trial — no card required. Cancel anytime.'],
                ['q' => 'How long does setup take?', 'a' => 'A clinic is taking real patients the same morning. Add departments, print the QR code, open the screen URL.'],
                ['q' => 'Can several receptionists use it?', 'a' => "Yes. Each desk signs in as its own station, so multiple receptionists see each other's changes immediately."],
                ['q' => 'Is patient data private?', 'a' => "Every clinic's data is isolated and every request is authorised against the clinic it belongs to."],
            ],
        ])]);

        Setting::updateOrCreate(['key' => 'page.demo_cta'], ['value' => json_encode([
            'badge' => 'Live demo available',
            'title' => 'See your hospital queue in action.',
            'description' => 'Open the demo and watch a live hospital queue — patients joining, counters calling, screens updating. No sign-up needed.',
            'cta_primary' => 'Open the Live Demo',
            'cta_secondary' => 'Sign In to Dashboard',
            'footer_text' => 'A real hospital, running live. No sign-up required.',
        ])]);
    }
}
