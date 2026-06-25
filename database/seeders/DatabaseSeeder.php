<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use App\Models\Company;
use App\Models\JobCategory;
use App\Models\JobListing;
use App\Models\Application;
use App\Models\ApplicationStatusLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 0. JOB CATEGORIES ─────────────────────────────────────
        $categoryNames = [
            'Web Development'   => 'bi-code-slash',
            'IT Infrastructure' => 'bi-hdd-network',
            'IT Support'        => 'bi-headset',
            'Data & Analytics'  => 'bi-bar-chart-line',
            'Software Development' => 'bi-laptop',
            'UI/UX Design'      => 'bi-palette',
            'Quality Assurance' => 'bi-check2-square',
        ];

        $categories = [];
        foreach ($categoryNames as $name => $icon) {
            $categories[$name] = JobCategory::updateOrCreate([
                'slug' => Str::slug($name),
            ], [
                'name' => $name,
                'icon' => $icon,
            ]);
        }

        // ── 1. ADMIN ──────────────────────────────────────────────
        $admin = User::updateOrCreate([
            'email' => 'admin@githired.com',
        ], [
            'name'     => 'GitHired Admin',
            'role'     => 'admin',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // ── 2. EMPLOYERS ──────────────────────────────────────────
        $employers = [
            [
                'name'  => 'Marco Reyes',
                'email' => 'marco@techph.com',
                'company' => [
                    'name'        => 'TechPH Solutions',
                    'industry'    => 'Software Development',
                    'size'        => '51-200',
                    'location'    => 'Makati City, Metro Manila',
                    'website'     => 'https://techph.com',
                    'description' => 'A Philippine-based software development company delivering enterprise web and mobile solutions for clients across Southeast Asia.',
                ],
            ],
            [
                'name'  => 'Angelica Santos',
                'email' => 'angelica@cloudbase.ph',
                'company' => [
                    'name'        => 'CloudBase PH',
                    'industry'    => 'Cloud & Infrastructure',
                    'size'        => '11-50',
                    'location'    => 'Cebu City, Cebu',
                    'website'     => 'https://cloudbase.ph',
                    'description' => 'CloudBase PH specializes in cloud infrastructure, DevOps consulting, and managed hosting for growing Philippine businesses.',
                ],
            ],
            [
                'name'  => 'Daniel Lim',
                'email' => 'daniel@pixelforge.dev',
                'company' => [
                    'name'        => 'PixelForge Studio',
                    'industry'    => 'Web Design & Development',
                    'size'        => '1-10',
                    'location'    => 'Davao City, Davao del Sur',
                    'website'     => 'https://pixelforge.dev',
                    'description' => 'A boutique web studio crafting high-performance websites and digital products for startups and SMEs in Mindanao.',
                ],
            ],
            [
                'name'  => 'Rica Navarro',
                'email' => 'rica@datatrace.io',
                'company' => [
                    'name'        => 'DataTrace Analytics',
                    'industry'    => 'Data & Analytics',
                    'size'        => '11-50',
                    'location'    => 'Bonifacio Global City, Metro Manila',
                    'website'     => 'https://datatrace.io',
                    'description' => 'DataTrace helps Philippine enterprises make sense of their data through business intelligence dashboards and predictive analytics.',
                ],
            ],
        ];

        $createdEmployers = [];
        $createdCompanies = [];

        foreach ($employers as $emp) {
            $user = User::updateOrCreate([
                'email' => $emp['email'],
            ], [
                'name'     => $emp['name'],
                'role'     => 'employer',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);

            $company = Company::updateOrCreate([
                'slug' => Str::slug($emp['company']['name']),
            ], [
                'user_id'     => $user->id,
                'name'        => $emp['company']['name'],
                'industry'    => $emp['company']['industry'],
                'size'        => $emp['company']['size'],
                'location'    => $emp['company']['location'],
                'website'     => $emp['company']['website'],
                'description' => $emp['company']['description'],
            ]);

            $createdEmployers[] = $user;
            $createdCompanies[] = $company;
        }

        // ── 3. JOB LISTINGS ───────────────────────────────────────
        // 'category' below maps to the JobCategory name created above
        $jobs = [
            // TechPH Solutions (employer 0, company 0)
            [
                'employer_idx' => 0,
                'title'           => 'Laravel Backend Developer',
                'location'        => 'Makati City, Metro Manila',
                'location_type'   => 'hybrid',
                'type'            => 'full-time',
                'experience_level'=> 'mid',
                'category'        => 'Web Development',
                'description'     => "TechPH Solutions is looking for a skilled Laravel Backend Developer to join our growing engineering team. You'll be working on large-scale enterprise web applications serving clients across Southeast Asia.\n\nYou will collaborate closely with our frontend team, DevOps engineers, and product managers to deliver clean, well-tested PHP code on a Laravel stack.",
                'requirements'    => "• At least 2 years of hands-on Laravel experience\n• Strong understanding of RESTful API design\n• Experience with MySQL or PostgreSQL\n• Familiarity with Git workflows and code reviews\n• Knowledge of queues, events, and Laravel's service container\n• Bonus: experience with Vue.js or React",
                'skills_required' => ['PHP', 'Laravel', 'MySQL', 'REST API', 'Git'],
                'salary_min'      => 35000,
                'salary_max'      => 55000,
                'status'          => 'active',
            ],
            [
                'employer_idx' => 0,
                'title'           => 'React Frontend Developer',
                'location'        => 'Makati City, Metro Manila',
                'location_type'   => 'onsite',
                'type'            => 'full-time',
                'experience_level'=> 'mid',
                'category'        => 'Web Development',
                'description'     => "We're hiring a React Frontend Developer to build and maintain responsive, performant user interfaces for our enterprise clients. You'll translate Figma designs into pixel-perfect, accessible React components.\n\nYou'll work in a fast-paced Agile environment with bi-weekly sprints and direct client exposure.",
                'requirements'    => "• 2+ years of professional React experience\n• Proficiency in JavaScript (ES6+) and TypeScript\n• Experience with REST API integration\n• Familiarity with Tailwind CSS or Bootstrap\n• Understanding of Git and collaborative workflows\n• Bonus: experience with Redux, React Query, or Next.js",
                'skills_required' => ['React', 'JavaScript', 'TypeScript', 'Tailwind CSS', 'Git'],
                'salary_min'      => 35000,
                'salary_max'      => 50000,
                'status'          => 'active',
            ],
            [
                'employer_idx' => 0,
                'title'           => 'Junior PHP Developer',
                'location'        => 'Makati City, Metro Manila',
                'location_type'   => 'onsite',
                'type'            => 'full-time',
                'experience_level'=> 'entry',
                'category'        => 'Web Development',
                'description'     => "Great opportunity for a fresh graduate or junior developer to join a supportive team and grow fast. You'll be mentored by senior developers and contribute to real client projects from day one.",
                'requirements'    => "• Basic understanding of PHP and OOP\n• Some exposure to a PHP framework (Laravel, CodeIgniter)\n• Basic HTML, CSS, JavaScript\n• Willingness to learn and accept feedback\n• Fresh graduates welcome",
                'skills_required' => ['PHP', 'HTML', 'CSS', 'JavaScript', 'MySQL'],
                'salary_min'      => 18000,
                'salary_max'      => 25000,
                'status'          => 'active',
            ],

            // CloudBase PH (employer 1, company 1)
            [
                'employer_idx' => 1,
                'title'           => 'DevOps Engineer',
                'location'        => 'Cebu City, Cebu',
                'location_type'   => 'hybrid',
                'type'            => 'full-time',
                'experience_level'=> 'senior',
                'category'        => 'IT Infrastructure',
                'description'     => "CloudBase PH is expanding its infrastructure team and we need an experienced DevOps Engineer to manage CI/CD pipelines, cloud deployments, and server automation for our growing client base.\n\nYou'll own the deployment pipeline and collaborate with developers to ensure smooth, zero-downtime releases.",
                'requirements'    => "• 3+ years of DevOps or SysAdmin experience\n• Hands-on with AWS, GCP, or Azure\n• Experience with Docker and Kubernetes\n• CI/CD tools: GitHub Actions, GitLab CI, or Jenkins\n• Linux server administration\n• Shell scripting (Bash)\n• Bonus: Terraform or Ansible experience",
                'skills_required' => ['AWS', 'Docker', 'Kubernetes', 'CI/CD', 'Linux', 'Bash'],
                'salary_min'      => 60000,
                'salary_max'      => 90000,
                'status'          => 'active',
            ],
            [
                'employer_idx' => 1,
                'title'           => 'IT Support Specialist',
                'location'        => 'Cebu City, Cebu',
                'location_type'   => 'onsite',
                'type'            => 'full-time',
                'experience_level'=> 'entry',
                'category'        => 'IT Support',
                'description'     => "We're looking for a reliable IT Support Specialist to handle helpdesk tickets, network troubleshooting, and hardware/software support for our clients. You'll be the first line of support and a crucial part of client satisfaction.",
                'requirements'    => "• Background in IT, Computer Science, or related course\n• Knowledge of Windows Server and Active Directory basics\n• TCP/IP networking fundamentals\n• Customer service mindset\n• CompTIA A+ or Network+ is a plus",
                'skills_required' => ['Windows Server', 'Networking', 'Hardware Support', 'Helpdesk', 'Active Directory'],
                'salary_min'      => 18000,
                'salary_max'      => 28000,
                'status'          => 'active',
            ],
            [
                'employer_idx' => 1,
                'title'           => 'Cloud Infrastructure Intern',
                'location'        => 'Cebu City, Cebu',
                'location_type'   => 'hybrid',
                'type'            => 'internship',
                'experience_level'=> 'entry',
                'category'        => 'IT Infrastructure',
                'description'     => "A hands-on internship where you'll assist the infrastructure team with cloud monitoring, documentation, and minor automation tasks. Perfect for 3rd or 4th year IT/CS students doing OJT.",
                'requirements'    => "• Currently enrolled in BS IT, BS CS, or related course\n• Basic understanding of cloud concepts (AWS Free Tier experience is a plus)\n• Eagerness to learn Linux and cloud tools\n• Available for at least 3 months",
                'skills_required' => ['Linux basics', 'Cloud fundamentals', 'Documentation'],
                'salary_min'      => 5000,
                'salary_max'      => 8000,
                'status'          => 'active',
            ],

            // PixelForge Studio (employer 2, company 2)
            [
                'employer_idx' => 2,
                'title'           => 'Full Stack Web Developer',
                'location'        => 'Davao City, Davao del Sur',
                'location_type'   => 'remote',
                'type'            => 'full-time',
                'experience_level'=> 'mid',
                'category'        => 'Web Development',
                'description'     => "PixelForge Studio is a Davao-based web studio looking for a capable Full Stack Developer who can handle both frontend and backend tasks on client projects. You'll build sites independently and liaise directly with clients for requirements.",
                'requirements'    => "• Proficient in PHP (Laravel preferred) or Node.js\n• Frontend: React, Vue, or Vanilla JS\n• Database: MySQL or PostgreSQL\n• Experience deploying to shared hosting or VPS\n• Good communication skills for client-facing work",
                'skills_required' => ['PHP', 'Laravel', 'Vue.js', 'MySQL', 'CSS'],
                'salary_min'      => 30000,
                'salary_max'      => 45000,
                'status'          => 'active',
            ],
            [
                'employer_idx' => 2,
                'title'           => 'UI/UX Designer (with Frontend Skills)',
                'location'        => 'Davao City, Davao del Sur',
                'location_type'   => 'remote',
                'type'            => 'part-time',
                'experience_level'=> 'mid',
                'category'        => 'UI/UX Design',
                'description'     => "We need a designer who can also code. You'll design mockups in Figma and then translate them into clean, responsive HTML/CSS. Most of our projects are business websites and landing pages for Mindanao SMEs.",
                'requirements'    => "• Strong Figma skills — can design from scratch\n• Able to convert designs to HTML + CSS (Bootstrap or Tailwind)\n• Eye for typography and layout\n• Understanding of mobile-first design\n• Portfolio required",
                'skills_required' => ['Figma', 'HTML', 'CSS', 'Tailwind CSS', 'Bootstrap'],
                'salary_min'      => 15000,
                'salary_max'      => 22000,
                'status'          => 'active',
            ],

            // DataTrace Analytics (employer 3, company 3)
            [
                'employer_idx' => 3,
                'title'           => 'Data Analyst',
                'location'        => 'Bonifacio Global City, Metro Manila',
                'location_type'   => 'hybrid',
                'type'            => 'full-time',
                'experience_level'=> 'mid',
                'category'        => 'Data & Analytics',
                'description'     => "DataTrace is looking for a Data Analyst to help our clients extract business value from their raw data. You'll build dashboards, write queries, and present insights to stakeholders in clear, actionable reports.",
                'requirements'    => "• Proficient in SQL (PostgreSQL or MySQL)\n• Experience with Excel or Google Sheets for analysis\n• Familiarity with BI tools: Power BI, Tableau, or Metabase\n• Ability to present findings clearly to non-technical stakeholders\n• Python for data wrangling is a strong plus",
                'skills_required' => ['SQL', 'Power BI', 'Python', 'Excel', 'Data Visualization'],
                'salary_min'      => 35000,
                'salary_max'      => 55000,
                'status'          => 'active',
            ],
            [
                'employer_idx' => 3,
                'title'           => 'Backend Developer (Python / Django)',
                'location'        => 'Bonifacio Global City, Metro Manila',
                'location_type'   => 'hybrid',
                'type'            => 'full-time',
                'experience_level'=> 'senior',
                'category'        => 'Software Development',
                'description'     => "We're building internal tools and data pipeline APIs that power our analytics platform. We need a senior Python backend developer who can write clean, testable APIs and data processing scripts.\n\nYou'll work closely with our data scientists and frontend team to ship features weekly.",
                'requirements'    => "• 3+ years Python experience in a production environment\n• Strong Django or FastAPI skills\n• Database: PostgreSQL with complex query optimization\n• Experience building and consuming REST APIs\n• Familiarity with async tasks (Celery, Redis)\n• Bonus: PySpark or Pandas data pipeline experience",
                'skills_required' => ['Python', 'Django', 'PostgreSQL', 'REST API', 'Celery', 'Redis'],
                'salary_min'      => 65000,
                'salary_max'      => 95000,
                'status'          => 'active',
            ],
            [
                'employer_idx' => 3,
                'title'           => 'QA Engineer',
                'location'        => 'Bonifacio Global City, Metro Manila',
                'location_type'   => 'onsite',
                'type'            => 'full-time',
                'experience_level'=> 'mid',
                'category'        => 'Quality Assurance',
                'description'     => "DataTrace needs a QA Engineer to ensure the quality and reliability of our analytics dashboards and APIs. You'll write test plans, execute test cases, and report bugs to the development team.",
                'requirements'    => "• 2+ years QA experience (manual and automated)\n• Experience with Selenium, Cypress, or Playwright\n• Ability to write clear bug reports\n• API testing with Postman\n• Understanding of Agile/Scrum workflow",
                'skills_required' => ['Selenium', 'Cypress', 'Postman', 'Manual Testing', 'Agile'],
                'salary_min'      => 30000,
                'salary_max'      => 45000,
                'status'          => 'active',
            ],
        ];

        $createdJobs = [];
        foreach ($jobs as $job) {
            $empIdx  = $job['employer_idx'];
            $slug = Str::slug($createdCompanies[$empIdx]->name . '-' . $job['title']);

            $listing = JobListing::updateOrCreate([
                'user_id' => $createdEmployers[$empIdx]->id,
                'title' => $job['title'],
            ], [
                'user_id'          => $createdEmployers[$empIdx]->id,
                'company_id'       => $createdCompanies[$empIdx]->id,
                'category_id'      => $categories[$job['category']]->id, // ← linked here
                'title'            => $job['title'],
                'slug'             => $slug,
                'location'         => $job['location'],
                'location_type'    => $job['location_type'],
                'type'             => $job['type'],
                'experience_level' => $job['experience_level'],
                'description'      => $job['description'],
                'requirements'     => $job['requirements'],
                'skills_required'  => $job['skills_required'],
                'salary_min'       => $job['salary_min'],
                'salary_max'       => $job['salary_max'],
                'status'           => $job['status'],
                'published_at'     => now()->subDays(rand(1, 14)),
                'expires_at'       => now()->addDays(rand(14, 45)),
                'views_count'      => rand(10, 340),
            ]);
            $createdJobs[] = $listing;
        }

        // ── 4. SAMPLE APPLICANTS ──────────────────────────────────
        $applicants = [
            ['name' => 'Juan dela Cruz',   'email' => 'juan@email.com',    'headline' => 'Full Stack Developer', 'desired_job_type' => 'full-time', 'work_preference' => 'hybrid', 'experience_level' => 'mid', 'skills' => ['PHP', 'Laravel', 'Vue.js', 'MySQL']],
            ['name' => 'Maria Santos',     'email' => 'maria@email.com',   'headline' => 'React Frontend Dev',   'desired_job_type' => 'full-time', 'work_preference' => 'remote', 'experience_level' => 'mid', 'skills' => ['React', 'JavaScript', 'TypeScript', 'Tailwind CSS']],
            ['name' => 'Carlo Mendoza',    'email' => 'carlo@email.com',   'headline' => 'DevOps Engineer',      'desired_job_type' => 'contract', 'work_preference' => 'remote', 'experience_level' => 'senior', 'skills' => ['AWS', 'Docker', 'Linux', 'CI/CD']],
            ['name' => 'Ana Reyes',        'email' => 'ana@email.com',     'headline' => 'UI/UX Designer',       'desired_job_type' => 'full-time', 'work_preference' => 'onsite', 'experience_level' => 'entry', 'skills' => ['Figma', 'HTML', 'CSS', 'Bootstrap']],
        ];

        $createdApplicants = [];
        foreach ($applicants as $ap) {
            $user = User::updateOrCreate([
                'email' => $ap['email'],
            ], [
                'name'     => $ap['name'],
                'role'     => 'applicant',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);

            Profile::updateOrCreate([
                'user_id'  => $user->id,
            ], [
                'user_id'  => $user->id,
                'headline' => $ap['headline'],
                'location' => 'Philippines',
                'desired_job_type' => $ap['desired_job_type'],
                'work_preference' => $ap['work_preference'],
                'experience_level' => $ap['experience_level'],
                'skills'   => $ap['skills'],
            ]);

            $createdApplicants[] = $user;
        }

        // ── 5. SAMPLE APPLICATIONS (with status log history) ──────
        // Helper closure to create an application AND its initial log entry
        $createApplication = function ($applicantUser, $jobListing, $statusHistory, $coverLetter = null) {
            $application = Application::updateOrCreate([
                'user_id'        => $applicantUser->id,
                'job_listing_id' => $jobListing->id,
            ], [
                'status'         => end($statusHistory)['status'], // final/current status
                'cover_letter'   => $coverLetter,
                'status_updated_at' => now()->subDays($statusHistory[0]['days_ago']),
            ]);

            $application->statusLogs()->delete();

            // Build status log history (simulates status changing over time)
            $previousStatus = null;
            foreach ($statusHistory as $entry) {
                ApplicationStatusLog::create([
                    'application_id' => $application->id,
                    'old_status'     => $previousStatus,
                    'new_status'     => $entry['status'],
                    'changed_by'     => $previousStatus === null
                                            ? $applicantUser->id   // applicant submits = initial log
                                            : $jobListing->user_id, // employer changes status after that
                    'note'           => $entry['note'] ?? null,
                    'created_at'     => now()->subDays($entry['days_ago']),
                ]);
                $previousStatus = $entry['status'];
            }

            return $application;
        };

        // Juan applies to 3 jobs with mixed statuses + history
        $createApplication(
            $createdApplicants[0],
            $createdJobs[0],
            [
                ['status' => 'pending', 'days_ago' => 4, 'note' => 'Application submitted.'],
            ],
            'I am very interested in this position and believe my background in web development makes me a strong candidate.'
        );

        $createApplication(
            $createdApplicants[0],
            $createdJobs[1],
            [
                ['status' => 'pending',   'days_ago' => 6, 'note' => 'Application submitted.'],
                ['status' => 'interview', 'days_ago' => 2, 'note' => 'Shortlisted for technical interview.'],
            ],
            'React is one of my strongest skills — I have built several production SPAs.'
        );

        $createApplication(
            $createdApplicants[0],
            $createdJobs[6],
            [
                ['status' => 'pending',  'days_ago' => 8, 'note' => 'Application submitted.'],
                ['status' => 'rejected','days_ago' => 3, 'note' => 'Position filled by another candidate.'],
            ]
        );

        // Maria applies to 2 jobs
        $createApplication(
            $createdApplicants[1],
            $createdJobs[1],
            [
                ['status' => 'pending',   'days_ago' => 10, 'note' => 'Application submitted.'],
                ['status' => 'interview', 'days_ago' => 6,  'note' => 'Passed initial screening.'],
                ['status' => 'hired',     'days_ago' => 2,  'note' => 'Offer accepted!'],
            ],
            'React is my passion and I have 3 years of building production-grade SPAs.'
        );

        $createApplication(
            $createdApplicants[1],
            $createdJobs[6],
            [
                ['status' => 'pending', 'days_ago' => 1, 'note' => 'Application submitted.'],
            ]
        );

        // Carlo applies to DevOps role
        $createApplication(
            $createdApplicants[2],
            $createdJobs[3],
            [
                ['status' => 'pending',   'days_ago' => 5, 'note' => 'Application submitted.'],
                ['status' => 'interview', 'days_ago' => 1, 'note' => 'Interview scheduled for next week.'],
            ],
            'I have 4 years managing AWS infrastructure with Docker and Kubernetes in production.'
        );

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('');
        $this->command->info('Categories created: ' . count($categories));
        $this->command->info('Jobs created: ' . count($createdJobs));
        $this->command->info('');
        $this->command->info('Test accounts (password: password)');
        $this->command->info('  Admin:     admin@githired.com');
        $this->command->info('  Employer:  marco@techph.com');
        $this->command->info('  Applicant: juan@email.com');
    }
}
