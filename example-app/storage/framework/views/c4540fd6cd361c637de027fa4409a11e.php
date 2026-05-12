<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - DepEd</title>
    
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Inter:wght@400;700;900&display=swap" rel="stylesheet">

    <style>
        [x-cloak] { display: none !important; }
        .font-cinzel { font-family: 'Cinzel', serif; }
        .modal-backdrop { z-index: 1040 !important; }
        .modal { z-index: 1050 !important; }

        /* Print styles preserved */
        @media print {
    /* 1. Kill all web UI elements */
    nav, aside, .no-print, button, form, .pagination { 
        display: none !important; 
    }

    /* 2. Reset the container to use 100% paper width */
    body, .flex-1, main, .max-w-7xl { 
        width: 100% !important; 
        max-width: none !important;
        margin: 0 !important; 
        padding: 0 !important; 
        background: white !important;
    }

    /* 3. Fix the Table Layout */
    table { 
        width: 100% !important; 
        table-layout: fixed !important; /* Forces columns to respect width */
        border-collapse: collapse !important;
    }
    
    th, td { 
        word-wrap: break-word !important; /* Prevents long school names from breaking layout */
        font-size: 8pt !important; /* Standardize font size for paper */
        padding: 8px 4px !important;
        border: 1px solid #cbd5e1 !important;
    }

    /* 4. Column Widths (Adjust these to fit your data) */
    th:nth-child(1) { width: 15%; } /* ID Code */
    th:nth-child(2) { width: 40%; } /* Name */
    th:nth-child(3) { width: 15%; } /* District */
    th:nth-child(4), th:nth-child(5) { width: 15%; } /* Teachers/Enrollees */
}
    </style>
</head>


