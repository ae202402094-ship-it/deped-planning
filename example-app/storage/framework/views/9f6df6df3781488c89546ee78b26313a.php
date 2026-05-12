<?php $__env->startSection('content'); ?>
<?php
    $isEmbed = session('is_embedded', false) || request()->query('embed') === 'true';

    $actualTeacherRatio = $school->no_of_teachers > 0 ? round($school->no_of_enrollees / $school->no_of_teachers) : '0';
    $actualClassroomRatio = $school->no_of_classrooms > 0 ? round($school->no_of_enrollees / $school->no_of_classrooms) : '0';
    $actualChairRatio = $school->no_of_chairs > 0 ? round($school->no_of_enrollees / $school->no_of_chairs, 1) : '0';
    $actualToiletRatio = $school->no_of_toilets > 0 ? round($school->no_of_enrollees / $school->no_of_toilets) : '0';
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/lucide@latest"></script>

<style>
    .map-pulse { border-radius: 50%; height: 20px; width: 20px; position: absolute; background: rgba(165, 42, 42, 0.4); animation: pulsate 2s ease-out infinite; opacity: 0; }
    @keyframes pulsate { 0% { transform: scale(0.1, 0.1); opacity: 0; } 50% { opacity: 1.0; } 100% { transform: scale(1.2, 1.2); opacity: 0; } }
    .leaflet-popup-content-wrapper { border-radius: 12px; padding: 5px; }
    .no-print { @media print { display: none !important; } }
</style>

