<?php

namespace App\Http\Middleware\Customer;

use Closure;
use Illuminate\Http\Request;
use App\Services\BranchService;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class BranchMiddleware
{
    protected $branchService;

    public function __construct(BranchService $branchService)
    {
        $this->branchService = $branchService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Handle branch selection and validation
        $this->processBranchSelection($request);

        // Share branch data with views
        $this->shareBranchDataWithViews();

        return $next($request);
    }


    /**
     * Process branch selection from request or cookie and validate current branch
     */
    protected function processBranchSelection(Request $request): void
    {
        // Handle branch selection from request parameter
        if ($branchId = $request->input('branch_id')) {
            $this->setBranchSafely($branchId, 'request');
            return;
        }

        // Handle branch selection from cookie if no session branch exists
        if (!session()->has('selected_branch') && ($branchId = $request->cookie('selected_branch'))) {
            $this->setBranchSafely($branchId, 'cookie');
        }

        // Validate current selected branch
        $this->validateCurrentBranch();
    }

    /**
     * Safely set branch with error handling
     */
    protected function setBranchSafely($branchId, $source): void
    {
        try {
            $this->branchService->setSelectedBranch($branchId, false);
        } catch (\Exception $e) {
            if ($source === 'cookie') {
                cookie()->queue(cookie()->forget('selected_branch'));
            }
        }
    }

    /**
     * Validate current selected branch and clear if invalid
     */
    protected function validateCurrentBranch(): void
    {
        $currentBranchId = session('selected_branch');
        
        if ($currentBranchId && !$this->branchService->isValidBranch($currentBranchId)) {
            $this->branchService->clearSelectedBranch();
            cookie()->queue(cookie()->forget('selected_branch'));
        }
    }

    /**
     * Share branch data with views efficiently
     */
    protected function shareBranchDataWithViews(): void
    {
        $currentBranch = $this->branchService->getCurrentBranch();
        
        View::share([
            'currentBranch' => $currentBranch,
            'branches' => $this->branchService->getActiveBranches(),
            'hasBranchSelected' => !is_null($currentBranch)
        ]);
    }
}