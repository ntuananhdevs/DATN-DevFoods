// Function to show a specific panel
        function showPanel(panelId, isDesktop = false) {
            // Hide all panels
            document.querySelectorAll('.panel').forEach(panel => {
                panel.style.display = 'none';
            });
            
            // Show the selected panel
            document.getElementById(panelId).style.display = 'block';
            
            // Update active nav item
            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach(item => item.classList.remove('active'));
            
            // Find the nav item that was clicked and make it active
            const index = ['mapPanel', 'incomePanel', 'servicesPanel', 'inboxPanel', 'profilePanel'].indexOf(panelId);
            if (index >= 0 && !isDesktop) {
                navItems[index].classList.add('active');
            }
            
            // Update desktop nav items
            if (isDesktop) {
                const desktopNavItems = document.querySelectorAll('.desktop-nav-item');
                desktopNavItems.forEach(item => item.classList.remove('active'));
                
                const desktopIndex = ['mapPanel', 'incomePanel', 'servicesPanel', 'inboxPanel', 'profilePanel'].indexOf(panelId);
                if (desktopIndex >= 0) {
                    desktopNavItems[desktopIndex].classList.add('active');
                }
                
                // Adjust map container width based on panel visibility
                const mapContainer = document.getElementById('mapPanel');
                if (panelId === 'mapPanel') {
                    mapContainer.classList.add('full-width');
                } else {
                    mapContainer.classList.remove('full-width');
                    mapContainer.style.display = 'block'; // Always show map on desktop
                }
            }
        }
        
        // Function to show detail panel
        function showDetailPanel(panelId) {
            document.getElementById(panelId).style.display = 'flex';
        }
        
        // Function to close detail panel
        function closeDetailPanel(panelId) {
            document.getElementById(panelId).style.display = 'none';
        }
        
        // Function to switch tabs
        function switchTab(group, tabId) {
            // Hide all tab contents for this group
            const tabContents = document.querySelectorAll(`#${group}-day, #${group}-week, #${group}-month, #${group}-all, #${group}-important, #${group}-alerts`);
            tabContents.forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show the selected tab content
            const selectedTab = document.getElementById(`${group}-${tabId}`);
            if (selectedTab) {
                selectedTab.classList.add('active');
            }
            
            // Update active tab
            const tabs = document.querySelectorAll(`.${group === 'inbox' ? 'inbox-tab' : 'tab'}`);
            tabs.forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Find the tab that was clicked and make it active
            const clickedTab = Array.from(tabs).find(tab => tab.textContent.toLowerCase().includes(tabId));
            if (clickedTab) {
                clickedTab.classList.add('active');
            }
        }
        
        // Check if desktop view
        function isDesktopView() {
            return window.innerWidth >= 1024;
        }
        
        // Initialize layout based on screen size
        function initLayout() {
            if (isDesktopView()) {
                // Desktop layout
                document.getElementById('mapPanel').style.display = 'block';
                document.getElementById('mapPanel').classList.add('full-width');
            } else {
                // Mobile layout
                showPanel('mapPanel');
            }
        }
        
        // Add event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize layout
            initLayout();
            
            // Example: Show trip detail when clicking on a specific element in trip history
            document.querySelectorAll('.trip-history-item').forEach(item => {
                item.addEventListener('click', function() {
                    showDetailPanel('tripDetailPanel');
                });
            });
            
            // Example: Show wallet panel when clicking on balance button
            document.querySelector('.balance-button').addEventListener('click', function() {
                showDetailPanel('walletPanel');
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                initLayout();
            });
            
            // Example: Show trip detail when clicking on service config button
            document.querySelector('.service-config').addEventListener('click', function() {
                showDetailPanel('tripDetailPanel');
            });
        });