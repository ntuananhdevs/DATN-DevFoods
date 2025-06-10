<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Branch;
use Symfony\Component\HttpFoundation\Response;

class CheckSelectedBranch
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip middleware for API routes
        if ($request->is('api/*') && !$request->is('api/customer/branches/*')) {
            return $next($request);
        }
        
        // No longer automatically select a default branch
        // Let the user explicitly choose one
        
        // Check if we have a branch in the request and set it in session
        if ($request->has('branch_id')) {
            $branchId = $request->branch_id;
            session(['selected_branch' => $branchId]);
            session()->save();
        }
        
        // Check for branch in cookies as a fallback
        if (!session()->has('selected_branch') && $request->cookie('selected_branch')) {
            $branchId = $request->cookie('selected_branch');
            session(['selected_branch' => $branchId]);
            session()->save();
        }
        
        // Share current branch with all views
        if (session()->has('selected_branch')) {
            $branchId = session('selected_branch');
            $currentBranch = Branch::find($branchId);
            
            if ($currentBranch) {
                view()->share('currentBranch', $currentBranch);
            }
        }
        
        // Share all branches with all views for branch selector
        $branches = Branch::where('active', true)->get();
        view()->share('branches', $branches);
        
        return $next($request);
    }
} 