<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="UTF-8">
    <title>Petrol Bill - <?php echo e($bill->month->month ?? 'N/A'); ?> <?php echo e($bill->month->year ?? ''); ?></title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }

        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
            background-color: white;
        }

        .bill-header {
            text-align: center;
            margin-bottom: 10px;
        }

        .bill-header h2 {
            font-size: 15px;
        }

        .bill-box {
            width: 40%;
        }

        .bill-info {
            margin-bottom: 10px;
        }

        .bill-info-item strong {
            font-size: 12px;
            color: #333;
        }

        .bill-info-item span {
            font-size: 12px;
            color: #333;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 5px;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #e0e0e0;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #f5f7fa;
            color: #333;
            font-weight: 600;
        }

        tfoot td {
            font-weight: bold;
        }

        .bill-summary {
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="bill-header">
        <h2>Petrol Bill details for the month of <?php echo e($bill->month->month ?? 'N/A'); ?> <?php echo e($bill->month->year ?? ''); ?></h2>
    </div>

    <div class="bill-box">
        <div class="bill-info">
            <div class="bill-info-item">
                <strong>Name:</strong>
                <span><?php echo e($bill->employee->name ?? 'N/A'); ?>(<?php echo e($bill->employee->vehicle_no ?? ''); ?>)</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>SN</th>
                    <th>Date</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $bill->date; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td><?php echo e($d); ?></td>
                        <td><?php echo e($bill->quantity[$index] ?? ''); ?></td>
                        <td><?php echo e($bill->rate[$index] ?? ''); ?></td>
                        <td><?php echo e(number_format($bill->amount[$index] ?? 0, 2)); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5">No data available</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="text-align: right;">Total</td>
                    <td><?php echo e(number_format($bill->total_quantity, 2)); ?></td>
                    <td></td>
                    <td><?php echo e(number_format($bill->total_amount, 2)); ?></td>
                </tr>
            </tfoot>
        </table>

        <?php if($bill->remarks): ?>
            <p style="margin-top: 10px;"><strong>कैफियत:</strong> <?php echo e($bill->remarks); ?></p>
        <?php endif; ?>

        <div class="bill-summary">
            <p><strong>Signature:</strong> ______________________</p>
        </div>
    </div>
</body>
</html><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/petrol/bills/pdf.blade.php ENDPATH**/ ?>