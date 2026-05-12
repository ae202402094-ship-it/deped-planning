<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-6 py-10 font-sans leading-relaxed">
    
    <div class="flex justify-between items-stretch bg-white border-2 border-black mb-10 shadow-[6px_6px_0px_0px_rgba(165,42,42,1)]">
        <div class="flex items-center">
            <div class="bg-[#a52a2a] text-white px-8 py-5 text-sm font-black uppercase tracking-normal border-r-2 border-black">
                Registry Synchronization Preview
            </div>
            <h1 class="px-8 text-base font-black text-black uppercase tracking-wide border-none">
                Batch Audit: <?php echo e($formattedData->total()); ?> Entities
            </h1>
        </div>
        <div class="flex border-l-2 border-black">
            <a href="<?php echo e(route('admin.schools')); ?>" class="px-8 py-5 text-xs font-black uppercase text-slate-500 hover:text-red-600 transition-colors bg-slate-50 flex items-center border-none">
                Abort Protocol
            </a>
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10 text-center">
        <div class="border-2 border-black p-5 bg-white shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 border-none">New Entries</p>
            <p class="text-3xl font-black text-blue-600 border-none"><?php echo e($newCount); ?></p>
        </div>
        <div class="border-2 border-black p-5 bg-white shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 border-none">Record Updates</p>
            <p class="text-3xl font-black text-emerald-600 border-none"><?php echo e($updateCount); ?></p>
        </div>
        <div class="border-2 border-black p-5 bg-white shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 border-none">Ignored Duplicates</p>
            <p class="text-3xl font-black text-red-600 border-none"><?php echo e($conflictCount); ?></p>
        </div>
        <div class="border-2 border-black p-5 bg-slate-900 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 border-none text-white/50">Batch Total</p>
            <p class="text-3xl font-black text-white border-none"><?php echo e($formattedData->total()); ?></p>
        </div>
    </div>

    
    <div class="overflow-visible border-2 border-black bg-white shadow-2xl mb-6">
        <table class="w-full text-left border-collapse table-auto border-none">
            <thead>
                <tr class="bg-black text-white border-none">
                    <th class="p-4 text-xs font-black uppercase tracking-widest border-r border-white/20">Identification</th>
                    <th class="p-4 text-xs font-black uppercase tracking-widest border-r border-white/20">Inventory Matrix</th>
                    <th class="p-4 text-xs font-black uppercase tracking-widest border-r border-white/20">Utility Audit</th>
                    <th class="p-4 text-xs font-black uppercase tracking-widest border-r border-white/20">Deficit Audit</th>
                    <th class="p-4 text-xs font-black uppercase tracking-widest border-none">Technical Remarks</th>
                </tr>
            </thead>
            <tbody class="divide-y-2 divide-black border-none">
                <?php $__currentLoopData = $formattedData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $isConflict = $row['status'] === 'conflict';
                    $hasChanges = !empty($row['changes']);
                    $rowBg = $isConflict ? 'bg-red-50/70' : 'bg-white';
                ?>
                <tr class="hover:bg-slate-50 transition-colors <?php echo e($rowBg); ?> border-none">
                    
                    <td class="p-4 border-r border-black/10 align-top border-none">
                        <div class="mb-2 border-none">
                            <?php if($row['status'] == 'new'): ?>
                                <span class="text-[9px] bg-blue-600 text-white px-2 py-1 font-black uppercase rounded-sm border-none">New Entry</span>
                            <?php elseif($isConflict): ?>
                                <span class="text-[9px] bg-red-600 text-white px-2 py-1 font-black uppercase rounded-sm border-none">Ignored Duplicate</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-sm font-black text-slate-900 uppercase leading-tight border-none <?php echo e(isset($row['changes']['name']) ? 'bg-emerald-200 px-1 rounded' : ''); ?>">
                            <?php echo e($row['name']); ?>

                        </p>
                        <p class="text-xs font-mono font-bold text-slate-500 mt-1 italic border-none">ID: <?php echo e($row['school_id']); ?></p>
                    </td>
                    
                    
                    <td class="p-4 border-r border-black/10 align-top border-none">
                        <div class="grid grid-cols-1 gap-y-1 font-mono text-xs border-none">
                            <?php $__currentLoopData = ['no_of_teachers' => 'Teachers', 'no_of_enrollees' => 'Enrollees', 'no_of_classrooms' => 'Rooms', 'no_of_chairs' => 'Chairs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex justify-between border-b border-black/5 border-none <?php echo e(isset($row['changes'][$key]) ? 'bg-emerald-200 font-black px-1 rounded' : ''); ?>">
                                <span class="uppercase opacity-60 border-none"><?php echo e($label); ?>:</span>
                                <span class="border-none text-slate-900"><?php echo e($row[$key]); ?></span>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </td>

                    
                    <td class="p-4 border-r border-black/10 align-top border-none">
                        <div class="space-y-2 border-none">
                            <?php $__currentLoopData = [['key' => 'with_electricity', 'label' => 'Electricity'], ['key' => 'with_potable_water', 'label' => 'Water'], ['key' => 'with_internet', 'label' => 'Web']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $util): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center gap-3 border-none <?php echo e(isset($row['changes'][$util['key']]) ? 'bg-emerald-200 px-1 rounded' : ''); ?>">
                                <div class="w-3 h-3 rounded-full border-none <?php echo e($row[$util['key']] ? 'bg-emerald-500' : 'bg-red-500'); ?>"></div>
                                <span class="text-xs font-black uppercase border-none text-slate-800"><?php echo e($util['label']); ?></span>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </td>

                    
                    <td class="p-4 border-r border-black/10 align-top font-mono text-xs border-none">
                        <div class="space-y-1 border-none text-slate-800">
                            <?php $__currentLoopData = ['classroom_shortage' => 'ROOM_S', 'chair_shortage' => 'CHAIR_S', 'toilet_shortage' => 'TOILET_S']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <p class="border-none <?php echo e(isset($row['changes'][$key]) ? 'bg-emerald-200 font-black px-1 rounded' : ''); ?>"><?php echo e($label); ?>: <?php echo e($row[$key]); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </td>

                    
                    <td class="p-4 align-top border-none">
                        <div class="text-[10px] font-bold uppercase border-none">
                            <?php if($isConflict): ?>
                                <div class="text-red-700 font-black border-none">
                                    SYSTEM OVERRIDE: DUPLICATE RECORD DETECTED. THIS ENTRY WILL BE EXCLUDED FROM THE SYNC.
                                </div>
                            <?php else: ?>
                                <span class="text-slate-500 italic border-none"><?php echo e($row['hazards'] ?: 'No physical hazards documented.'); ?></span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    
    <div class="mt-6 border-none">
        <?php echo e($formattedData->links()); ?>

    </div>

    
    <div class="mt-12 flex flex-col md:flex-row justify-between items-center gap-8 border-t-4 border-black pt-10 border-none">
        <div class="flex flex-col border-none">
            <div class="flex items-center gap-4 border-none">
                <div class="w-3 h-3 bg-[#a52a2a] animate-pulse rounded-full border-none"></div>
                <p class="text-xs font-black text-slate-800 uppercase tracking-widest border-none">Registry Write Protocol</p>
            </div>
            <p class="text-[10px] text-slate-400 mt-1 font-bold italic border-none">Note: Faded red rows indicate internal CSV duplication and will be ignored by the system[cite: 1].</p>
        </div>
        
        <form action="<?php echo e(route('schools.confirm_import')); ?>" method="POST" class="w-full md:w-auto border-none">
            <?php echo csrf_field(); ?>
            <button type="submit" class="w-full md:w-auto bg-[#a52a2a] text-white px-20 py-7 text-sm font-black uppercase tracking-[0.3em] hover:bg-black transition-all shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] active:translate-x-1 active:translate-y-1 border-none">
                Execute Batch Synchronization
            </button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\deped\example-app\resources\views/admin/preview_import.blade.php ENDPATH**/ ?>