<body class="bg-slate-50 flex items-start min-h-screen overflow-x-hidden" x-data="{ sidebarOpen: false }">

    
    <div id="globalLoader" class="fixed inset-0 z-[9999] hidden flex flex-col items-center justify-center bg-slate-900/80 backdrop-blur-md">
        <div class="relative w-24 h-24">
            <div class="absolute inset-0 border-4 border-slate-700 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-red-800 rounded-full border-t-transparent animate-spin"></div>
        </div>
        <div class="mt-8 text-center">
            <h3 class="text-white font-black uppercase tracking-[0.3em] text-xs mb-2">System Processing</h3>
            <p class="text-slate-400 font-bold uppercase text-[9px] tracking-widest animate-pulse">Synchronizing Registry Data...</p>
        </div>
    </div>

    
    <div x-show="sidebarOpen" 
         x-cloak 
         @click="sidebarOpen = false" 
         class="fixed inset-0 bg-black/50 z-40 lg:hidden">
    </div>

    
    <aside class="fixed inset-y-0 left-0 z-50 w-64 h-screen flex flex-col flex-shrink-0 shadow-lg text-white transition-transform duration-300 ease-in-out lg:sticky lg:top-0 lg:translate-x-0 no-print"
           style="background-color: #a52a2a;"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
           
        <div class="p-6 font-bold text-xl text-white border-b border-white/20 uppercase tracking-widest flex items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <i data-lucide="user-round" class="w-5 h-5"></i>
                <span class="text-sm md:text-xl">Admin Panel</span>
            </div>
            
            <button @click="sidebarOpen = false" class="lg:hidden text-white/70 hover:text-white transition">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <nav class="mt-4 flex-grow overflow-y-auto">
            <div class="px-6 py-2 text-[9px] font-black uppercase text-white/40 tracking-[0.2em]">Management</div>

             <a href="<?php echo e(route('admin.dashboard')); ?>" class="flex items-center gap-3 px-6 py-3 text-white hover:bg-black/10 transition <?php echo e(request()->routeIs('admin.dashboard') ? 'bg-black/20 font-black' : ''); ?>">
                <i data-lucide="home" class="w-4 h-4 text-white/70"></i>
                <span>Home</span>
            </a>
            
            
            <a href="<?php echo e(route('admin.schools')); ?>" class="flex items-center gap-3 px-6 py-3 text-white hover:bg-black/10 transition <?php echo e(request()->routeIs('admin.schools') ? 'bg-black/20 font-black' : ''); ?>">
                <i data-lucide="school" class="w-4 h-4 text-white/70"></i>
                <span>Manage Schools</span>
            </a>
            
            <a href="<?php echo e(route('admin.history')); ?>" class="flex items-center gap-3 px-6 py-3 text-white/80 hover:text-white hover:bg-black/10 transition">
                <i data-lucide="clipboard-list" class="w-4 h-4 text-white/70"></i>
                <span>Audit Logs</span>
            </a>

            <div class="mt-6 px-6 py-2 text-[9px] font-black uppercase text-white/40 tracking-[0.2em]">Live Tools</div>
            
            <a href="<?php echo e(route('admin.map')); ?>" class="flex items-center gap-3 px-6 py-3 text-white/80 hover:text-white hover:bg-black/10 transition <?php echo e(request()->routeIs('admin.map') ? 'bg-black/20 font-black' : ''); ?>">
                <i data-lucide="map" class="w-4 h-4 text-white/70"></i>
                <span>Registry Map</span>
            </a>
            
            <div class="mt-6 px-6 py-2 text-[9px] font-black uppercase text-white/40 tracking-[0.2em]">Public Preview</div>
            
            <a href="<?php echo e(route('public.map')); ?>" target="_blank" class="flex items-center gap-3 px-6 py-3 text-white/80 hover:text-white hover:bg-black/10 transition">
                <i data-lucide="external-link" class="w-4 h-4 text-white/70"></i>
                <span>View Interactive Map</span>
            </a>
            
            <a href="<?php echo e(route('public.schools')); ?>" target="_blank" class="flex items-center gap-3 px-6 py-3 text-white/80 hover:text-white hover:bg-black/10 transition">
                <i data-lucide="search" class="w-4 h-4 text-white/70"></i>
                <span>View Directory</span>
            </a>
        </nav>

        <div class="mt-auto border-t border-white/20">
            <form method="POST" action="<?php echo e(route('logout')); ?>" class="w-full">
                <?php echo csrf_field(); ?>
                <button type="submit" class="w-full flex items-center gap-3 px-6 py-4 text-white hover:bg-black/10 font-bold transition-all cursor-pointer">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    
    <div class="flex-1 flex flex-col w-full min-w-0">
        
        
        <header class="bg-[#a52a2a] text-white shadow-lg relative z-10 w-full no-print">
            <div class="px-4 py-3 md:px-8 md:py-4">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 md:gap-6 relative">
                    
                    
                    <button @click="sidebarOpen = true" class="absolute left-0 top-0 lg:hidden text-white hover:text-white/80 transition p-1">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>

                    
                    <div class="flex items-center gap-2 md:gap-4 shrink-0 mt-8 md:mt-0">
                        <img src="<?php echo e(asset('images/deped.png')); ?>" alt="DepEd" class="h-10 sm:h-12 md:h-16 w-auto drop-shadow-md">
                        <img src="<?php echo e(asset('images/r9.png')); ?>" alt="Region IX" class="h-10 sm:h-12 md:h-16 w-auto drop-shadow-md">
                    </div>

                    
                    <div class="flex flex-col font-cinzel text-white items-center md:items-start text-center md:text-left flex-1 md:border-l border-white/20 md:pl-6 px-2 w-full">
                        <span class="text-[8px] sm:text-[9px] tracking-widest leading-tight font-black uppercase">Republic of the Philippines</span>
                        <span class="text-[8px] sm:text-[9px] tracking-widest leading-tight font-black uppercase">Department of Education</span>
                        <div class="w-full border-b border-white/30 my-1"></div>
                        <h1 class="text-sm sm:text-lg md:text-xl lg:text-2xl tracking-wide font-black leading-tight uppercase">
                            <?php echo e($site_settings->header_title ?? 'Zamboanga City Division'); ?>

                        </h1>
                    </div>

                    
                    <div class="hidden xl:block shrink-0">
                        <img src="<?php echo e(asset('images/ts.png')); ?>" alt="Transparency Seal" class="h-16 w-auto opacity-90">
                    </div>
                </div>
            </div>
        </header>

        
        <main class="p-4 md:p-8 bg-slate-50 flex-grow">
            <?php echo $__env->yieldContent('content'); ?>
        </main>

        
        <footer class="bg-[#f2f2f2] text-gray-700 pt-10 pb-12 md:pt-16 md:pb-16 border-t border-gray-300 mt-auto relative" x-data="{ activeSection: null }">
            <div class="container mx-auto px-4 md:px-6 lg:px-20">
                
                <div class="grid grid-cols-2 lg:flex lg:flex-row items-center lg:items-start gap-y-8 lg:gap-10 justify-between">
                    
                    
                    <div class="col-span-1 lg:w-auto flex justify-center lg:justify-start flex-shrink-0 order-2 lg:order-1">
                        <?php $footerLeftLogos = isset($site_logos) ? $site_logos->where('position', 'footer_left') : collect(); ?>
                        <?php $__empty_1 = true; $__currentLoopData = $footerLeftLogos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $logo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <img src="<?php echo e(asset('storage/' . $logo->image_path)); ?>" alt="<?php echo e($logo->name); ?>" class="w-[100px] md:w-[150px] h-auto object-contain">
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <img src="<?php echo e(asset('images/rnp.png')); ?>" alt="PH Seal" class="w-[100px] md:w-[150px] h-auto object-contain">
                        <?php endif; ?>
                    </div>

                    
                    <div class="col-span-2 w-full flex-grow grid grid-cols-1 md:grid-cols-3 gap-2 md:gap-8 lg:mx-10 order-1 lg:order-2">
                        
                        
                        <div class="border-b border-gray-200 md:border-none">
                            <button @click="activeSection = (activeSection === 'rep' ? null : 'rep')" 
                                    class="w-full py-4 md:py-0 flex justify-between items-center md:block text-left outline-none">
                                <h2 class="font-bold text-[11px] md:text-sm uppercase tracking-wider text-gray-800">Republic of the Philippines</h2>
                                <i class="bi bi-chevron-down md:hidden transition-transform" :class="activeSection === 'rep' ? 'rotate-180' : ''"></i>
                            </button>
                            <div class="overflow-hidden transition-all md:max-h-none" :class="activeSection === 'rep' ? 'max-h-40 pb-4' : 'max-h-0 md:mt-4'">
                                <p class="text-[13px] leading-relaxed whitespace-pre-line text-gray-600">
                                    <?php echo e($site_settings->footer_about ?? 'All content is in the public domain unless otherwise stated.'); ?>

                                </p>
                            </div>
                        </div>

                        
                        <div class="border-b border-gray-200 md:border-none">
                            <?php if(!empty($site_settings->footer_sections)): ?>
                                <?php $__currentLoopData = $site_settings->footer_sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <button @click="activeSection = (activeSection === 'sec'+<?php echo e($index); ?> ? null : 'sec'+<?php echo e($index); ?>)" 
                                            class="w-full py-4 md:py-0 flex justify-between items-center md:block text-left outline-none">
                                        <h2 class="font-bold text-[11px] md:text-sm uppercase tracking-wider text-gray-800"><?php echo e($section['title']); ?></h2>
                                        <i class="bi bi-chevron-down md:hidden transition-transform" :class="activeSection === 'sec'+<?php echo e($index); ?> ? 'rotate-180' : ''"></i>
                                    </button>
                                    <div class="overflow-hidden transition-all md:max-h-none" :class="activeSection === 'sec'+<?php echo e($index); ?> ? 'max-h-60 pb-4' : 'max-h-0 md:mt-4'">
                                        <?php if(!empty($section['content'])): ?>
                                            <p class="text-[13px] leading-relaxed mb-3 whitespace-pre-line text-gray-600"><?php echo e($section['content']); ?></p>
                                        <?php endif; ?>
                                        <?php if(!empty($section['links'])): ?>
                                            <ul class="text-[13px] space-y-2">
                                                <?php $__currentLoopData = $section['links']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li><a href="<?php echo e($link['url'] ?? '#'); ?>" class="text-gray-500 hover:text-red-700 transition-colors"><?php echo e($link['label']); ?></a></li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <button @click="activeSection = (activeSection === 'gov' ? null : 'gov')" 
                                        class="w-full py-4 md:py-0 flex justify-between items-center md:block text-left outline-none">
                                    <h2 class="font-bold text-[11px] md:text-sm uppercase tracking-wider text-gray-800">About GOVPH</h2>
                                    <i class="bi bi-chevron-down md:hidden transition-transform" :class="activeSection === 'gov' ? 'rotate-180' : ''"></i>
                                </button>
                                <div class="overflow-hidden transition-all md:max-h-none" :class="activeSection === 'gov' ? 'max-h-40 pb-4' : 'max-h-0 md:mt-4'">
                                    <ul class="text-[13px] space-y-2">
                                        <li><a href="https://www.gov.ph" target="_blank" class="text-gray-500 hover:text-red-700 transition-colors">GOV.PH</a></li>
                                        <li><a href="#" class="text-gray-500 hover:text-red-700 transition-colors">Open Data Portal</a></li>
                                        <li><a href="#" class="text-gray-500 hover:text-red-700 transition-colors">Official Gazette</a></li>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>

                        
                        <div class="border-b border-gray-200 md:border-none">
                            <button @click="activeSection = (activeSection === 'contact' ? null : 'contact')" 
                                    class="w-full py-4 md:py-0 flex justify-between items-center md:block text-left outline-none">
                                <h2 class="font-bold text-[11px] md:text-sm uppercase tracking-wider text-gray-800">Contact Us</h2>
                                <i class="bi bi-chevron-down md:hidden transition-transform" :class="activeSection === 'contact' ? 'rotate-180' : ''"></i>
                            </button>
                            <div class="overflow-hidden transition-all md:max-h-none" :class="activeSection === 'contact' ? 'max-h-80 pb-4' : 'max-h-0 md:mt-4'">
                                <div class="text-[13px] space-y-4 text-gray-600">
                                    <?php if(!empty($site_settings->address)): ?>
                                        <div><strong>Address:</strong><br>
                                            <?php $__currentLoopData = $site_settings->address; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <span class="block"><?php echo e($address); ?></span> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(!empty($site_settings->contact_email)): ?>
                                        <div><strong>Email:</strong><br>
                                            <?php $__currentLoopData = $site_settings->contact_email; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $email): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
                                                <a href="mailto:<?php echo e($email); ?>" class="block hover:text-red-700 transition-colors"><?php echo e($email); ?></a> 
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-span-1 lg:w-auto flex justify-center lg:justify-end flex-shrink-0 order-3 lg:order-3">
                        <?php $footerRightLogos = isset($site_logos) ? $site_logos->where('position', 'footer_right') : collect(); ?>
                        <?php $__empty_1 = true; $__currentLoopData = $footerRightLogos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $logo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <img src="<?php echo e(asset('storage/' . $logo->image_path)); ?>" alt="<?php echo e($logo->name); ?>" class="w-[100px] md:w-[150px] h-auto object-contain">
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <img src="<?php echo e(asset('images/foi.png')); ?>" alt="FOI Logo" class="w-[100px] md:w-[150px] h-auto object-contain">
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="container mx-auto px-4 mt-10 pt-6 border-t border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest text-center md:text-left">
                        &copy; 2026 Department of Education - Zamboanga City Division
                    </p>
                    <a href="<?php echo e(route('login')); ?>" class="text-gray-400 hover:text-[#a52a2a] transition-colors flex items-center gap-2 text-[10px] font-black uppercase tracking-widest focus:outline-none no-underline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Portal Access
                    </a>
                </div>
            </div>
        </footer>

    </div>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        document.addEventListener('submit', function(e) {
            if (!e.target.classList.contains('search-form')) {
                const loader = document.getElementById('globalLoader');
                if (loader) loader.classList.remove('hidden');
            }
        });

        window.showLoader = function() {
            document.getElementById('globalLoader').classList.remove('hidden');
        };
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html><?php /**PATH C:\xampp\htdocs\deped\example-app\resources\views/layouts/admin.blade.php ENDPATH**/ ?>