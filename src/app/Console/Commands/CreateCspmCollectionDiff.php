<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\CspmService;
use Illuminate\Console\Command;

class CreateCspmCollectionDiff extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cspm:create_collection_diff';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private CspmService $cspmService;

    /**
     * CreateCspmCollectionDiff constructor.
     *
     * @param CspmService $cspmService
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
        $this->cspmService->createCollectionDiff('14d57somkzdd1qhvhf');
        return 0;
    }
}
