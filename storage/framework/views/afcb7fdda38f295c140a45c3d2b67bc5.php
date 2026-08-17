<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #222; }
        h2 { text-align: center; margin-bottom: 2px; }
        h4 { text-align: center; margin-top: 0; margin-bottom: 20px; font-weight: normal; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .info-table td { border: none; padding: 3px 8px; }
        .total-row { font-weight: bold; background-color: #f7f7f7; }
    </style>
</head>
<body>
    <h2>Petrol Bill</h2>
    <h4>Month: <?php echo e($bill->month->month ?? ''); ?> - <?php echo e($bill->month->year ?? ''); ?></h4>

    <table class="info-table">
        <tr>
            <td><strong>कर्मचारीको नाम:</strong> <?php echo e($bill->employee->name ?? 'N/A'); ?></td>
            <td><strong>पद:</strong> <?php echo e($bill->employee->position->name ?? 'N/A'); ?></td>
        </tr>
        <tr>
            <td><strong>Vehicle No:</strong> <?php echo e($bill->employee->vehicle_no ?? '-'); ?></td>
            <td><strong>Quantity Limit:</strong> <?php echo e($bill->employee->petrol_quantity_limit ?? '-'); ?> लिटर</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>क्र.सं.</th>
                <th>मिति</th>
                <th>परिमाण (Litre)</th>
                <th>दर</th>
                <th>रकम</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $bill->date; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="text-center"><?php echo e($i + 1); ?></td>
                <td><?php echo e($d); ?></td>
                <td class="text-right"><?php echo e(number_format($bill->quantity[$i] ?? 0, 2)); ?></td>
                <td class="text-right"><?php echo e(number_format($bill->rate[$i] ?? 0, 2)); ?></td>
                <td class="text-right"><?php echo e(number_format($bill->amount[$i] ?? 0, 2)); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="total-row">
                <td colspan="2" class="text-right">जम्मा</td>
                <td class="text-right"><?php echo e(number_format($bill->total_quantity, 2)); ?></td>
                <td></td>
                <td class="text-right">रु <?php echo e(number_format($bill->total_amount, 2)); ?></td>
            </tr>
        </tbody>
    </table>

    <?php if($bill->remarks): ?>
        <p><strong>कैफियत:</strong> <?php echo e($bill->remarks); ?></p>
    <?php endif; ?>

    <table class="info-table" style="margin-top: 40px;">
        <tr>
            <td style="width: 50%;">दस्तखत (कर्मचारी): ___________________</td>
            <td style="width: 50%;">दस्तखत (स्वीकृत): ___________________</td>
        </tr>
    </table>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/petrol/bills/pdf.blade.php ENDPATH**/ ?>