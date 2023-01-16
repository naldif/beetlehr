<?php

namespace App\Actions\Utility\Payroll;


class ComponentMetaFormat
{
    public function handle($meta, $type)
    {
        switch ($type) {
            case 'deduction_late':
                return $this->formatDeductionLateMeta($meta);
                break;

            case 'earning_overtime':
                return $this->formatEarningOvertimeMeta($meta);
                break;

            default:
                return null;
                break;
        }
    }

    public function formatDeductionLateMeta($data)
    {
        return [
            'action' => $data['action'],
            'late_tolerance' => array_key_exists('late_tolerance', $data) ?  $data['late_tolerance'] : null,
        ];
    }

    public function formatEarningOvertimeMeta($data)
    {
        return [
            'action' => $data['action']
        ];
    }
}
