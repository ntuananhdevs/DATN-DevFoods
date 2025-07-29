{{-- Branch checking logic partial --}}
{{-- Use branches data from BranchMiddleware --}}
@if(session('selected_branch') || request()->cookie('selected_branch'))
    {{-- Branch is already selected, no action needed --}}
    @include('partials.customer.branch-selector-modal', ['branches' => $branches])
@else
    {{-- Include branch selector modal --}}
    @include('partials.customer.branch-selector-modal', ['branches' => $branches])
   
    {{-- Auto-show modal if no branch is selected --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if branch is selected
            const selectedBranch = sessionStorage.getItem('selected_branch') || 
                                 localStorage.getItem('selected_branch') ||
                                 getCookie('selected_branch');
            
            if (!selectedBranch) {
                // Show branch selector modal
                const branchModal = document.getElementById('branch-selector-modal');
                if (branchModal) {
                    branchModal.style.display = 'flex';
                }
            }
        });
        
        // Helper function to get cookie value
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        }
    </script>
@endif