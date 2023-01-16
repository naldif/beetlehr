<?php

namespace App\Actions\Utility\Approval;

use Carbon\Carbon;
use App\Models\Approval;
use App\Models\Employee;
use App\Models\ApprovalStep;


class AssignApprovalData
{
    public function handle($listApprovers, $data, $meta_data, $reference_id)
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        // Insert Multi Approval Data
        $approvalData = [
            'type' => $data->type,
            'status' => 'awaiting',
            'reference_id' => $reference_id,
            'requester_id' => $data->requester->id,
            'requester' => [
                'name' => $data->requester->user_detail->name,
                'branch' => $data->requester->branch_detail->name,
                'designation' => $data->requester->designation_detail->name,
            ],
            'meta_data' => $meta_data,
            'requested_at' => $timestamp,
        ];
        $approval = Approval::create($approvalData);

        // Insert Approver Data
        $approverData = [];
        $level = 1;
        foreach ($listApprovers as $approver) {
            if (count($approverData) == 0) {
                if ($approver->approver_type === 'reports_to') {
                    if ($data->requester->manager) {
                        array_push($approverData, [
                            'approval_id' => $approval->id,
                            'approver_id' => $data->requester->manager_id,
                            'approver' => json_encode([
                                'name' => $data->requester->manager->user_detail->name,
                                'user_id' => $data->requester->manager->user_id,
                                'timezone' => $data->requester->manager->branch_detail->timezone,
                                'branch' => $data->requester->manager->branch_detail->name,
                                'designation' => $data->requester->manager->designation_detail->name
                            ]),
                            'level' => $level,
                            'status' => 'awaiting',
                            'notified_at' => $timestamp,
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp
                        ]);
                    }
                } else {
                    array_push($approverData, [
                        'approval_id' => $approval->id,
                        'approver_id' => $approver->employee_id,
                        'approver' => json_encode([
                            'name' => $approver->employee_detail->user_detail->name,
                            'user_id' => $approver->employee_detail->user_id,
                            'timezone' => $approver->employee_detail->branch_detail->timezone,
                            'placement' => $approver->employee_detail->branch_detail->name,
                            'designation' => $approver->employee_detail->designation_detail->name
                        ]),
                        'level' => $level,
                        'status' => 'awaiting',
                        'notified_at' => $timestamp,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp
                    ]);
                }
            } else {
                if ($approver->approver_type === 'reports_to') {
                    $latestApprover = end($approverData);
                    $designatedApprover = Employee::find($latestApprover['approver_id']);
                    if ($designatedApprover->manager) {
                        array_push($approverData, [
                            'approval_id' => $approval->id,
                            'approver_id' => $designatedApprover->manager_id,
                            'approver' => json_encode([
                                'name' => $designatedApprover->manager->user_detail->name,
                                'user_id' => $designatedApprover->manager->user_id,
                                'timezone' => $designatedApprover->manager->branch_detail->timezone,
                                'branch' => $designatedApprover->manager->branch_detail->name,
                                'designation' => $designatedApprover->manager->designation_detail->name
                            ]),
                            'level' => $level,
                            'status' => 'pending',
                            'notified_at' => null,
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp
                        ]);
                    }
                } else {
                    $designatedApprover = Employee::find($approver->employee_id);
                    array_push($approverData, [
                        'approval_id' => $approval->id,
                        'approver_id' => $approver->employee_id,
                        'approver' => json_encode([
                            'name' => $designatedApprover->user_detail->name,
                            'user_id' => $designatedApprover->user_id,
                            'timezone' => $designatedApprover->branch_detail->timezone,
                            'branch' => $designatedApprover->branch_detail->name,
                            'designation' => $designatedApprover->designation_detail->name
                        ]),
                        'level' => $level,
                        'status' => 'pending',
                        'notified_at' => null,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp
                    ]);
                }
            }

            $level++;
        }

        ApprovalStep::insert($approverData);
    }
}
