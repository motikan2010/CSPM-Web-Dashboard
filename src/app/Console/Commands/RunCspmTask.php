<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\CspmService;
use Illuminate\Console\Command;

class RunCspmTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cspm:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private CspmService $cspmService;

    /**
     * RunCspmTask constructor.
     * @param CspmService $cspmService
     * @return void
     */
    public function __construct(CspmService $cspmService)
    {
        parent::__construct();
        $this->cspmService = $cspmService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->cspmService->runCspm('', true);
        return 0;
    }
}
