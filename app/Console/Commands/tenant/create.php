<?php

namespace App\Console\Commands\tenant;

use Illuminate\Console\Command;

use Illuminate\Support\Str;

use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Repositories\HostnameRepository;
use Hyn\Tenancy\Repositories\WebsiteRepository;

class create extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create {fqdn}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Given a unique tenant name, creates a new tenant in the system.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // This will be the complete website name (tenantUser.mysite.com)
        $fqdn = sprintf('%s.%s', $this->argument('fqdn'), env('APP_DOMAIN'));
        // The website object will save the tenant instance information
        // I recommend to use something random for security reasons
        $website = new Website;
        $website->uuid = env('TENANT_WEBSITE_PREFIX') . Str::random(6);
        app(WebsiteRepository::class)->create($website);
        // The hostname object will save the tenant hosting information, and will be related to the previous created software.
        $hostname = new Hostname;
        $hostname->fqdn = $fqdn;
        $hostname = app(HostnameRepository::class)->create($hostname);
        app(HostnameRepository::class)->attach($hostname, $website);
    }
}
