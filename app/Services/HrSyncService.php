<?php

namespace App\Services;

use App\Http\Controllers\HrController;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Support\Facades\Log;

class HrSyncService
{
    protected $provisioningService;

    protected $summary = [
        'departments_synced' => 0,
        'positions_synced'   => 0,
        'employees_created'  => 0,
        'employees_updated'  => 0,
        'users_created'      => 0,
        'errors'             => [],
    ];

    public function __construct(UserProvisioningService $provisioningService)
    {
        $this->provisioningService = $provisioningService;
    }

    public function runFullSync(): array
    {
        // $this->syncDepartments();
        // $this->syncPositions();
        $this->syncEmployees();

        return $this->summary;
    }

    protected function syncDepartments(): void
    {
        try {
            $list = HrController::getDepartmentList();
        //    dd($list);

            foreach ($list->data ?? [] as $dept) {
                // Note: API ko actual field naam confirm huनुparcha (haal 'name' assume gareko)
                $name = $dept->name ?? $dept->departmentName ?? null;
                if (!$name) continue;

                Department::firstOrCreate(['name' => trim($name)]);
                $this->summary['departments_synced']++;
            }
        } catch (\Exception $e) {
            $this->summary['errors'][] = 'Department sync error: ' . $e->getMessage();
            Log::error('HR Sync - Department error: ' . $e->getMessage());
        }
    }

    protected function syncPositions(): void
    {
        try {
            $list = HrController::getDesignationList();
            //  dd($list);
            foreach ($list->data ?? [] as $designation) {
                // Note: API ko actual field naam confirm huनुparcha (haal 'name' assume gareko)
                $name = $designation->name ?? $designation->designation_name ?? null;
                if (!$name) continue;

                Position::firstOrCreate(
                    ['name' => trim($name)],
                    ['ot_rate' => 0, 'level' => 0, 'is_active' => true]
                );
                $this->summary['positions_synced']++;
            }
        } catch (\Exception $e) {
            $this->summary['errors'][] = 'Position sync error: ' . $e->getMessage();
            Log::error('HR Sync - Position error: ' . $e->getMessage());
        }
    }

    protected function syncEmployees(): void
    {
        try {
            $list = HrController::getEmployeeList();
            //  dump($list);
            dd($list);
            foreach ($list->data ?? [] as $emp) {
                try {
                    $this->syncOneEmployee($emp);
                } catch (\Exception $e) {
                    $this->summary['errors'][] = 'Employee (' . ($emp->employee_code ?? 'unknown') . ') error: ' . $e->getMessage();
                    Log::error('HR Sync - Employee error: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $this->summary['errors'][] = 'Employee list fetch error: ' . $e->getMessage();
            Log::error('HR Sync - Employee list error: ' . $e->getMessage());
        }
    }

    protected function syncOneEmployee($emp): void
    {
        // Note: API ko actual field naam sabai confirm huनुparcha, haal reasonable assume gareko
        $status = $emp->status ?? 1;
        if ((int) $status !== 1) {
            return; // Active bahekaलाई skip
        }

        $employeeCode = trim($emp->employee_code ?? $emp->emp_code ?? '');
        if (empty($employeeCode)) {
            return;
        }

        $name       = $emp->name ?? $emp->full_name ?? null;
        $email      = $emp->email ?? null;
        $mobile     = $emp->mobile ?? $emp->phone ?? null;
        $deptName   = $emp->department ?? $emp->department_name ?? null;
        $posName    = $emp->designation ?? $emp->position ?? $emp->designation_name ?? null;

        $departmentId = $deptName ? Department::firstOrCreate(['name' => trim($deptName)])->id : null;
        $positionId   = $posName ? Position::firstOrCreate(
                            ['name' => trim($posName)],
                            ['ot_rate' => 0, 'level' => 0, 'is_active' => true]
                        )->id : null;

        $existing = Employee::where('employee_code', $employeeCode)->first();

        if ($existing) {
            // Existing bhaye: naam BAHEK, baaki sabai update
            $existing->update([
                'email'         => $email ?? $existing->email,
                'mobile'        => $mobile ?? $existing->mobile,
                'department'    => $deptName ?? $existing->department,
                'position_id'   => $positionId ?? $existing->position_id,
                'is_active'     => true,
                'last_synced_at'=> now(),
            ]);
            $this->summary['employees_updated']++;
            $employee = $existing;
        } else {
            // Navaya employee create
            $employee = Employee::create([
                'employee_code'  => $employeeCode,
                'name'           => $name,
                'email'          => $email,
                'mobile'         => $mobile,
                'department'     => $deptName,
                'position_id'    => $positionId,
                'is_active'      => true,
                'last_synced_at' => now(),
            ]);
            $this->summary['employees_created']++;
        }

        // User auto-provision (existing bhaye skip हुन्छ, provisionFor() भित्रै त्यो check छ)
        $user = $this->provisioningService->provisionFor($employee);
        if ($user && $user->wasRecentlyCreated) {
            $this->summary['users_created']++;
        }
    }
}