<div class="min-h-screen bg-[#f8fafc] <?php echo e($isEmbed ? 'py-4' : 'py-12'); ?> px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">
        
        
        <header class="relative bg-white p-8 rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="absolute top-4 right-8 text-right flex items-center gap-2 group">
                <span class="text-[10px] font-black uppercase tracking-[0.15em] text-slate-400">
                    as of [<?php echo e($school->updated_at->format('F d, Y')); ?>]
                </span>
                <div class="relative cursor-help">
                    <i data-lucide="help-circle" class="w-3 h-3 text-slate-300"></i>
                    <div class="absolute right-0 bottom-full mb-2 w-48 p-2 bg-slate-900 text-white text-[9px] rounded shadow-xl opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-50 normal-case tracking-normal">
                        This reflects the exact date the school registry was last updated by the Division Office.
                    </div>
                </div>
            </div>

            <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mt-2">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-1 bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest rounded-sm">Institutional Profile</span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em]">Registry Cycle 2026</span>
                    </div>
                    <h1 class="text-4xl font-black text-slate-900 tracking-tight"><?php echo e($school->name); ?></h1>
                    
                    
                    <div class="flex flex-wrap items-center gap-2 mt-4 pt-2">
                        <?php if(($school->sector ?? 'Public') === 'Private'): ?>
                            <span class="px-3 py-1 bg-purple-100 text-purple-700 border border-purple-200 text-[10px] font-black uppercase tracking-widest rounded-md">Private</span>
                        <?php else: ?>
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 border border-blue-200 text-[10px] font-black uppercase tracking-widest rounded-md">Public</span>
                        <?php endif; ?>
                        
                        <span class="px-3 py-1 bg-slate-100 text-slate-700 border border-slate-200 text-[10px] font-black uppercase tracking-widest rounded-md">
                            <?php echo e($school->school_level ?? 'Unclassified Level'); ?>

                        </span>
                        
                        <span class="px-3 py-1 bg-slate-100 text-slate-700 border border-slate-200 text-[10px] font-black uppercase tracking-widest rounded-md flex items-center gap-1">
                            <i data-lucide="map-pin" class="w-3 h-3"></i> <?php echo e($school->district ?? 'Unassigned District'); ?>

                        </span>
                    </div>
                </div>

                <?php if(!$isEmbed): ?>
                <div class="flex flex-col items-end gap-3 no-print mt-4 md:mt-0">
                    <button onclick="window.print()" class="bg-[#a52a2a] text-white px-6 py-3 rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg hover:bg-black transition-all flex items-center gap-2">
                        <i data-lucide="printer" class="w-4 h-4"></i> Print Profile
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </header>

        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
            <?php $__currentLoopData = [
                ['label' => 'Total Enrollees', 'value' => $school->no_of_enrollees, 'icon' => 'graduation-cap', 'tip' => 'The total number of students currently officially registered in this school.'],
                ['label' => 'Total Teachers', 'value' => $school->no_of_teachers, 'icon' => 'users-round', 'tip' => 'The count of active teaching personnel assigned to this facility.'],
                ['label' => 'Total Classrooms', 'value' => $school->no_of_classrooms, 'icon' => 'door-open', 'tip' => 'Number of rooms used for daily academic instruction.'],
                ['label' => 'Total Toilets', 'value' => $school->no_of_toilets, 'icon' => 'toilet', 'tip' => 'Total sanitary cubicles available for student and staff use.'],
                ['label' => 'Total Chairs', 'value' => $school->no_of_chairs, 'icon' => 'armchair', 'tip' => 'Standard seating units available for the student population.'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metric): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="group relative bg-white pt-5 px-4 pb-4 sm:pt-6 sm:px-6 shadow-sm border border-slate-200 rounded-2xl overflow-hidden hover:border-[#a52a2a]/40 transition-all duration-300">
                <dt class="flex justify-between items-start">
                    <div class="absolute rounded-xl p-3 bg-red-50 text-[#a52a2a] group-hover:bg-[#a52a2a] group-hover:text-white transition-colors duration-300">
                        <i data-lucide="<?php echo e($metric['icon']); ?>" class="w-6 h-6"></i>
                    </div>
                    <div class="ml-16 flex items-center gap-1.5">
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest truncate group-hover:text-[#a52a2a]">
                            <?php echo e($metric['label']); ?>

                        </p>
                        <div class="relative inline-block group/tip cursor-help">
                            <i data-lucide="help-circle" class="w-3 h-3 text-slate-300"></i>
                            <div class="absolute left-1/2 bottom-full -translate-x-1/2 mb-2 w-40 p-2 bg-slate-900 text-white text-[9px] font-medium rounded shadow-2xl opacity-0 group-hover/tip:opacity-100 transition-opacity pointer-events-none z-50 normal-case tracking-normal text-center">
                                <?php echo e($metric['tip']); ?>

                            </div>
                        </div>
                    </div>
                </dt>
                <dd class="ml-16 flex items-baseline pb-1 mt-1">
                    <p class="text-3xl font-black text-slate-900 tabular-nums"><?php echo e(number_format($metric['value'])); ?></p>
                </dd>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden h-[630px] flex flex-col">
                    <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-6 bg-[#a52a2a] rounded-full"></div>
                            <h2 class="text-xs font-black text-slate-900 uppercase tracking-widest">Interactive Map</h2>
                            <div class="relative group/maptip cursor-help">
                                <i data-lucide="help-circle" class="w-3 h-3 text-slate-300"></i>
                                <div class="absolute left-0 bottom-full mb-2 w-56 p-2 bg-slate-900 text-white text-[9px] rounded shadow-xl opacity-0 group-hover/maptip:opacity-100 transition-opacity pointer-events-none z-50 normal-case tracking-normal">
                                    Visualizes the exact location. Use 'Street' for navigation or 'Satellite' to see the physical terrain and campus rooflines.
                                </div>
                            </div>
                        </div>
                        <div class="bg-white/90 p-1 rounded-xl border border-slate-200 flex gap-1 no-print">
                            <button id="setVoyager" class="px-3 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-lg bg-[#a52a2a] text-white transition-all">Street</button>
                            <button id="setSatellite" class="px-3 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-slate-100 text-slate-600 transition-all">Satellite</button>
                        </div>
                    </div>
                    <div class="relative flex-1">
                        <div id="schoolMap" class="absolute inset-0 w-full h-full"></div>
                    </div>
                </div>
            </div>

            
            <div class="space-y-6">
                
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-2 border-b border-slate-50 pb-3">
                        <i data-lucide="clipboard-check" class="w-4 h-4 text-[#a52a2a]"></i> Resources Shortage
                        <div class="relative group/shorttip cursor-help ml-auto">
                            <i data-lucide="help-circle" class="w-3 h-3 text-slate-300"></i>
                            <div class="absolute right-0 bottom-full mb-2 w-56 p-2 bg-slate-900 text-white text-[9px] rounded shadow-xl opacity-0 group-hover/shorttip:opacity-100 transition-opacity pointer-events-none z-50 normal-case tracking-normal">
                                Compares student population against available units. Red numbers indicate a critical deficit that needs budget priority.
                            </div>
                        </div>
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead>
                                <tr class="text-left text-[8px] font-black uppercase tracking-widest text-slate-400">
                                    <th class="pb-2 pr-1">Category</th>
                                    <th class="pb-2 px-1 text-center">Ratio</th>
                                    <th class="pb-2 pl-1 text-right">Shortage</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <?php $__currentLoopData = [
                                    ['Teachers', $actualTeacherRatio, $school->teacher_shortage],
                                    ['Classrooms', $actualClassroomRatio, $school->classroom_shortage],
                                    ['Seats', $actualChairRatio, $school->chair_shortage],
                                    ['Toilets', $actualToiletRatio, $school->toilet_shortage]
                                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="py-3 pr-1 text-[10px] font-bold text-slate-700"><?php echo e($row[0]); ?></td>
                                    <td class="py-3 px-1 text-center text-[10px] font-black text-slate-500">1:<?php echo e($row[1]); ?></td>
                                    <td class="py-3 pl-1 text-right font-black text-xs <?php echo e(($row[2] > 0) ? 'text-red-600' : 'text-emerald-500'); ?>">
                                        <?php echo e(number_format($row[2] ?? 0)); ?>

                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-2 border-b border-slate-50 pb-3">
                        <i data-lucide="plug-zap" class="w-4 h-4 text-amber-500"></i> Utilities
                        <div class="relative group/utiltip cursor-help ml-auto">
                            <i data-lucide="help-circle" class="w-3 h-3 text-slate-300"></i>
                            <div class="absolute right-0 bottom-full mb-2 w-56 p-2 bg-slate-900 text-white text-[9px] rounded shadow-xl opacity-0 group-hover/utiltip:opacity-100 transition-opacity pointer-events-none z-50 normal-case tracking-normal">
                                Shows the operational status of basic facilities. NO indicates a gap in infrastructure development.
                            </div>
                        </div>
                    </h3>
                    <div class="space-y-3">
                        <?php $__currentLoopData = [
                            ['Power', $school->with_electricity, in_array($school->with_electricity, ['Grid Connection', 'Hybrid'])],
                            ['Water', $school->with_potable_water ? 'YES' : 'NO', $school->with_potable_water],
                            ['Internet', $school->with_internet ? 'YES' : 'NO', $school->with_internet]
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $util): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center justify-between p-3 rounded-xl border <?php echo e($util[2] ? 'bg-emerald-50/30 border-emerald-100' : 'bg-red-50/30 border-red-100'); ?>">
                            <span class="text-xs font-bold text-slate-600 uppercase tracking-wider"><?php echo e($util[0]); ?></span>
                            <span class="text-[10px] font-black uppercase <?php echo e($util[2] ? 'text-emerald-700' : 'text-red-700'); ?>">
                                <?php echo e($util[1]); ?>

                            </span>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-2 border-b border-slate-50 pb-3">
                        <i data-lucide="shield-alert" class="w-4 h-4 text-[#a52a2a]"></i> Risk Profile
                        <div class="relative group/risktip cursor-help ml-auto">
                            <i data-lucide="help-circle" class="w-3 h-3 text-slate-300"></i>
                            <div class="absolute right-0 bottom-full mb-2 w-56 p-2 bg-slate-900 text-white text-[9px] rounded shadow-xl opacity-0 group-hover/risktip:opacity-100 transition-opacity pointer-events-none z-50 normal-case tracking-normal">
                                Lists environmental threats the school is exposed to based on divisional audit.
                            </div>
                        </div>
                    </h3>
                    <?php
                        $rawHazards = is_array($school->hazard_type) ? $school->hazard_type : (json_decode($school->hazard_type, true) ?? [$school->hazard_type]);
                        $activeHazards = [];
                        if (is_array($rawHazards)) {
                            foreach($rawHazards as $h) {
                                $clean = trim(str_replace(['"', '[', ']'], '', $h));
                                if (!empty($clean) && strtolower($clean) !== 'none' && strtolower($clean) !== 'others') {
                                    $activeHazards[] = $clean;
                                }
                            }
                        }
                    ?>
                    <div class="p-4 bg-slate-50 rounded-xl border-l-4 <?php echo e(count($activeHazards) > 0 ? 'border-[#a52a2a]' : 'border-emerald-500'); ?>">
                        <?php if(count($activeHazards) > 0): ?>
                            <div class="flex flex-wrap gap-2">
                                <?php $__currentLoopData = $activeHazards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hazard): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="bg-red-100 text-red-800 text-[8px] font-black px-2 py-1 rounded uppercase tracking-widest"><?php echo e($hazard); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <div class="text-[9px] font-black text-emerald-700 uppercase tracking-widest flex items-center gap-1.5">
                                <i data-lucide="check-circle-2" class="w-4 h-4"></i> No Critical Hazards
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') lucide.createIcons();

        const lat = <?php echo e($school->latitude); ?>;
        const lng = <?php echo e($school->longitude); ?>;
        const voyager = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { attribution: 'CARTO' });
        const satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { attribution: 'Esri' });
        const map = L.map('schoolMap', { scrollWheelZoom: false, layers: [voyager] }).setView([lat, lng], 17);
        
        document.getElementById('setVoyager').addEventListener('click', function() {
            map.removeLayer(satellite); map.addLayer(voyager);
            this.className = 'px-3 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-lg bg-[#a52a2a] text-white transition-all';
            document.getElementById('setSatellite').className = 'px-3 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-slate-100 text-slate-600 transition-all';
        });

        document.getElementById('setSatellite').addEventListener('click', function() {
            map.removeLayer(voyager); map.addLayer(satellite);
            this.className = 'px-3 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-lg bg-[#a52a2a] text-white transition-all';
            document.getElementById('setVoyager').className = 'px-3 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-slate-100 text-slate-600 transition-all';
        });

        const markerIcon = L.divIcon({
            html: `<div class="relative flex items-center justify-center"><div class="map-pulse"></div><div class="relative w-8 h-8 bg-[#a52a2a] border-4 border-white rounded-full shadow-2xl flex items-center justify-center"><div class="w-1.5 h-1.5 bg-white rounded-full"></div></div></div>`,
            className: '', iconSize: [48, 48], iconAnchor: [24, 24]
        });

        L.marker([lat, lng], { icon: markerIcon }).addTo(map).bindPopup(`
            <div class="font-sans min-w-[150px]">
                <strong class="text-slate-800 block mb-1 text-xs uppercase"><?php echo e($school->name); ?></strong>
            </div>
        `).openPopup();
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\deped\example-app\resources\views/user_view.blade.php ENDPATH**/ ?>